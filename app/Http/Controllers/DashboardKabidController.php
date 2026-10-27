<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LaporanSarpras;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardKabidController extends Controller
{
    public function pengajuan(Request $request)
    {
        $data = Pengajuan::with(['user', 'layanan'])->latest()->get();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function sarpras(Request $request)
    {
        $rekapitulasi = LaporanSarpras::selectRaw('kondisi, count(*) as total')
            ->groupBy('kondisi')
            ->get();

        $data = LaporanSarpras::with('user')->latest()->get();

        return response()->json([
            'rekapitulasi' => $rekapitulasi,
            'data' => $data,
        ]);
    }

    public function verify(Request $request, LaporanSarpras $laporan_sarpras)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:terverifikasi',
        ]);

        return DB::transaction(function () use ($request, $laporan_sarpras, $validated) {
            $laporan_sarpras->update([
                'status_verifikasi' => $validated['status_verifikasi'],
            ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'sarpras_diverifikasi',
                'description' => "Kabid verifikasi laporan sarpras ID {$laporan_sarpras->id} ({$laporan_sarpras->nama_ruangan})",
            ]);

            return response()->json([
                'data' => $laporan_sarpras->refresh(),
            ]);
        });
    }
}
