<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_mahasiswa' => \App\Models\Mahasiswa::count(),
            'total_stasi' => \App\Models\Stasi::where('aktif', true)->count(),
            'total_penguji' => \App\Models\User::where('role', 'penguji')->count(),
            'total_jadwal' => \App\Models\Jadwal::count(),
            'total_nilai' => \App\Models\Nilai::count(),
        ];

        $recentJadwal = \App\Models\Jadwal::orderBy('mulai', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentJadwal'));
    }
}
