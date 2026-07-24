<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    /**
     * Display a listing of attendance logs (Log Presensi) with search and date filtering.
     */
    public function index(Request $request)
    {
        $query = AttendanceLog::with('user', 'geofence');

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
                ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Calculate summary metrics based on filtered query or overall
        $totalLogs = AttendanceLog::count();
        $checkInLogs = AttendanceLog::where('type', 'MASUK')->count();
        $checkOutLogs = AttendanceLog::where('type', 'PULANG')->count();
        $outOfRadiusLogs = AttendanceLog::where('is_outside_radius', true)->count();

        $perPage = $request->input('per_page', 5);
        $logs = $query->latest('date')->latest('time')->paginate($perPage)->withQueryString();

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'totalLogs' => $totalLogs,
                'checkInLogs' => $checkInLogs,
                'checkOutLogs' => $checkOutLogs,
                'outOfRadiusLogs' => $outOfRadiusLogs,
                'logs' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'user_name' => $log->user->name ?? 'Karyawan',
                        'nip' => $log->user->nip ?? '-',
                        'date' => \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y'),
                        'time' => $log->time,
                        'type' => $log->type,
                        'status' => $log->status,
                        'face_confidence' => $log->face_confidence_score,
                        'distance' => $log->distance_from_office,
                        'is_outside_radius' => $log->is_outside_radius,
                        'lat' => $log->lat,
                        'lng' => $log->lng,
                        'photo_url' => $log->photo_path ? asset($log->photo_path) : null,
                        'notes' => $log->notes,
                        'registered_face_url' => ($log->user && $log->user->registered_face_photo) ? asset($log->user->registered_face_photo) : null,
                    ];
                }),
            ]);
        }

        return view('admin.attendance_logs.index', compact(
            'totalLogs',
            'checkInLogs',
            'checkOutLogs',
            'outOfRadiusLogs',
            'logs'
        ));
    }

    /**
     * Export attendance logs to Excel/CSV format neatly.
     */
    public function export(Request $request)
    {
        $fileName = 'Laporan_Presensi_Karyawan_' . date('Y-m-d_H-i') . '.csv';

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
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fputs($file, "\xEF\xBB\xBF");

            // Explicit Excel delimiter directive
            fputs($file, "sep=;\n");

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
                'Catatan'
            ], ';');

            // Data Rows
            foreach ($logs as $index => $log) {
                fputcsv($file, [
                    $index + 1,
                    $log->user->nip ?? '-',
                    $log->user->name ?? '-',
                    $log->user->position ?? '-',
                    \Carbon\Carbon::parse($log->date)->format('d/m/Y'),
                    $log->time,
                    $log->type,
                    $log->status,
                    $log->face_confidence_score ? $log->face_confidence_score . '%' : 'N/A',
                    $log->distance_from_office . ' Meter',
                    "{$log->lat}, {$log->lng}",
                    $log->geofence->office_name ?? 'Kantor Utama',
                    $log->notes ?? '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
