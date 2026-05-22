<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MahasiswaImport;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('kelas');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mahasiswa = $query->orderBy('nama')->paginate(20);
        $kelasList = Kelas::orderBy('kode')->get();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim',
            'nama' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        Mahasiswa::create($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'kelasList'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($mahasiswa->foto && \Storage::disk('public')->exists($mahasiswa->foto)) {
                \Storage::disk('public')->delete($mahasiswa->foto);
            }

            $data['foto'] = $this->compressAndSavePhoto(
                $request->file('foto')->getRealPath(),
                $mahasiswa->nim
            );
        } else {
            unset($data['foto']);
        }

        $mahasiswa->update($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil diupdate.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function importForm()
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.import', compact('kelasList'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        try {
            Excel::import(new MahasiswaImport($request->kelas_id), $request->file('file'));
            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Import mahasiswa berhasil.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Show upload foto form
     */
    public function uploadFotoForm()
    {
        return view('admin.mahasiswa.upload-foto');
    }

    /**
     * Process bulk photo upload (ZIP file with NIM as filename)
     */
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:51200', // max 50MB
        ]);

        $zipFile = $request->file('file');
        $zip = new \ZipArchive;

        if ($zip->open($zipFile->getRealPath()) !== true) {
            return back()->with('error', 'Gagal membuka file ZIP.');
        }

        $uploaded = 0;
        $skipped = [];
        $storagePath = storage_path('app/public/foto-mahasiswa');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            
            // Skip directories and hidden files
            if (str_ends_with($filename, '/') || str_starts_with(basename($filename), '.')) {
                continue;
            }

            // Get extension and NIM from filename
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $skipped[] = "{$filename}: format tidak didukung (hanya jpg/png)";
                continue;
            }

            $nim = pathinfo(basename($filename), PATHINFO_FILENAME);

            // Find mahasiswa by NIM
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();
            if (!$mahasiswa) {
                $skipped[] = "{$filename}: NIM '{$nim}' tidak ditemukan";
                continue;
            }

            // Delete old photo if exists
            if ($mahasiswa->foto && file_exists(storage_path('app/public/' . $mahasiswa->foto))) {
                unlink(storage_path('app/public/' . $mahasiswa->foto));
            }

            // Extract to temp, compress, then save
            $tempPath = sys_get_temp_dir() . '/' . uniqid('foto_') . '.' . $ext;
            $content = $zip->getFromIndex($i);
            file_put_contents($tempPath, $content);

            $savedPath = $this->compressAndSavePhoto($tempPath, $nim);
            unlink($tempPath);

            if ($savedPath) {
                $mahasiswa->update(['foto' => $savedPath]);
                $uploaded++;
            } else {
                $skipped[] = "{$filename}: gagal compress foto";
            }
        }

        $zip->close();

        $message = "Berhasil upload {$uploaded} foto.";
        if (count($skipped) > 0) {
            return redirect()->route('admin.mahasiswa.index')
                ->with('success', $message)
                ->with('import_errors', $skipped);
        }

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', $message);
    }

    /**
     * Update foto for individual mahasiswa
     */
    public function updateFoto(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Delete old photo
        if ($mahasiswa->foto && \Storage::disk('public')->exists($mahasiswa->foto)) {
            \Storage::disk('public')->delete($mahasiswa->foto);
        }

        $path = $this->compressAndSavePhoto(
            $request->file('foto')->getRealPath(),
            $mahasiswa->nim
        );

        $mahasiswa->update(['foto' => $path]);

        return back()->with('success', 'Foto berhasil diupdate.');
    }

    /**
     * Compress and save photo to storage
     * Resize to max 400px width, convert to JPEG quality 70
     */
    private function compressAndSavePhoto(string $sourcePath, string $nim): ?string
    {
        $storagePath = storage_path('app/public/foto-mahasiswa');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            return null;
        }

        $mime = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Create image resource from source
        switch ($mime) {
            case 'image/jpeg':
                $source = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = @imagecreatefrompng($sourcePath);
                break;
            default:
                return null;
        }

        if (!$source) {
            return null;
        }

        // Resize to max 400px width (keeping aspect ratio)
        $maxWidth = 400;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG before converting
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save as JPEG with quality 70 (good balance between size and quality)
        $filename = $nim . '.jpg';
        $destPath = $storagePath . '/' . $filename;
        imagejpeg($resized, $destPath, 70);

        // Free memory
        imagedestroy($source);
        imagedestroy($resized);

        return 'foto-mahasiswa/' . $filename;
    }
}
