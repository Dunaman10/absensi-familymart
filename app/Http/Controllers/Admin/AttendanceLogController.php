<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    /**
     * Display a listing of attendance logs (Log Presensi) with search and date filtering.
     */
    public function index(Request $request)
    {
        $query = AttendanceLog::with('user', 'geofence', 'approver');

        // Filter approval_status if specified
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Search Filter (Karyawan Name, NIP, Status, Type)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('nip', 'LIKE', "%{$search}%")
                        ->orWhere('position', 'LIKE', "%{$search}%");
                })
                    ->orWhere('type', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Calculate summary metrics
        $totalLogs = AttendanceLog::count();
        $checkInLogs = AttendanceLog::where('type', 'MASUK')->count();
        $checkOutLogs = AttendanceLog::where('type', 'PULANG')->count();
        $outOfRadiusLogs = AttendanceLog::where('is_outside_radius', true)->count();
        $pendingReasonLogs = AttendanceLog::where('approval_status', 'PENDING')->count();

        $perPage = $request->input('per_page', 5);
        $logs = $query->latest('date')->latest('time')->paginate($perPage)->withQueryString();

        return view('admin.attendance_logs.index', compact(
            'totalLogs',
            'checkInLogs',
            'checkOutLogs',
            'outOfRadiusLogs',
            'pendingReasonLogs',
            'logs'
        ));
    }

    /**
     * Setujui catatan alasan karyawan (Approve Reason).
     */
    public function approveReason(Request $request, $id)
    {
        $log = AttendanceLog::findOrFail($id);

        $log->approval_status = 'APPROVED';
        $log->approval_notes = $request->input('approval_notes', 'Alasan keterlambatan/pulang cepat disetujui oleh Admin.');
        $log->approved_by = auth()->id();
        $log->save();

        return redirect()->back()
            ->with('success', "Catatan alasan karyawan '{$log->user->name}' BERHASIL DISETUJUI. Presensi sah tercatat!");
    }

    /**
     * Tolak catatan alasan karyawan (Reject Reason).
     */
    public function rejectReason(Request $request, $id)
    {
        $log = AttendanceLog::findOrFail($id);

        $log->approval_status = 'REJECTED';
        $log->approval_notes = $request->input('approval_notes', 'Alasan ditolak oleh Admin HRIS.');
        $log->approved_by = auth()->id();
        $log->save();

        return redirect()->back()
            ->with('success', "Catatan alasan karyawan '{$log->user->name}' DITOLAK! Presensi dianggap tidak valid/terlambat tanpa izin sah.");
    }

    /**
     * Export attendance logs to Excel/CSV format neatly.
     */
    public function export(Request $request)
    {
        $fileName = 'Laporan_Presensi_Karyawan_'.date('Y-m-d_H-i').'.csv';

        $query = AttendanceLog::with('user', 'geofence');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('nip', 'LIKE', "%{$search}%");
                })
                    ->orWhere('type', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $logs = $query->latest('date')->latest('time')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Explicit Excel delimiter directive
            fwrite($file, "sep=;\n");

            // Header Row
            fputcsv($file, [
                'No',
                'NIP',
                'Nama Karyawan',
                'Jabatan',
                'Tanggal Presensi',
                'Waktu (WIB)',
                'Tipe Presensi',
                'Status Kehadiran',
                'Skor Match Biometrik',
                'Jarak Geofence (Meter)',
                'Koordinat Lat / Lng',
                'Lokasi Kantor',
                'Catatan',
            ], ';');

            // Data Rows
            foreach ($logs as $index => $log) {
                fputcsv($file, [
                    $index + 1,
                    $log->user->nip ?? '-',
                    $log->user->name ?? '-',
                    $log->user->position ?? '-',
                    Carbon::parse($log->date)->format('d/m/Y'),
                    $log->time,
                    $log->type,
                    $log->status,
                    $log->face_confidence_score ? $log->face_confidence_score.'%' : 'N/A',
                    $log->distance_from_office.' Meter',
                    "{$log->lat}, {$log->lng}",
                    $log->geofence->office_name ?? 'Kantor Utama',
                    $log->notes ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
