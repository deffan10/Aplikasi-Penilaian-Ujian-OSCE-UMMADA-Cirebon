<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gelombang extends Model
{
    protected $table = 'gelombang';

    protected $fillable = [
        'jadwal_id',
        'nama',
        'waktu_mulai',
        'waktu_selesai',
        'urutan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    /**
     * Get jadwal for this gelombang
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    /**
     * Get mahasiswa peserta gelombang ini
     */
    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'gelombang_mahasiswa')
            ->withTimestamps();
    }

    /**
     * Get penguji per stasi untuk gelombang ini
     */
    public function pengujiStasi(): HasMany
    {
        return $this->hasMany(GelombangPenguji::class);
    }

    /**
     * Get nilai for this gelombang
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Get penguji untuk stasi tertentu
     */
    public function getPengujiForStasi($stasiId)
    {
        $gp = $this->pengujiStasi()->where('stasi_id', $stasiId)->first();
        return $gp ? $gp->penguji : null;
    }

    /**
     * Get waktu lengkap
     */
    public function getWaktuLengkapAttribute(): string
    {
        if ($this->waktu_mulai && $this->waktu_selesai) {
            return $this->waktu_mulai->format('H:i') . ' - ' . $this->waktu_selesai->format('H:i');
        }
        return '-';
    }

    /**
     * Check if gelombang has started (based on jadwal date + waktu_mulai)
     */
    public function hasStarted(): bool
    {
        // If no waktu_mulai set, always allow
        if (!$this->waktu_mulai) {
            return true;
        }

        $jadwal = $this->jadwal;
        if (!$jadwal || !$jadwal->mulai) {
            return true;
        }

        // Combine jadwal date with gelombang time
        $startDateTime = $jadwal->mulai->copy()
            ->setTimeFromTimeString($this->waktu_mulai->format('H:i:s'));

        return now() >= $startDateTime;
    }

    /**
     * Check if gelombang has ended (based on jadwal date + waktu_selesai)
     */
    public function hasEnded(): bool
    {
        // If no waktu_selesai set, check jadwal selesai
        if (!$this->waktu_selesai) {
            return $this->jadwal && $this->jadwal->selesai && now() > $this->jadwal->selesai;
        }

        $jadwal = $this->jadwal;
        if (!$jadwal || !$jadwal->mulai) {
            return false;
        }

        // Combine jadwal date with gelombang time
        $endDateTime = $jadwal->mulai->copy()
            ->setTimeFromTimeString($this->waktu_selesai->format('H:i:s'));

        return now() > $endDateTime;
    }

    /**
     * Check if gelombang is currently active (between start and end time)
     */
    public function isActive(): bool
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * Check if penilaian is allowed (started and not ended)
     */
    public function canInputNilai(): bool
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * Get status text
     */
    public function getStatusWaktu(): array
    {
        if (!$this->hasStarted()) {
            $jadwal = $this->jadwal;
            $startDateTime = $jadwal->mulai->copy()
                ->setTimeFromTimeString($this->waktu_mulai->format('H:i:s'));
            
            return [
                'status' => 'belum_mulai',
                'label' => 'Belum Dimulai',
                'color' => 'yellow',
                'message' => 'Gelombang dimulai pada ' . $startDateTime->format('d M Y H:i'),
                'start_time' => $startDateTime,
            ];
        }

        if ($this->hasEnded()) {
            return [
                'status' => 'selesai',
                'label' => 'Sudah Selesai',
                'color' => 'gray',
                'message' => 'Waktu gelombang sudah berakhir',
            ];
        }

        return [
            'status' => 'aktif',
            'label' => 'Sedang Berlangsung',
            'color' => 'green',
            'message' => 'Gelombang sedang aktif',
        ];
    }
}
