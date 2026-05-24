<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Rider;
use Illuminate\Http\Request;

class RiderController extends Controller
{
    public function index()
    {
        $riders   = Rider::with('branch')->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.riders', compact('riders', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'vehicle_type' => 'required|in:bike,van,pickup',
            'branch_id'    => 'nullable|exists:branches,id',
        ]);

        Rider::create($request->only('name', 'phone', 'vehicle_type', 'branch_id') + ['is_active' => true]);

        return back()->with('success', 'Rider added successfully.');
    }

    public function update(Request $request, Rider $rider)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'vehicle_type' => 'required|in:bike,van,pickup',
            'branch_id'    => 'nullable|exists:branches,id',
            'is_active'    => 'boolean',
        ]);

        $rider->update($request->only('name', 'phone', 'vehicle_type', 'branch_id', 'is_active'));

        return back()->with('success', 'Rider updated.');
    }

    public function destroy(Rider $rider)
    {
        if ($rider->shipments()->whereNotIn('status', ['delivered', 'canceled', 'returned'])->exists()) {
            return back()->with('error', 'Cannot delete rider with active shipments.');
        }
        $rider->delete();
        return back()->with('success', 'Rider removed.');
    }
}
