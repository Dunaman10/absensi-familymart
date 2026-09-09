<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display employee leave requests with quota metrics.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = now()->timezone('Asia/Jakarta')->year;

        // Fetch employee's leave requests (ordered by latest start_date and ID)
        $query = $user->leaveRequests()->latest('start_date')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $leaveRequests = $query->paginate($perPage)->withQueryString();

        // Leave quota calculation (CUTI and IZIN count against annual allowance)
        $maxLeaveDays = LeaveSetting::getMaxDays();
        $usedLeaveDays = LeaveRequest::where('user_id', $user->id)
            ->whereIn('type', ['CUTI', 'IZIN'])
            ->where('status', 'APPROVED')
            ->whereYear('start_date', $currentYear)
            ->sum('duration_days');
        $remainingLeaveDays = max(0, $maxLeaveDays - $usedLeaveDays);

        // Monthly stats
        $currentMonth = now()->timezone('Asia/Jakarta')->month;
        $monthlyRequestCount = LeaveRequest::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Last request status
        $lastRequest = LeaveRequest::where('user_id', $user->id)
            ->latest('start_date')
            ->latest('id')
            ->first();

        return view('employee.leaves.index', compact(
            'leaveRequests',
            'maxLeaveDays',
            'usedLeaveDays',
            'remainingLeaveDays',
            'monthlyRequestCount',
            'lastRequest'
        ));
    }

    /**
     * Store a new leave request from the employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:IZIN,CUTI,SAKIT',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate duration
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $durationDays = $startDate->diffInDays($endDate) + 1;

        // Check leave quota for CUTI and IZIN types
        if (in_array($validated['type'], ['CUTI', 'IZIN'])) {
            $currentYear = now()->timezone('Asia/Jakarta')->year;
            $maxDays = LeaveSetting::getMaxDays();
            $usedDays = LeaveRequest::where('user_id', Auth::id())
                ->whereIn('type', ['CUTI', 'IZIN'])
                ->where('status', 'APPROVED')
                ->whereYear('start_date', $currentYear)
                ->sum('duration_days');

            if (($usedDays + $durationDays) > $maxDays) {
                return redirect()->route('employee.leaves.index')
                    ->with('error', 'Sisa kuota perizinan/cuti tahunan Anda tidak mencukupi. Tersisa '.max(0, $maxDays - $usedDays).' hari.');
            }
        }

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('uploads/leaves', 'public');
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration_days' => $durationDays,
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'PENDING',
        ]);

        return redirect()->route('employee.leaves.index')
            ->with('success', 'Pengajuan perizinan berhasil dikirim! Admin akan segera meninjau.');
    }
}
