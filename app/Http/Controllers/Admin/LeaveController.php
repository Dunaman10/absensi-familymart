<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display all leave requests with metrics and filters.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with('user', 'reviewer');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $leaveRequests = $query->latest('created_at')->paginate($perPage)->withQueryString();

        // Metrics
        $totalRequests = LeaveRequest::count();
        $pendingCount = LeaveRequest::pending()->count();
        $approvedCount = LeaveRequest::approved()->count();
        $rejectedCount = LeaveRequest::rejected()->count();

        // Leave setting
        $leaveSetting = LeaveSetting::first() ?? new LeaveSetting(['max_annual_leave_days' => 12]);

        return view('admin.leaves.index', compact(
            'leaveRequests',
            'totalRequests',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'leaveSetting'
        ));
    }

    /**
     * Approve a pending leave request.
     */
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'PENDING') {
            return redirect()->route('admin.leaves.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $leaveRequest->update([
            'status' => 'APPROVED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.leaves.index')
            ->with('success', "Pengajuan perizinan {$leaveRequest->user->name} berhasil disetujui.");
    }

    /**
     * Reject a pending leave request.
     */
    public function reject(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'PENDING') {
            return redirect()->route('admin.leaves.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'REJECTED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'reject_reason' => $validated['reject_reason'],
        ]);

        return redirect()->route('admin.leaves.index')
            ->with('success', "Pengajuan perizinan {$leaveRequest->user->name} telah ditolak.");
    }

    /**
     * Update leave settings (max annual leave days).
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_annual_leave_days' => 'required|integer|min:1|max:365',
        ]);

        LeaveSetting::updateOrCreate(
            ['id' => 1],
            ['max_annual_leave_days' => $validated['max_annual_leave_days']]
        );

        return redirect()->route('admin.leaves.index')
            ->with('success', "Batas cuti tahunan berhasil diperbarui menjadi {$validated['max_annual_leave_days']} hari.");
    }
}
