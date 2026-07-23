<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    /**
     * Display the geofencing and office location management page.
     */
    public function index()
    {
        return view('admin.geofences.index');
    }
}
