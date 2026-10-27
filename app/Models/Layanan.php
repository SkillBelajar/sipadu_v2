<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Layanan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'layanan';
    protected $fillable = ['nama', 'deskripsi', 'is_active'];
    protected $attributes = [
        'is_active' => true,
    ];

    public function syarat(): HasMany
    {
        return $this->hasMany(SyaratLayanan::class, 'layanan_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
