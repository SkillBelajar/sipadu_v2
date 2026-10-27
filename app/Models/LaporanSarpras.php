<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanSarpras extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nama_ruangan', 'kondisi', 'foto_path', 'status_verifikasi'];
    protected $attributes = [
        'status_verifikasi' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
