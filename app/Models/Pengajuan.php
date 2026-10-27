<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $fillable = ['layanan_id', 'user_id', 'status', 'tanggal_pengajuan'];
    protected $attributes = [
        'status' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class, 'pengajuan_id');
    }
}
