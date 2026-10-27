<?php

namespace App\Http\Controllers;

use App\Jobs\LogUserActivityJob;
use App\Models\ActivityLog;
use App\Models\LaporanSarpras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LaporanSarprasController extends Controller
{
    public function index(Request $request)
    {
        $laporan = LaporanSarpras::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $laporan,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $path = $request->file('foto')->store('laporan_sarpras', 'public');

            $laporan = LaporanSarpras::create([
                'user_id' => $request->user()->id,
                'nama_ruangan' => $validated['nama_ruangan'],
                'kondisi' => $validated['kondisi'],
                'foto_path' => $path,
                'status_verifikasi' => 'pending',
            ]);

            LogUserActivityJob::dispatch(
                userId: $request->user()->id,
                action: 'laporan_sarpras_dibuat',
                description: "Sekolah melaporkan kondisi ruangan {$validated['nama_ruangan']}",
            );

            return response()->json([
                'data' => $laporan,
            ], 201);
        });
    }
}
