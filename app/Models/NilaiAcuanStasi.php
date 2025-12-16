<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiAcuanStasi extends Model
{
    protected $table = 'nilai_acuan_stasi';

    protected $fillable = [
        'jadwal_id',
        'stasi_id',
        'nilai_acuan',
        'intercept',
        'slope',
        'sample_count',
        'calculated_at',
    ];

    protected $casts = [
        'nilai_acuan' => 'decimal:2',
        'intercept' => 'decimal:4',
        'slope' => 'decimal:4',
        'sample_count' => 'integer',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get jadwal for this nilai acuan
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    /**
     * Get stasi for this nilai acuan
     */
    public function stasi(): BelongsTo
    {
        return $this->belongsTo(Stasi::class);
    }
}
