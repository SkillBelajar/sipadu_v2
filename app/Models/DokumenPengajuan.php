<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPengajuan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pengajuan';
    protected $fillable = ['pengajuan_id', 'syarat_id', 'file_path', 'status', 'catatan_verifikator'];
    protected $attributes = [
        'status' => 'pending',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function syarat(): BelongsTo
    {
        return $this->belongsTo(SyaratLayanan::class, 'syarat_id');
    }
}
