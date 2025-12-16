<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiDetail extends Model
{
    protected $table = 'nilai_detail';

    protected $fillable = [
        'nilai_id',
        'komponen_stasi_id',
        'skor',
    ];

    protected $casts = [
        'skor' => 'decimal:2',
    ];

    /**
     * Get nilai header
     */
    public function nilai(): BelongsTo
    {
        return $this->belongsTo(Nilai::class);
    }

    /**
     * Get komponen stasi
     */
    public function komponen(): BelongsTo
    {
        return $this->belongsTo(KomponenStasi::class, 'komponen_stasi_id');
    }
}
