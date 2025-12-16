<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'nama',
        'mulai',
        'selesai',
        'keterangan',
        'tahun_akademik',
        'semester',
        'is_arsip',
        'diarsipkan_pada',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
        'is_arsip' => 'boolean',
        'diarsipkan_pada' => 'datetime',
    ];

    /**
     * Get peserta (mahasiswa) for this jadwal
     */
    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'jadwal_mahasiswa')
            ->withTimestamps();
    }

    /**
     * Get all nilai for this jadwal
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Check if jadwal is active (currently running)
     */
    public function isActive(): bool
    {
        $now = now();
        return $now->between($this->mulai, $this->selesai);
    }

    /**
     * Scope untuk jadwal aktif (tidak diarsip)
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_arsip', false);
    }

    /**
     * Scope untuk jadwal yang diarsip
     */
    public function scopeArsip(Builder $query): Builder
    {
        return $query->where('is_arsip', true);
    }

    /**
     * Get label tahun akademik lengkap
     */
    public function getLabelTahunAkademikAttribute(): ?string
    {
        if (!$this->tahun_akademik) return null;
        return $this->tahun_akademik . ' ' . ucfirst($this->semester ?? '');
    }

    /**
     * Get nama dengan label arsip
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama . ($this->is_arsip ? ' (Arsip)' : '');
    }
}
