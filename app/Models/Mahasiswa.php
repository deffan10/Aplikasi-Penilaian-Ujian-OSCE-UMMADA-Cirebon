<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'kelas_id',
        'foto',
    ];

    /**
     * Get kelas this mahasiswa belongs to
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get all nilai for this mahasiswa
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Get jadwal this mahasiswa is assigned to
     */
    public function jadwal(): BelongsToMany
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_mahasiswa')
            ->withTimestamps();
    }

    /**
     * Get gelombang this mahasiswa is assigned to
     */
    public function gelombang(): BelongsToMany
    {
        return $this->belongsToMany(Gelombang::class, 'gelombang_mahasiswa')
            ->withTimestamps();
    }
}
