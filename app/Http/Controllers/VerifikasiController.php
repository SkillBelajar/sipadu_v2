<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifikasiController extends Controller
{
    public function update(Request $request, DokumenPengajuan $dokumen_pengajuan)
    {
        $validated = $request->validate([
            'status' => 'required|in:valid,revisi',
            'catatan_verifikator' => 'required_if:status,revisi|nullable|string',
        ]);

        return DB::transaction(function () use ($request, $dokumen_pengajuan, $validated) {
            $dokumen = $dokumen_pengajuan;
            $dokumen->update([
                'status' => $validated['status'],
                'catatan_verifikator' => $validated['catatan_verifikator'] ?? null,
            ]);

            $pengajuan = $dokumen->pengajuan;
            $siblings = $pengajuan->dokumen;

            if ($siblings->contains('status', 'revisi')) {
                $pengajuan->update(['status' => 'revisi']);
            } elseif ($siblings->every(fn ($d) => $d->status === 'valid')) {
                $pengajuan->update(['status' => 'disetujui']);
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'verifikasi_dokumen',
                'description' => "Verifikator memverifikasi dokumen ID {$dokumen->id} sebagai {$validated['status']}",
            ]);

            return response()->json([
                'data' => $dokumen->refresh(),
                'pengajuan_status' => $pengajuan->fresh()->status,
            ]);
        });
    }
}
