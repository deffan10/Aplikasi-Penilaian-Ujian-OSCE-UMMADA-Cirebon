<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'kode',
        'nama',
        'tahun_akademik',
        'semester',
        'is_arsip',
        'diarsipkan_pada',
    ];

    protected $casts = [
        'is_arsip' => 'boolean',
        'diarsipkan_pada' => 'datetime',
    ];

    /**
     * Get all mahasiswa in this kelas
     */
    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * Scope untuk kelas aktif (tidak diarsip)
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_arsip', false);
    }

    /**
     * Scope untuk kelas yang diarsip
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
}
