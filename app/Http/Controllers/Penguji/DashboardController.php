<?php

namespace App\Http\Controllers\Penguji;

use App\Http\Controllers\Controller;
use App\Models\GelombangPenguji;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get assigned stasi (legacy)
        $assignedStasi = $user->assignedStasi()
            ->where('stasi.aktif', true)
            ->wherePivot('aktif', true)
            ->get();
        
        $totalDinilai = \App\Models\Nilai::where('penguji_id', $user->id)->count();

        $jadwalAktif = \App\Models\Jadwal::where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->where('is_arsip', false)
            ->count();

        $jadwalList = \App\Models\Jadwal::where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->where('is_arsip', false)
            ->withCount('gelombang')
            ->get();

        // Count gelombang where this penguji is assigned
        $gelombangCount = GelombangPenguji::where('penguji_id', $user->id)
            ->whereHas('gelombang.jadwal', function($q) {
                $q->where('is_arsip', false);
            })
            ->distinct('gelombang_id')
            ->count('gelombang_id');

        return view('penguji.dashboard', compact('assignedStasi', 'totalDinilai', 'jadwalAktif', 'jadwalList', 'gelombangCount'));
    }
}
