<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $fillable = [
        'jadwal_id',
        'stasi_id',
        'mahasiswa_id',
        'penguji_id',
        'global_rating_id',
        'total_nilai',
        'nilai_aktual',
        'lulus_stasi',
        'catatan',
    ];

    protected $casts = [
        'total_nilai' => 'decimal:2',
        'nilai_aktual' => 'decimal:2',
        'lulus_stasi' => 'boolean',
    ];

    /**
     * Get jadwal for this nilai
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    /**
     * Get stasi for this nilai
     */
    public function stasi(): BelongsTo
    {
        return $this->belongsTo(Stasi::class);
    }

    /**
     * Get mahasiswa for this nilai
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * Get penguji (user) for this nilai
     */
    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }

    /**
     * Get global rating for this nilai
     */
    public function globalRating(): BelongsTo
    {
        return $this->belongsTo(GlobalRating::class);
    }

    /**
     * Get detail nilai (per komponen)
     */
    public function detail(): HasMany
    {
        return $this->hasMany(NilaiDetail::class);
    }

    /**
     * Get log penilaian
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LogPenilaian::class);
    }

    /**
     * Get nilai acuan for this stasi in this jadwal
     */
    public function getNilaiAcuanAttribute(): ?float
    {
        $nilaiAcuan = NilaiAcuanStasi::where('jadwal_id', $this->jadwal_id)
            ->where('stasi_id', $this->stasi_id)
            ->first();
        
        return $nilaiAcuan ? (float) $nilaiAcuan->nilai_acuan : null;
    }
}
