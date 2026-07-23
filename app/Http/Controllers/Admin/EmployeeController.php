<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees (Data Karyawan).
     */
    public function index()
    {
        return view('admin.employees.index');
    }
}
