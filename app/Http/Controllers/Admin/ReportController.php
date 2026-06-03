<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ── Report filter page ───────────────────────────────────────────
    public function index()
    {
        $cashiers = User::whereNotNull('pos_role')->orWhere('utype', 'ADM')
            ->orderBy('name')->get(['id', 'name']);
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('admin.report.index', compact('cashiers', 'branches'));
    }

    // ── Generate PDF ─────────────────────────────────────────────────
    public function pdf(Request $request)
    {
        $request->validate([
            'date_from'  => 'required|date',
            'date_to'    => 'required|date|after_or_equal:date_from',
            'cashier_id' => 'nullable|exists:users,id',
            'branch_id'  => 'nullable|exists:branches,id',
        ]);

        $from      = Carbon::parse($request->date_from)->startOfDay();
        $to        = Carbon::parse($request->date_to)->endOfDay();
        $cashierId = $request->cashier_id;
        $branchId  = $request->branch_id;

        // ── Base order query ─────────────────────────────────────────
        $orderQuery = Order::with(['orderItems.product', 'orderItems.variant', 'cashier', 'branch', 'posPayment'])
            ->whereBetween('created_at', [$from, $to])
            ->where('is_hold', false);

        if ($cashierId) {
            $orderQuery->where('cashier_id', $cashierId);
        }
        if ($branchId) {
            $orderQuery->where('branch_id', $branchId);
        }

        $orders = $orderQuery->orderBy('created_at')->get();

        // ── Summary totals ────────────────────────────────────────────
        $totalOrders   = $orders->count();
        $grossSales    = $orders->sum('subtotal');
        $totalDiscount = $orders->sum('discount');
        $totalShipping = $orders->sum('shipping');
        $totalTax      = $orders->sum('tax');
        $netSales      = $orders->sum('total');

        // ── By source ────────────────────────────────────────────────
        $bySource = $orders->groupBy('source')->map(fn ($g) => [
            'count'  => $g->count(),
            'total'  => $g->sum('total'),
        ]);

        // ── By order type ─────────────────────────────────────────────
        $byType = $orders->groupBy(fn ($o) => $o->type ?: 'online')->map(fn ($g) => [
            'count' => $g->count(),
            'total' => $g->sum('total'),
        ]);

        // ── By payment method (POS orders) ────────────────────────────
        $byPayment = $orders->filter(fn ($o) => $o->posPayment)
            ->groupBy(fn ($o) => $o->posPayment->method)
            ->map(fn ($g) => [
                'count' => $g->count(),
                'total' => $g->sum('total'),
            ]);

        // ── By cashier ───────────────────────────────────────────────
        $byCashier = $orders->filter(fn ($o) => $o->cashier_id)
            ->groupBy('cashier_id')
            ->map(fn ($g) => [
                'name'  => $g->first()->cashier?->name ?? 'Unknown',
                'count' => $g->count(),
                'total' => $g->sum('total'),
            ])->values();

        // ── Product sales breakdown ───────────────────────────────────
        $productSales = collect();
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $key = $item->product_id . '-' . ($item->variant_id ?? 0);
                if ($productSales->has($key)) {
                    $existing = $productSales->get($key);
                    $existing['qty']   += $item->quantity;
                    $existing['total'] += $item->price * $item->quantity;
                    $productSales->put($key, $existing);
                } else {
                    $productSales->put($key, [
                        'name'    => $item->product?->name ?? 'Deleted Product',
                        'variant' => $item->variant_label ?? $item->variant?->display_label ?? '',
                        'price'   => $item->price,
                        'qty'     => $item->quantity,
                        'total'   => $item->price * $item->quantity,
                    ]);
                }
            }
        }
        $productSales = $productSales->values()->sortByDesc('total');

        // ── Expenses ─────────────────────────────────────────────────
        $expenseQuery = Expense::whereBetween('expense_date', [
            $from->toDateString(), $to->toDateString()
        ]);
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $expenses      = $expenseQuery->orderBy('expense_date')->get();
        $totalExpenses = $expenses->sum('amount');

        // ── Meta ──────────────────────────────────────────────────────
        $filterCashier = $cashierId ? User::find($cashierId) : null;
        $filterBranch  = $branchId  ? Branch::find($branchId) : null;
        $generatedAt   = now()->format('d M Y, h:i A');
        $logoPath      = public_path('images/logo/logo.png');
        $logoBase64    = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $data = compact(
            'from', 'to', 'orders', 'totalOrders',
            'grossSales', 'totalDiscount', 'totalShipping', 'totalTax', 'netSales',
            'bySource', 'byType', 'byPayment', 'byCashier',
            'productSales', 'expenses', 'totalExpenses',
            'filterCashier', 'filterBranch',
            'generatedAt', 'logoBase64'
        );

        $pdf = Pdf::loadView('admin.report.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'     => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = 'report-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.pdf';

        return $pdf->stream($filename);
    }

    // ── Expenses list page ───────────────────────────────────────────
    public function expenses(Request $request)
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        $query = Expense::with(['branch', 'recorder'])->orderByDesc('expense_date');

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $expenses    = $query->paginate(25)->withQueryString();
        $totalAmount = $query->sum('amount');

        $categories = ['General', 'Transport', 'Packaging', 'Salaries', 'Utilities', 'Rent', 'Marketing', 'Maintenance', 'Other'];

        return view('admin.expenses.index', compact('expenses', 'branches', 'totalAmount', 'categories'));
    }

    // ── Store expense ─────────────────────────────────────────────────
    public function expense_store(Request $request)
    {
        $request->validate([
            'description'  => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'branch_id'    => 'nullable|exists:branches,id',
            'notes'        => 'nullable|string|max:500',
        ]);

        Expense::create([
            'description'  => $request->description,
            'category'     => $request->category,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'branch_id'    => $request->branch_id ?: null,
            'recorded_by'  => Auth::id(),
            'notes'        => $request->notes,
        ]);

        return back()->with('success', 'Expense recorded successfully.');
    }

    // ── Delete expense ────────────────────────────────────────────────
    public function expense_delete(int $id)
    {
        Expense::findOrFail($id)->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
