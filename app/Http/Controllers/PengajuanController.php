<?php

namespace App\Http\Controllers;

use App\Jobs\LogUserActivityJob;
use App\Models\ActivityLog;
use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\SyaratLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PengajuanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf|max:5120',
        ]);

        $layanan = \App\Models\Layanan::findOrFail($validated['layanan_id']);
        $requiredSyarat = $layanan->syarat;

        if (count($validated['files']) !== $requiredSyarat->count()) {
            return response()->json([
                'message' => 'File harus disertakan untuk setiap syarat layanan.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $validated, $layanan, $requiredSyarat) {
            $pengajuan = Pengajuan::create([
                'layanan_id' => $validated['layanan_id'],
                'user_id' => $request->user()->id,
                'status' => 'pending',
            ]);

            $syarats = $requiredSyarat->values()->all();

            foreach ($validated['files'] as $index => $file) {
                $syarat = $syarats[$index] ?? null;
                if (! $syarat) {
                    continue;
                }

                $path = $file->store('dokumen_pengajuan', 'public');

                DokumenPengajuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'syarat_id' => $syarat->id,
                    'file_path' => $path,
                    'status' => 'pending',
                ]);
            }

            LogUserActivityJob::dispatch(
                userId: $request->user()->id,
                action: 'created_pengajuan',
                description: "Pengajuan #{$pengajuan->id} dibuat untuk {$layanan->nama}",
            );

            return response()->json([
                'data' => $pengajuan->load('dokumen'),
            ], 201);
        });
    }
}
