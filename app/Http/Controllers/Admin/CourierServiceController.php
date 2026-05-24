<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use Illuminate\Http\Request;

class CourierServiceController extends Controller
{
    public function index()
    {
        $couriers = CourierService::withCount('shipments')->get();
        return view('admin.couriers.index', compact('couriers'));
    }

    public function update(Request $request, CourierService $courier)
    {
        $request->validate([
            'api_key'      => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'api_base_url' => 'nullable|url|max:255',
            'is_active'    => 'boolean',
            'tracking_url_template' => 'nullable|string|max:255',
            'settings'     => 'nullable|array',
        ]);

        $data = $request->only(['api_base_url', 'is_active', 'tracking_url_template', 'settings']);

        // Only update credentials if provided (blank = keep existing)
        if ($request->filled('api_key')) {
            $data['api_key'] = $request->api_key;
        }
        if ($request->filled('api_password')) {
            $data['api_password'] = $request->api_password;
        }

        $courier->update($data);

        return back()->with('success', $courier->name . ' settings updated.');
    }
}
