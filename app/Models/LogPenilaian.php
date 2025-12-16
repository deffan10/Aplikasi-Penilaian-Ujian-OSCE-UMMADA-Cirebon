<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPenilaian extends Model
{
    protected $table = 'log_penilaian';

    protected $fillable = [
        'nilai_id',
        'penguji_id',
        'catatan',
    ];

    /**
     * Get nilai header
     */
    public function nilai(): BelongsTo
    {
        return $this->belongsTo(Nilai::class);
    }

    /**
     * Get penguji (user) who wrote this log
     */
    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
