<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\SyaratLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'syarat' => 'required|array|min:1',
            'syarat.*.nama_syarat' => 'required|string|max:255',
            'syarat.*.tipe_file' => 'nullable|string|max:50',
        ]);

        return DB::transaction(function () use ($validated) {
            $layanan = Layanan::create([
                'nama' => $validated['nama'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            foreach ($validated['syarat'] as $syarat) {
                SyaratLayanan::create([
                    'layanan_id' => $layanan->id,
                    'nama_syarat' => $syarat['nama_syarat'],
                    'tipe_file' => $syarat['tipe_file'] ?? 'pdf',
                ]);
            }

            return response()->json([
                'data' => $layanan->load('syarat'),
            ], 201);
        });
    }

    public function index()
    {
        return response()->json([
            'data' => Layanan::with('syarat')->get(),
        ]);
    }
}
