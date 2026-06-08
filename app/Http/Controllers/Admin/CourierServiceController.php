<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourierServiceController extends Controller
{
    public function index()
    {
        $couriers = CourierService::withCount('shipments')->orderBy('name')->get();
        return view('admin.couriers.index', compact('couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'tracking_url_template' => 'nullable|string|max:255',
            'is_active'             => 'boolean',
        ]);

        $code = Str::slug($request->name, '_');

        // Ensure unique code
        $base = $code;
        $i = 2;
        while (CourierService::where('code', $code)->exists()) {
            $code = $base . '_' . $i++;
        }

        CourierService::create([
            'name'                  => $request->name,
            'code'                  => $code,
            'tracking_url_template' => $request->tracking_url_template,
            'is_active'             => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Courier \"{$request->name}\" added successfully.");
    }

    public function update(Request $request, CourierService $courier)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'api_key'               => 'nullable|string|max:255',
            'api_password'          => 'nullable|string|max:255',
            'api_base_url'          => 'nullable|url|max:255',
            'is_active'             => 'boolean',
            'tracking_url_template' => 'nullable|string|max:255',
        ]);

        $data = [
            'name'                  => $request->name,
            'is_active'             => $request->boolean('is_active'),
            'tracking_url_template' => $request->tracking_url_template,
            'api_base_url'          => $request->api_base_url,
        ];

        if ($request->filled('api_key'))      $data['api_key']      = $request->api_key;
        if ($request->filled('api_password')) $data['api_password'] = $request->api_password;

        $courier->update($data);

        return back()->with('success', $courier->name . ' updated.');
    }

    public function destroy(CourierService $courier)
    {
        if ($courier->shipments_count > 0) {
            return back()->with('error', "Cannot delete \"{$courier->name}\" — it has {$courier->shipments_count} shipment(s) linked.");
        }

        $name = $courier->name;
        $courier->delete();
        return back()->with('success', "Courier \"{$name}\" deleted.");
    }
}
