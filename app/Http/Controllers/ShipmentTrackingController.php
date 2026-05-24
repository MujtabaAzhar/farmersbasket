<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentTrackingController extends Controller
{
    public function index(Request $request)
    {
        $shipment  = null;
        $order     = null;
        $error     = null;

        if ($request->filled('tracking')) {
            $query = trim($request->tracking);

            // Resolve FB-1010 order number format → actual order ID
            $resolvedOrderId = null;
            if (preg_match('/^FB-(\d+)$/i', $query, $m)) {
                $resolvedOrderId = (int)$m[1] - 1000;
            }

            // 1. Match by shipment tracking number (e.g. FB-2026-0001)
            $shipment = Shipment::with(['courier', 'order.orderItems.product', 'trackings'])
                ->where('tracking_number', $query)
                ->first();

            // 2. Match by order ID, order number (FB-1010), or customer phone
            if (!$shipment) {
                $shipment = Shipment::with(['courier', 'order.orderItems.product', 'trackings'])
                    ->whereHas('order', function ($q) use ($query, $resolvedOrderId) {
                        $q->where('id', is_numeric($query) ? $query : ($resolvedOrderId ?? 0))
                          ->orWhere(function ($q2) use ($resolvedOrderId) {
                              if ($resolvedOrderId) $q2->where('id', $resolvedOrderId);
                          })
                          ->orWhere('phone', $query);
                    })
                    ->latest()
                    ->first();
            }

            // 3. No shipment yet — look up the order directly
            if (!$shipment) {
                $orderId = $resolvedOrderId ?? (is_numeric($query) ? (int)$query : null);
                $order = Order::with('orderItems.product')
                    ->where(function ($q) use ($orderId, $query) {
                        if ($orderId) $q->where('id', $orderId);
                        $q->orWhere('phone', $query);
                    })
                    ->first();

                if (!$order) {
                    $error = 'No order or shipment found for "' . e($query) . '". Please check and try again.';
                }
            }
        }

        return view('orderTracking', compact('shipment', 'order', 'error'));
    }
}
