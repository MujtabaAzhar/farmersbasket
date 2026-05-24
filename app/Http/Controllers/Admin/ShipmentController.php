<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['order', 'courier', 'rider', 'bookedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('courier')) {
            $query->where('courier_service_id', $request->courier);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('tracking_number', 'like', "%{$q}%")
                    ->orWhereHas('order', fn($o) => $o->where('id', $q)
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%"));
            });
        }

        $shipments = $query->paginate(20)->withQueryString();
        $couriers  = CourierService::where('is_active', true)->get();

        return view('admin.shipments.index', compact('shipments', 'couriers'));
    }

    public function create(Request $request)
    {
        $order = Order::with(['orderItems.product', 'orderItems.variant'])
            ->findOrFail($request->order_id);

        $existing = Shipment::where('order_id', $order->id)
            ->whereNotIn('status', ['canceled', 'returned'])
            ->first();

        $couriers = CourierService::where('is_active', true)
            ->orderByRaw("FIELD(code,'internal','leopards','tcs','mnp')")
            ->get();

        $riders = Rider::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.shipments.create', compact('order', 'couriers', 'existing', 'riders'));
    }

    public function store(Request $request)
    {
        $courierService = CourierService::findOrFail($request->courier_service_id);
        $isInternal     = $courierService->code === 'internal';

        // Shared validation
        $rules = [
            'order_id'           => 'required|integer|exists:orders,id',
            'courier_service_id' => 'required|integer|exists:courier_services,id',
            'recipient_name'     => 'required|string|max:100',
            'recipient_phone'    => 'required|string|max:20',
            'recipient_address'  => 'required|string|max:255',
            'origin_city'        => 'required|string|max:100',
            'destination_city'   => 'required|string|max:100',
            'notes'              => 'nullable|string|max:500',
        ];

        if ($isInternal) {
            $rules['rider_id']            = 'required|integer|exists:riders,id';
            $rules['vehicle_type']        = 'required|in:bike,van,pickup';
            $rules['estimated_delivery']  = 'required|date|after_or_equal:today';
            $rules['delivery_time_slot']  = 'required|in:10am-1pm,1pm-4pm,4pm-7pm';
        } else {
            $rules['weight']              = 'required|numeric|min:0.1|max:999';
            $rules['pieces']              = 'required|integer|min:1';
            $rules['declared_value']      = 'required|numeric|min:0';
            $rules['special_instructions'] = 'nullable|string|max:500';
            $rules['estimated_delivery']  = 'nullable|date|after_or_equal:today';
        }

        $request->validate($rules);

        $data = [
            'order_id'            => $request->order_id,
            'courier_service_id'  => $request->courier_service_id,
            'status'              => 'pending',
            'recipient_name'      => $request->recipient_name,
            'recipient_phone'     => $request->recipient_phone,
            'recipient_address'   => $request->recipient_address,
            'origin_city'         => $request->origin_city,
            'destination_city'    => $request->destination_city,
            'estimated_delivery'  => $request->estimated_delivery,
            'booking_date'        => now()->toDateString(),
            'booked_by'           => Auth::id(),
            'notes'               => $request->notes,
        ];

        if ($isInternal) {
            $data['rider_id']           = $request->rider_id;
            $data['vehicle_type']       = $request->vehicle_type;
            $data['delivery_time_slot'] = $request->delivery_time_slot;
        } else {
            $data['weight']               = $request->weight;
            $data['pieces']               = $request->pieces;
            $data['declared_value']       = $request->declared_value;
            $data['special_instructions'] = $request->special_instructions;
        }

        $shipment = Shipment::create($data);

        // Book with courier (generates tracking number)
        $courier = CourierManager::make($courierService);
        $result  = $courier->book($shipment);

        if ($result['success']) {
            $shipment->update([
                'status'          => 'booked',
                'tracking_number' => $result['tracking_number'],
                'cn_number'       => $result['cn_number'] ?? null,
                'raw_response'    => $result['raw'] ?? null,
            ]);

            Order::find($request->order_id)->update([
                'status'          => 'shipped',
                'tracking_number' => $result['tracking_number'],
                'courier_name'    => $courierService->name,
            ]);

            $desc = $isInternal
                ? 'Shipment assigned to rider ' . ($shipment->rider?->name ?? '') . '. Pending pickup.'
                : 'Shipment booked with ' . $courierService->name . '.';

            ShipmentTracking::record($shipment->id, 'booked', $desc, $request->origin_city);
        }

        return redirect()->route('admin.shipments.show', $shipment)
            ->with($result['success'] ? 'success' : 'warning',
                $result['success']
                    ? 'Shipment created — Tracking: ' . $shipment->tracking_number
                    : 'Shipment saved but booking failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'order.orderItems.product',
            'order.orderItems.variant',
            'courier', 'rider.branch',
            'trackings',
            'bookedBy',
        ]);
        return view('admin.shipments.show', compact('shipment'));
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $defaultDescriptions = [
            'booked'           => 'Shipment booked and awaiting pickup.',
            'picked_up'        => 'Order picked up from store and dispatched.',
            'in_transit'       => 'Order is in transit.',
            'out_for_delivery' => 'Rider is on the way to deliver your order.',
            'delivered'        => 'Order delivered successfully.',
            'failed'           => 'Delivery attempt failed.',
            'returned'         => 'Shipment returned to sender.',
            'canceled'         => 'Shipment canceled.',
        ];

        $request->validate([
            'status'      => 'required|in:' . implode(',', array_keys(Shipment::STATUSES)),
            'description' => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:100',
            'event_time'  => 'nullable|date',
        ]);

        $description = $request->filled('description')
            ? $request->description
            : ($defaultDescriptions[$request->status] ?? ucwords(str_replace('_', ' ', $request->status)));

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
            $description,
            $request->location,
            $request->event_time ?? now(),
            'manual'
        );

        return back()->with('success', 'Status updated to "' . $shipment->fresh()->status_label . '".');
    }

    public function refresh(Shipment $shipment)
    {
        if (!$shipment->tracking_number) {
            return back()->with('error', 'No tracking number assigned yet.');
        }

        if ($shipment->isInternal()) {
            return back()->with('info', 'Internal shipments are tracked manually — use the Update Status form below.');
        }

        $courier = CourierManager::make($shipment->courier);

        if (!$courier->isConfigured()) {
            return back()->with('warning', 'Courier API not configured. Add credentials in Courier Services settings.');
        }

        $result = $courier->track($shipment->tracking_number);

        if (!$result['success']) {
            return back()->with('error', 'Tracking failed: ' . ($result['message'] ?? 'API error'));
        }

        $imported = 0;
        foreach ($result['events'] as $event) {
            $exists = ShipmentTracking::where('shipment_id', $shipment->id)
                ->where('event_time', $event['event_time'])
                ->where('status', $event['status'])
                ->exists();

            if (!$exists) {
                ShipmentTracking::record(
                    $shipment->id,
                    $event['status'],
                    $event['description'],
                    $event['location'] ?? null,
                    $event['event_time'],
                    'api'
                );
                $imported++;
            }
        }

        if (!empty($result['events'])) {
            $latest = collect($result['events'])->sortByDesc('event_time')->first();
            $shipment->update(['status' => $latest['status'], 'last_tracked_at' => now()]);
        }

        return back()->with('success', "Tracking refreshed — {$imported} new event(s) imported.");
    }

    public function destroy(Shipment $shipment)
    {
        if (!in_array($shipment->status, ['pending', 'canceled'])) {
            return back()->with('error', 'Only pending or canceled shipments can be deleted.');
        }
        $shipment->delete();
        return redirect()->route('admin.shipments.index')->with('success', 'Shipment deleted.');
    }
}
