<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalRating extends Model
{
    protected $table = 'global_ratings';

    protected $fillable = [
        'kode',
        'nilai',
        'label',
        'deskripsi',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    /**
     * Check if this is the borderline rating (nilai = 2)
     */
    public function isBorderline(): bool
    {
        return $this->nilai === 2;
    }

    /**
     * Check if this is passing (nilai >= 2)
     */
    public function isPassing(): bool
    {
        return $this->nilai >= 2;
    }
}
