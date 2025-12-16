<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $kelasId;

    public function __construct(int $kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function model(array $row)
    {
        return new Mahasiswa([
            'nim' => $row['nim'],
            'nama' => $row['nama'],
            'kelas_id' => $this->kelasId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|unique:mahasiswa,nim',
            'nama' => 'required|string|max:100',
        ];
    }
}
