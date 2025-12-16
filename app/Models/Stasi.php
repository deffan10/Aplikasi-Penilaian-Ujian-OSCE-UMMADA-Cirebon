<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stasi extends Model
{
    protected $table = 'stasi';

    protected $fillable = [
        'nama',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Get komponen penilaian for this stasi
     */
    public function komponens(): HasMany
    {
        return $this->hasMany(KomponenStasi::class)->orderBy('urutan');
    }

    /**
     * Get penguji assigned to this stasi
     */
    public function penguji(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'penguji_stasi')
            ->withPivot('aktif')
            ->withTimestamps();
    }

    /**
     * Get all nilai for this stasi
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Get total bobot for all komponen
     */
    public function getTotalBobotAttribute(): int
    {
        return $this->komponen->sum('bobot');
    }
}
