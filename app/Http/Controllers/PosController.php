<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Services\NotificationService;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\GiftOrder;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\PosHeldOrder;
use App\Models\PosPayment;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class PosController extends Controller
{
    private const CART = 'pos';

    // -------------------------------------------------------------------------
    // Main POS Screen
    // -------------------------------------------------------------------------

    public function index()
    {
        $user       = Auth::user();
        $branch     = $user->branch;
        $session    = $user->activeSession();
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $heldCount        = PosHeldOrder::where('cashier_id', $user->id)->count();
        $initialCart      = $this->renderCart();
        $initialCartCount = Cart::instance(self::CART)->count();
        $initialSubtotal  = (float) Cart::instance(self::CART)->subtotal(2, '.', '');
        $initialTax       = (float) Cart::instance(self::CART)->tax(2, '.', '');
        $coupons          = Coupon::where('expiry_date', '>=', Carbon::today())->orderBy('code')->get();

        return view('pos.index', compact('user', 'branch', 'session', 'categories', 'heldCount', 'initialCart', 'initialCartCount', 'initialSubtotal', 'initialTax', 'coupons'));
    }

    // -------------------------------------------------------------------------
    // Product Search (AJAX — returns HTML partial)
    // -------------------------------------------------------------------------

    public function product_search(Request $request)
    {
        $q          = $request->input('q', '');
        $category   = $request->input('category');

        $query = Product::with(['variants' => fn($q) => $q->where('is_active', true)->where('stock_qty', '>', 0)])
            ->where('stock_status', 'instock')
            ->whereHas('variants', fn($q) => $q->where('is_active', true)->where('stock_qty', '>', 0));

        if ($q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhereHas('variants', fn($q3) => $q3->where('barcode', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%"));
            });
        }
        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query->limit(24)->get();

        return view('pos.partials.products', compact('products'));
    }

    // -------------------------------------------------------------------------
    // Cart Operations (AJAX — return cart partial)
    // -------------------------------------------------------------------------

    public function cart_add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = ProductVariant::findOrFail($request->variant_id);

        if (!$variant->isInStock()) {
            return response()->json(['success' => false, 'message' => 'Variant is out of stock.'], 422);
        }

        $itemName = $product->name . ' (' . $variant->display_label . ')';

        Cart::instance(self::CART)
            ->add($product->id, $itemName, $request->quantity, $variant->price, [
                'variant_id'    => $variant->id,
                'variant_label' => $variant->display_label,
            ])
            ->associate(Product::class);

        return response()->json([
            'success' => true,
            'cart'    => $this->renderCart(),
            'count'   => Cart::instance(self::CART)->count(),
        ]);
    }

    public function cart_update(Request $request, string $rowId)
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0']]);

        if ((int) $request->quantity === 0) {
            Cart::instance(self::CART)->remove($rowId);
        } else {
            Cart::instance(self::CART)->update($rowId, $request->quantity);
        }

        return response()->json([
            'success' => true,
            'cart'    => $this->renderCart(),
            'count'   => Cart::instance(self::CART)->count(),
        ]);
    }

    public function cart_remove(string $rowId)
    {
        Cart::instance(self::CART)->remove($rowId);

        return response()->json([
            'success' => true,
            'cart'    => $this->renderCart(),
            'count'   => Cart::instance(self::CART)->count(),
        ]);
    }

    public function cart_clear()
    {
        Cart::instance(self::CART)->destroy();
        return response()->json(['success' => true, 'cart' => $this->renderCart(), 'count' => 0]);
    }

    // -------------------------------------------------------------------------
    // Customer Search (AJAX — returns JSON)
    // -------------------------------------------------------------------------

    public function customer_search(Request $request)
    {
        $q = $request->input('q', '');

        $customers = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->whereNull('pos_role')
            ->where('utype', '!=', 'ADM')
            ->limit(10)
            ->get(['id', 'name', 'email', 'mobile']);

        return response()->json($customers);
    }

    public function customer_create(Request $request)
    {
        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20'],
            'email'  => ['nullable', 'email', 'max:100'],
        ]);

        $customer = User::firstOrCreate(
            ['mobile' => $request->mobile],
            [
                'name'     => $request->name,
                'email'    => $request->email ?? strtolower(str_replace(' ', '', $request->name)) . '@walkin.pos',
                'password' => bcrypt($request->mobile),
            ]
        );

        return response()->json(['success' => true, 'customer' => $customer->only(['id', 'name', 'email', 'mobile'])]);
    }

    public function customer_lookup(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        if (!$phone) {
            return response()->json(['found' => false]);
        }

        $customer = User::where('mobile', $phone)
            ->whereNull('pos_role')
            ->where('utype', '!=', 'ADM')
            ->first(['id', 'name', 'mobile', 'email']);

        if (!$customer) {
            return response()->json(['found' => false]);
        }

        $addresses = CustomerAddress::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get(['id', 'title', 'address', 'city', 'is_default']);

        return response()->json([
            'found'     => true,
            'customer'  => $customer,
            'addresses' => $addresses,
        ]);
    }

    public function address_save(Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:users,id'],
            'address_id'  => ['nullable', 'exists:customer_addresses,id'],
            'title'       => ['required', 'in:Home,Office,Other'],
            'address'     => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'is_default'  => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_default')) {
            CustomerAddress::where('customer_id', $request->customer_id)->update(['is_default' => false]);
        }

        if ($request->address_id) {
            $addr = CustomerAddress::where('customer_id', $request->customer_id)
                ->findOrFail($request->address_id);
            $addr->update([
                'title'      => $request->title,
                'address'    => $request->address,
                'city'       => $request->city,
                'is_default' => $request->boolean('is_default'),
            ]);
        } else {
            $addr = CustomerAddress::create([
                'customer_id' => $request->customer_id,
                'title'       => $request->title,
                'address'     => $request->address,
                'city'        => $request->city,
                'is_default'  => $request->boolean('is_default'),
            ]);
        }

        return response()->json(['success' => true, 'address' => $addr]);
    }

    // -------------------------------------------------------------------------
    // Hold Order
    // -------------------------------------------------------------------------

    public function hold_order(Request $request)
    {
        $cart = Cart::instance(self::CART)->content();

        if ($cart->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty.']);
        }

        $user   = Auth::user();
        $branch = $user->branch;

        PosHeldOrder::create([
            'cashier_id'    => $user->id,
            'branch_id'     => $branch?->id ?? 0,
            'cart_data'     => $cart->map(fn ($item) => [
                'id'           => $item->id,
                'name'         => $item->name,
                'qty'          => $item->qty,
                'price'        => $item->price,
                'rowId'        => $item->rowId,
                'variant_id'   => $item->options->variant_id ?? null,
                'variant_label'=> $item->options->variant_label ?? null,
            ])->values()->toArray(),
            'customer_data' => $request->input('customer'),
            'gift_data'     => $request->input('gift'),
            'note'          => $request->input('note'),
        ]);

        Cart::instance(self::CART)->destroy();

        return response()->json([
            'success'   => true,
            'message'   => 'Order held.',
            'heldCount' => PosHeldOrder::where('cashier_id', $user->id)->count(),
            'cart'      => $this->renderCart(),
        ]);
    }

    public function held_orders()
    {
        $held = PosHeldOrder::where('cashier_id', Auth::id())
            ->latest()
            ->get();

        return view('pos.held-orders', compact('held'));
    }

    public function resume_order(int $id)
    {
        $held = PosHeldOrder::where('cashier_id', Auth::id())->findOrFail($id);

        Cart::instance(self::CART)->destroy();

        foreach ($held->cart_data as $item) {
            $options = [];
            if (!empty($item['variant_id'])) {
                $options['variant_id']    = $item['variant_id'];
                $options['variant_label'] = $item['variant_label'] ?? '';
            }
            Cart::instance(self::CART)
                ->add($item['id'], $item['name'], $item['qty'], $item['price'], $options)
                ->associate(Product::class);
        }

        $customerData = $held->customer_data;
        $giftData     = $held->gift_data;

        $held->delete();

        return response()->json([
            'success'      => true,
            'cart'         => $this->renderCart(),
            'count'        => Cart::instance(self::CART)->count(),
            'customerData' => $customerData,
            'giftData'     => $giftData,
        ]);
    }

    // -------------------------------------------------------------------------
    // Checkout
    // -------------------------------------------------------------------------

    public function checkout()
    {
        if (Cart::instance(self::CART)->count() === 0) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty.');
        }

        $cartItems = Cart::instance(self::CART)->content();
        $subtotal  = Cart::instance(self::CART)->subtotal(2, '.', '');
        $tax       = Cart::instance(self::CART)->tax(2, '.', '');
        $total     = Cart::instance(self::CART)->total(2, '.', '');
        $user      = Auth::user();
        $branch    = $user->branch;
        $session   = $user->activeSession();

        return view('pos.checkout', compact('cartItems', 'subtotal', 'tax', 'total', 'user', 'branch', 'session'));
    }

    public function place_order(Request $request)
    {
        $request->validate([
            'payment_method'    => ['required', 'in:cash,online_transfer'],
            'cash_received'     => ['required_if:payment_method,cash', 'nullable', 'numeric', 'min:0'],
            'online_platform'   => ['required_if:payment_method,online_transfer', 'nullable', 'string', 'max:50'],
            'reference_no'      => ['nullable', 'string', 'max:100'],
            'payment_verified'  => ['nullable', 'boolean'],
            'notes'             => ['nullable', 'string', 'max:255'],
            'order_note'        => ['nullable', 'string', 'max:500'],
            'discount_amount'   => ['nullable', 'numeric', 'min:0'],
            'coupon_code'       => ['nullable', 'string', 'max:50'],
            // Customer
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'customer_id'      => ['nullable', 'exists:users,id'],
            'customer_name'    => ['nullable', 'string', 'max:100'],
            'order_type'       => ['nullable', 'in:booking,pickup'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'delivery_city'    => ['nullable', 'string', 'max:100'],
            'address_id'       => ['nullable', 'exists:customer_addresses,id'],
            'save_customer'    => ['nullable', 'boolean'],
            // Gift
            'is_gift'                => ['nullable', 'in:1,0'],
            'gift_sender_name'       => ['required_if:is_gift,1', 'nullable', 'string', 'max:100'],
            'gift_sender_phone'      => ['required_if:is_gift,1', 'nullable', 'string', 'max:20'],
            'gift_sender_address'    => ['required_if:is_gift,1', 'nullable', 'string', 'max:255'],
            'gift_sender_city'       => ['required_if:is_gift,1', 'nullable', 'string', 'max:100'],
            'gift_receiver_name'     => ['required_if:is_gift,1', 'nullable', 'string', 'max:100'],
            'gift_receiver_phone'    => ['required_if:is_gift,1', 'nullable', 'string', 'max:20'],
            'gift_receiver_address'  => ['required_if:is_gift,1', 'nullable', 'string'],
            'gift_receiver_city'     => ['required_if:is_gift,1', 'nullable', 'string', 'max:100'],
            'delivery_date'          => ['nullable', 'date'],
            'gift_message'           => ['nullable', 'string', 'max:500'],
            'gift_wrapping'          => ['nullable', 'boolean'],
        ]);

        $cartItems = Cart::instance(self::CART)->content();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['error' => 'Cart is empty.']);
        }

        $isGift    = (bool) $request->is_gift;
        $isBooking = !$isGift && ($request->order_type === 'booking');
        $cashier   = Auth::user();
        $branch    = $cashier->branch;
        $session   = $cashier->activeSession();

        // Resolve or create customer
        $phone = $request->customer_phone ?: ('ANON-' . time());
        if ($request->customer_id) {
            $customerId    = (int) $request->customer_id;
            $customerObj   = User::find($customerId);
            $customerName  = $customerObj?->name ?? 'Customer';
            $customerPhone = $request->customer_phone ?? $customerObj?->mobile ?? '';

            // Save new typed address for existing customer
            if ($isBooking && !$request->address_id && $request->delivery_address) {
                CustomerAddress::create([
                    'customer_id' => $customerId,
                    'title'       => 'Other',
                    'address'     => $request->delivery_address,
                    'city'        => $request->delivery_city ?? '',
                    'is_default'  => false,
                ]);
            }
        } else {
            $customerObj = User::firstOrCreate(
                ['mobile' => $phone],
                [
                    'name'     => $request->customer_name ?? 'Walk-in Customer',
                    'email'    => 'pos' . time() . '@pos.local',
                    'password' => bcrypt($phone),
                ]
            );
            $customerId    = $customerObj->id;
            $customerName  = $customerObj->name;
            $customerPhone = $phone;

            // Optionally persist address for new customer
            if ($request->boolean('save_customer') && $isBooking && $request->delivery_address) {
                CustomerAddress::firstOrCreate(
                    ['customer_id' => $customerId, 'address' => $request->delivery_address],
                    ['title' => 'Home', 'city' => $request->delivery_city ?? '', 'is_default' => true]
                );
            }
        }

        // Compute totals
        $subtotal = (float) str_replace(',', '', Cart::instance(self::CART)->subtotal(2, '.', ''));
        $tax      = (float) str_replace(',', '', Cart::instance(self::CART)->tax(2, '.', ''));
        $discount = (float) ($request->discount_amount ?? 0);
        $total    = max(0, $subtotal + $tax - $discount);

        // Build order
        $order = new Order();
        $order->user_id              = $customerId;
        $order->subtotal             = $subtotal;
        $order->tax                  = $tax;
        $order->discount             = $discount;
        $order->total                = $total;
        $order->source               = 'pos';
        $order->cashier_id           = $cashier->id;
        $order->branch_id            = $branch?->id;
        $order->pos_session_id       = $session?->id;
        $order->coupon_code          = $request->coupon_code ?: null;
        $order->order_note           = $request->order_note;
        $order->requested_delivery_date = $request->delivery_date;
        $order->delivery_time_slot   = $request->delivery_time_slot;
        $order->status               = 'ordered';
        $isVerified = $request->payment_method === 'cash'
            || ($request->payment_method === 'online_transfer' && $request->boolean('payment_verified'));
        $order->payment_status = $isVerified ? 'paid' : 'pending';

        // Delivery address
        if ($isGift) {
            $order->name    = $request->gift_receiver_name;
            $order->phone   = $request->gift_receiver_phone;
            $order->address = $request->gift_receiver_address;
            $order->city    = $request->gift_receiver_city ?? 'N/A';
            $order->type    = 'gift';
        } elseif ($isBooking) {
            if ($request->address_id) {
                $addr = CustomerAddress::find($request->address_id);
                $order->address = $addr?->address ?? $request->delivery_address ?? 'N/A';
                $order->city    = $addr?->city ?? $request->delivery_city ?? 'N/A';
            } else {
                $order->address = $request->delivery_address ?? $branch?->address ?? 'N/A';
                $order->city    = $request->delivery_city ?? 'N/A';
            }
            $order->name  = $customerName;
            $order->phone = $customerPhone;
            $order->type  = 'booking';
        } else {
            $order->name    = $customerName;
            $order->phone   = $customerPhone;
            $order->address = $branch?->address ?? 'Store Pickup';
            $order->city    = $branch?->city ?? 'N/A';
            $order->type    = 'pickup';
        }

        $order->locality = $branch?->address ?? '';
        $order->landmark = '';
        $order->zip      = '';
        $order->state    = '';
        $order->country  = 'Pakistan';

        $order->save();

        AdminNotification::notify(
            'new_order',
            'New POS Order ' . $order->order_number,
            ($order->name ?: 'Walk-in') . ' — Rs ' . number_format($order->total, 0) . ' via ' . ucfirst($cashier->branch?->name ?? 'POS'),
            route('admin.order.details', $order->id)
        );

        NotificationService::orderPlaced($order);

        // Save order items & deduct stock
        foreach ($cartItems as $item) {
            $variantId    = $item->options->variant_id ?? null;
            $variantLabel = $item->options->variant_label ?? null;

            $orderItem                = new OrderItem();
            $orderItem->product_id    = $item->id;
            $orderItem->variant_id    = $variantId;
            $orderItem->variant_label = $variantLabel;
            $orderItem->order_id      = $order->id;
            $orderItem->price         = $item->price;
            $orderItem->quantity      = $item->qty;
            $orderItem->save();

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $before = $variant->stock_qty;
                    $variant->stock_qty = max(0, $before - $item->qty);
                    $variant->save();
                    $variant->product->syncStockStatus();
                    InventoryLog::record($item->id, 'order', $before, -$item->qty, $variantId, null, "POS Order #{$order->id}", $cashier->id);
                }
            }
        }

        // Save gift details
        if ($isGift) {
            GiftOrder::create([
                'order_id'         => $order->id,
                'sender_name'      => $request->gift_sender_name,
                'sender_phone'     => $request->gift_sender_phone,
                'sender_email'     => $cashier->email,
                'sender_address'   => $request->gift_sender_address,
                'sender_city'      => $request->gift_sender_city,
                'receiver_name'    => $request->gift_receiver_name,
                'receiver_phone'   => $request->gift_receiver_phone,
                'receiver_city'    => $request->gift_receiver_city,
                'receiver_address' => $request->gift_receiver_address,
                'gift_message'     => $request->gift_message,
                'gift_wrapping'    => (bool) $request->gift_wrapping,
            ]);
        }

        // Save POS payment
        $cashReceived = $request->payment_method === 'cash' ? (float) $request->cash_received : null;
        PosPayment::create([
            'order_id'         => $order->id,
            'method'           => $request->payment_method,
            'amount'           => $total,
            'cash_received'    => $cashReceived,
            'change_given'     => $cashReceived ? max(0, $cashReceived - $total) : null,
            'reference_no'     => $request->reference_no,
            'online_platform'  => $request->online_platform,
            'payment_verified' => $request->payment_method === 'online_transfer'
                                    ? $request->boolean('payment_verified')
                                    : null,
            'notes'            => $request->notes,
        ]);

        // Log order history
        OrderHistory::create([
            'order_id'   => $order->id,
            'status'     => 'ordered',
            'note'       => 'POS order placed',
            'created_by' => $cashier->id,
        ]);

        Cart::instance(self::CART)->destroy();

        return redirect()->route('pos.receipt', $order->id);
    }

    // -------------------------------------------------------------------------
    // Receipt
    // -------------------------------------------------------------------------

    public function receipt(int $id)
    {
        $order     = Order::with(['orderItems.product', 'posPayment', 'giftOrder', 'cashier', 'branch'])->findOrFail($id);
        $cashier   = Auth::user();

        // Only the placing cashier, supervisor, or admin can view
        if (! ($cashier->utype === 'ADM' || $cashier->isSupervisor() || $order->cashier_id === $cashier->id)) {
            abort(403);
        }

        $user    = $cashier;
        $branch  = $cashier->branch;
        $session = $cashier->activeSession();

        return view('pos.receipt', compact('order', 'user', 'branch', 'session'));
    }

    // -------------------------------------------------------------------------
    // Session Management
    // -------------------------------------------------------------------------

    public function sessions()
    {
        $user     = Auth::user();
        $sessions = PosSession::with('branch')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $currentSession = $user->activeSession();

        return view('pos.sessions', compact('sessions', 'currentSession'));
    }

    public function session_open(Request $request)
    {
        $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ]);

        $user   = Auth::user();
        $branch = $user->branch;

        if (! $branch) {
            return back()->withErrors(['error' => 'You are not assigned to any branch.']);
        }

        if ($user->activeSession()) {
            return back()->withErrors(['error' => 'A session is already open.']);
        }

        PosSession::create([
            'user_id'         => $user->id,
            'branch_id'       => $branch->id,
            'opening_balance' => $request->opening_balance,
            'status'          => 'open',
            'opened_at'       => now(),
        ]);

        return back()->with('status', 'Session opened.');
    }

    public function session_close(Request $request)
    {
        $request->validate([
            'closing_balance' => ['required', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $session = Auth::user()->activeSession();

        if (! $session) {
            return back()->withErrors(['error' => 'No open session found.']);
        }

        $expectedCash = $session->opening_balance + $session->totalSales();

        $session->update([
            'closing_balance' => $request->closing_balance,
            'expected_cash'   => $expectedCash,
            'notes'           => $request->notes,
            'status'          => 'closed',
            'closed_at'       => now(),
        ]);

        return back()->with('status', 'Session closed. Expected: Rs ' . number_format($expectedCash, 2) . ', Counted: Rs ' . number_format($request->closing_balance, 2));
    }

    // -------------------------------------------------------------------------
    // Supervisor Dashboard
    // -------------------------------------------------------------------------

    public function supervisor_dashboard()
    {
        $branch     = Auth::user()->branch;
        $today      = today();

        $todaySales   = Order::where('source', 'pos')->whereDate('created_at', $today)->where('is_hold', false)->sum('total');
        $todayOrders  = Order::where('source', 'pos')->whereDate('created_at', $today)->where('is_hold', false)->count();
        $openSessions = PosSession::where('status', 'open')
            ->when($branch, fn ($q) => $q->where('branch_id', $branch->id))
            ->with('cashier')
            ->get();
        $recentOrders = Order::with(['cashier', 'posPayment'])
            ->where('source', 'pos')
            ->whereDate('created_at', $today)
            ->latest()
            ->limit(10)
            ->get();

        return view('pos.supervisor', compact('todaySales', 'todayOrders', 'openSessions', 'recentOrders', 'branch'));
    }

    // -------------------------------------------------------------------------
    // Admin: Branch & Cashier Management (called from AdminController)
    // -------------------------------------------------------------------------
    // (These are kept in AdminController for sidebar consistency — see AdminController)

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function renderCart(): string
    {
        $items    = Cart::instance(self::CART)->content();
        $subtotal = Cart::instance(self::CART)->subtotal(2, '.', '');
        $tax      = Cart::instance(self::CART)->tax(2, '.', '');
        $total    = Cart::instance(self::CART)->total(2, '.', '');

        return view('pos.partials.cart', compact('items', 'subtotal', 'tax', 'total'))->render();
    }
}
