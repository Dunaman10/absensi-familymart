<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeofenceSetting;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    /**
     * Display the geofencing and office location management page.
     */
    public function index(Request $request)
    {
        $query = GeofenceSetting::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('office_name', 'LIKE', "%{$search}%");
        }

        $geofences = $query->latest()->get();
        $primaryGeofence = GeofenceSetting::where('is_active', true)->first() ?? $geofences->first();

        $totalLocations = GeofenceSetting::count();
        $activeLocations = GeofenceSetting::where('is_active', true)->count();

        return view('admin.geofences.index', compact(
            'geofences',
            'primaryGeofence',
            'totalLocations',
            'activeLocations'
        ));
    }

    /**
     * Store a new office geofence location.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius_in_meters' => 'required|integer|min:5|max:10000',
            'shift_start' => 'required|string',
            'shift_end' => 'required|string',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $is_active = $request->has('is_active') ? true : false;

        // If newly added is marked active, deactivate others if preferred
        if ($is_active) {
            GeofenceSetting::query()->update(['is_active' => false]);
        }

        GeofenceSetting::create([
            'office_name' => $validated['office_name'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'radius_in_meters' => $validated['radius_in_meters'],
            'shift_start' => $validated['shift_start'],
            'shift_end' => $validated['shift_end'],
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'],
            'is_active' => $is_active,
        ]);

        return redirect()->route('admin.geofences.index')
            ->with('success', "Lokasi kantor '{$validated['office_name']}' berhasil ditambahkan!");
    }

    /**
     * Update primary or specific geofence configuration.
     */
    public function update(Request $request, $id)
    {
        $geofence = GeofenceSetting::findOrFail($id);

        $validated = $request->validate([
            'office_name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius_in_meters' => 'required|integer|min:5|max:10000',
            'shift_start' => 'nullable|string',
            'shift_end' => 'nullable|string',
            'late_tolerance_minutes' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active') && $request->is_active) {
            GeofenceSetting::where('id', '!=', $id)->update(['is_active' => false]);
            $geofence->is_active = true;
        }

        $geofence->office_name = $validated['office_name'];
        $geofence->lat = $validated['lat'];
        $geofence->lng = $validated['lng'];
        $geofence->radius_in_meters = $validated['radius_in_meters'];

        if (!empty($validated['shift_start'])) {
            $geofence->shift_start = $validated['shift_start'];
        }
        if (!empty($validated['shift_end'])) {
            $geofence->shift_end = $validated['shift_end'];
        }
        if (isset($validated['late_tolerance_minutes'])) {
            $geofence->late_tolerance_minutes = $validated['late_tolerance_minutes'];
        }

        $geofence->save();

        return redirect()->route('admin.geofences.index')
            ->with('success', "Konfigurasi Geofencing untuk '{$geofence->office_name}' berhasil diperbarui!");
    }

    /**
     * Delete a geofence location.
     */
    public function destroy($id)
    {
        $geofence = GeofenceSetting::findOrFail($id);
        $name = $geofence->office_name;
        $geofence->delete();

        return redirect()->route('admin.geofences.index')
            ->with('success', "Lokasi kantor '{$name}' telah dihapus!");
    }
}
