<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyaratLayanan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'syarat_layanan';
    protected $fillable = ['layanan_id', 'nama_syarat', 'tipe_file'];
    protected $attributes = [
        'tipe_file' => 'pdf',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
