<?php

namespace App\Http\Controllers\Penguji;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $assignedStasi = $user->assignedStasi()
            ->where('stasi.aktif', true)
            ->wherePivot('aktif', true)
            ->get();
        
        $totalDinilai = \App\Models\Nilai::where('penguji_id', $user->id)->count();

        $jadwalAktif = \App\Models\Jadwal::where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->count();

        $jadwalList = \App\Models\Jadwal::where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->with('peserta')
            ->get();

        return view('penguji.dashboard', compact('assignedStasi', 'totalDinilai', 'jadwalAktif', 'jadwalList'));
    }
}
