<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomponenStasi extends Model
{
    protected $table = 'komponen_stasi';

    protected $fillable = [
        'stasi_id',
        'nama',
        'bobot',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'urutan' => 'integer',
    ];

    /**
     * Get stasi this komponen belongs to
     */
    public function stasi(): BelongsTo
    {
        return $this->belongsTo(Stasi::class);
    }
}
