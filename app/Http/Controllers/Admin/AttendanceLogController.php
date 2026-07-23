<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    /**
     * Display a listing of attendance logs (Log Presensi).
     */
    public function index()
    {
        return view('admin.attendance_logs.index');
    }
}
