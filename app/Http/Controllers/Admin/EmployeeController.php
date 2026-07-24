<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees (Data Karyawan) with search support.
     */
    public function index(Request $request)
    {
        $totalEmployees = User::count();
        $activeEmployees = User::where('account_status', 'Aktif')->count();
        $pendingBiometric = User::where('account_status', 'Pending Biometrik')->count();
        $inactiveEmployees = User::where('account_status', 'Non-Aktif')->count();

        $nextNip = User::generateNextNip();

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%")
                  ->orWhere('position', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%")
                  ->orWhere('account_status', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->latest()->get();

        return view('admin.employees.index', compact(
            'totalEmployees',
            'activeEmployees',
            'pendingBiometric',
            'inactiveEmployees',
            'nextNip',
            'employees'
        ));
    }

    /**
     * Export employees data to Excel/CSV file format neatly.
     */
    public function export(Request $request)
    {
        $fileName = 'Data_Karyawan_AbsensikuPro_' . date('Y-m-d_H-i') . '.csv';

        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%")
                  ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->latest()->get();

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Tell Microsoft Excel explicitly to use semicolon delimiter for separate columns
            fputs($file, "sep=;\n");

            // Header Row (Semicolon separated for Excel columns)
            fputcsv($file, [
                'No',
                'NIP',
                'Nama Lengkap',
                'Jabatan / Tim',
                'Role Akses',
                'Status Akun',
                'Tanggal Terdaftar'
            ], ';');

            // Data Rows
            foreach ($employees as $index => $emp) {
                fputcsv($file, [
                    $index + 1,
                    $emp->nip,
                    $emp->name,
                    $emp->position ?? '-',
                    $emp->role === 'admin' ? 'Administrator HR' : 'Karyawan',
                    $emp->account_status,
                    $emp->created_at ? $emp->created_at->format('d/m/Y H:i') : '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store a newly created employee in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'position' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $nip = User::generateNextNip();

        User::create([
            'nip' => $nip,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'account_status' => 'Pending Biometrik',
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', "Karyawan baru '{$validated['name']}' berhasil ditambahkan dengan NIP {$nip} dan status Pending Biometrik!");
    }

    /**
     * Update employee data (Name & Position only).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'position' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama karyawan wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->position = $validated['position'] ?? null;
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('admin.employees.index')
            ->with('success', "Data karyawan '{$user->name}' (NIP: {$user->nip}) berhasil diperbarui!");
    }

    /**
     * Permanently delete an employee and their related attendance logs from MySQL database.
     */
    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $name = $user->name;

        // Delete associated attendance logs first
        $user->attendanceLogs()->delete();

        // Force delete row from users table permanently
        $user->forceDelete();

        return redirect()->route('admin.employees.index')
            ->with('success', "Akun karyawan '{$name}' telah berhasil dihapus permanen dari database!");
    }
}
