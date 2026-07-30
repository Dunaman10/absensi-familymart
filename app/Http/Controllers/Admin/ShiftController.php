<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display listing of shifts and employee shift assignments.
     */
    public function index(Request $request)
    {
        $shifts = Shift::withCount('users')->get();

        $query = User::where('role', 'user')->with('shift');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nip', 'LIKE', "%{$search}%")
                    ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->get();

        return view('admin.shifts.index', compact('shifts', 'employees'));
    }

    /**
     * Store a new Shift.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        Shift::create([
            'name' => $validated['name'],
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.shifts.index')
            ->with('success', "Shift baru '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update an existing Shift.
     */
    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        $startTime = strlen($validated['start_time']) === 5 ? $validated['start_time'].':00' : $validated['start_time'];
        $endTime = strlen($validated['end_time']) === 5 ? $validated['end_time'].':00' : $validated['end_time'];

        $shift->update([
            'name' => $validated['name'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.shifts.index')
            ->with('success', "Data shift '{$shift->name}' berhasil diperbarui!");
    }

    /**
     * Delete a shift.
     */
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $name = $shift->name;

        // Reset user shift_id to null before deleting
        User::where('shift_id', $shift->id)->update(['shift_id' => null]);
        $shift->delete();

        return redirect()->route('admin.shifts.index')
            ->with('success', "Shift '{$name}' telah dihapus.");
    }

    /**
     * Assign shift to specific employee.
     */
    public function assignEmployee(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->shift_id = $request->shift_id;
        $user->save();

        $shiftName = $user->shift ? $user->shift->name : 'Tanpa Shift';

        return redirect()->route('admin.shifts.index')
            ->with('success', "Jadwal shift untuk '{$user->name}' berhasil diubah menjadi: {$shiftName}");
    }
}
