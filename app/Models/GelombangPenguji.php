<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GelombangPenguji extends Model
{
    protected $table = 'gelombang_penguji';

    protected $fillable = [
        'gelombang_id',
        'stasi_id',
        'penguji_id',
    ];

    /**
     * Get gelombang
     */
    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(Gelombang::class);
    }

    /**
     * Get stasi
     */
    public function stasi(): BelongsTo
    {
        return $this->belongsTo(Stasi::class);
    }

    /**
     * Get penguji (user)
     */
    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
