<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['order', 'rider.branch', 'trackings'])
            ->whereHas('courier', fn($q) => $q->where('code', 'internal'))
            ->whereNotIn('status', ['delivered', 'canceled', 'returned']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shipments = $query->orderBy('estimated_delivery')->orderBy('delivery_time_slot')->get();

        return view('admin.dispatch', compact('shipments'));
    }

    public function quickUpdate(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
        ]);

        $labels = [
            'picked_up'        => 'Out from Store',
            'out_for_delivery' => 'On the Way',
            'delivered'        => 'Delivered',
        ];

        $descriptions = [
            'picked_up'        => 'Order picked up from store and dispatched to rider.',
            'out_for_delivery' => 'Rider is on the way to deliver your order.',
            'delivered'        => 'Order delivered successfully.',
        ];

        $shipment->update(['status' => $request->status]);

        if ($request->status === 'delivered') {
            $shipment->update(['actual_delivery' => now()->toDateString()]);
            $shipment->order->update([
                'status'         => 'delivered',
                'delivered_date' => now(),
                'payment_status' => 'paid',
            ]);
        }

        ShipmentTracking::record(
            $shipment->id,
            $request->status,
            $descriptions[$request->status],
            null,
            now(),
            'manual'
        );

        return back()->with('success',
            $shipment->order->order_number . ' → ' . $labels[$request->status]);
    }
}
