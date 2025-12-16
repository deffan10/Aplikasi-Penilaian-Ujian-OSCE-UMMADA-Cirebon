# OSCE Penilaian – Desain Teknis Proyek (Laravel 11)

Dokumen ini berfungsi sebagai rancangan teknis lengkap untuk aplikasi penilaian OSCE (Farmasi) dengan dua role: Admin dan Penguji. Bahasa dibuat sederhana seperti mahasiswa, tetapi tetap teknis agar mudah diimplementasikan.

---

## 1) Ringkasan
- Backend: Laravel 11 (PHP 8.2+)
- Frontend: Laravel Blade + Tailwind CSS + Eldora UI
- Auth: Laravel Breeze (session-based), role `admin` dan `penguji`
- DB: MySQL 8
- Export PDF: barryvdh/laravel-dompdf
- Export Excel: maatwebsite/excel
- Target: Ringan, aman (login + policy), cepat, dan mudah dikembangkan

---

## 2) Arsitektur Aplikasi
- Pola: MVC + Service Layer + Policy
- Modularisasi ringan:
  - `app/Models/*` – Model Eloquent
  - `app/Http/Controllers/Admin/*` – Manajemen data (kelas, mahasiswa, stasi, komponen, jadwal, rekap)
  - `app/Http/Controllers/Penguji/*` – Penilaian oleh penguji
  - `app/Services/*` – Logika domain (contoh: perhitungan bobot)
  - `app/Policies/*` – Batasan akses dan wewenang
  - `resources/views/*` – Blade + Eldora UI
- Alur request: Route -> Middleware `auth` -> Controller -> Service/Model -> View/Response (PDF/Excel jika export)

---

## 3) Flow Utama Aplikasi
1. Login via Breeze:
   - Admin: akses penuh pengaturan & rekap
   - Penguji: hanya menilai stasi yang ditugaskan
2. Admin setup:
   - Kelola Kelas, Mahasiswa (import Excel)
   - Kelola Stasi dan Komponen (dinamis, bobot per komponen)
   - Assign Penguji ke Stasi
   - Buat Jadwal OSCE dan daftarkan Mahasiswa ke Jadwal
3. Penguji menilai:
   - Melihat daftar stasi (read-only untuk yang tidak ditugaskan)
   - Live update kalo bisa
   - Menilai pada stasi yang ditugaskan: input nilai per komponen (0–100) + Global Rating + catatan
   - Sistem hitung skor total berbasis bobot
4. Rekap & Export:
   - Rekap per Kelas, per Jadwal, dan Total
   - Kolom: No | NIM | Nama | Stasi 1 | Stasi 2 | ... | Global Rating | Nama Penguji
   - Global Rating Pilihannya "Lulus" atau "Tidak Lulus"
   - Export PDF (dengan kop surat yang dikelola admin) dan Excel

---

## 4) Desain Database
Tabel inti (dengan relasi):
- `users` (role: `admin`/`penguji`)
- `kelas`
- `mahasiswa` (belongsTo kelas)
- `stasi`
- `komponen_stasi` (belongsTo stasi)
- `penguji_stasi` (pivot: user penguji <-> stasi)
- `jadwal`
- `jadwal_mahasiswa` (pivot: mahasiswa <-> jadwal)
- `global_ratings` (lookup: LULUS, BORDERLINE, TIDAK_LULUS)
- `nilai` (header penilaian: belongsTo jadwal, stasi, mahasiswa, penguji, global_rating)
- `nilai_detail` (detail per komponen)
- `log_penilaian` (catatan/counter-narrative per mahasiswa)
- `settings` (opsional; simpan kop surat dan pengaturan lain)

Catatan: Unik perekaman nilai oleh penguji pada kombinasi (jadwal, stasi, mahasiswa, penguji).

Relasi ringkas:
- User(1..n)-< penguji_stasi >- (n..1)Stasi
- Kelas(1..n)-Mahasiswa
- Jadwal(1..n)-< jadwal_mahasiswa >- (n..1)Mahasiswa
- Stasi(1..n)-KomponenStasi
- Nilai(1..n)-NilaiDetail, dan Nilai belongsTo (Jadwal, Stasi, Mahasiswa, Penguji(User), GlobalRating)
- LogPenilaian belongsTo (Nilai, Penguji(User))

Validasi bobot:
- Bobot komponen disimpan sebagai persentase integer (0–100). Disarankan sum(bobot) = 100 per stasi (divalidasi aplikasi).

Formula hitung nilai total (normalisasi bobot):
$$ Total = \frac{\sum_i skor_i \times bobot_i}{\sum_i bobot_i} $$

---

## 5) Migrations (Contoh Kode)
> Catatan: Di proyek nyata, setiap file ini dibuat terpisah di `database/migrations`.

```php
<?php
// 2025_01_01_000000_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'penguji'])->default('penguji');
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
```

```php
<?php
// 2025_01_01_000100_create_kelas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // ex: D3-3A
            $table->string('nama')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};
```

```php
<?php
// 2025_01_01_000200_create_mahasiswa_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mahasiswa'); }
};
```

```php
<?php
// 2025_01_01_000300_create_stasi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stasi'); }
};
```

```php
<?php
// 2025_01_01_000400_create_komponen_stasi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('komponen_stasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->string('nama');
            $table->unsignedTinyInteger('bobot'); // 0-100
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('komponen_stasi'); }
};
```

```php
<?php
// 2025_01_01_000500_create_penguji_stasi_table.php (pivot)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penguji_stasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->unique(['user_id','stasi_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('penguji_stasi'); }
};
```

```php
<?php
// 2025_01_01_000600_create_jadwal_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal'); }
};
```

```php
<?php
// 2025_01_01_000700_create_jadwal_mahasiswa_table.php (pivot)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['jadwal_id','mahasiswa_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_mahasiswa'); }
};
```

```php
<?php
// 2025_01_01_000800_create_global_ratings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('global_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // LULUS | BORDERLINE | TIDAK_LULUS
            $table->string('label');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('global_ratings'); }
};
```

```php
<?php
// 2025_01_01_000900_create_nilai_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('global_rating_id')->nullable()->constrained('global_ratings')->nullOnDelete();
            $table->decimal('total_nilai', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['jadwal_id','stasi_id','mahasiswa_id','penguji_id'], 'nilai_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('nilai'); }
};
```

```php
<?php
// 2025_01_01_001000_create_nilai_detail_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nilai_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai')->cascadeOnDelete();
            $table->foreignId('komponen_stasi_id')->constrained('komponen_stasi')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['nilai_id','komponen_stasi_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('nilai_detail'); }
};
```

```php
<?php
// 2025_01_01_001100_create_log_penilaian_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('log_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('log_penilaian'); }
};
```

```php
<?php
// 2025_01_01_001200_create_settings_table.php (opsional, untuk kop surat dll.)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
```

---

## 6) Model (Relasi Inti)
> Simpan di `app/Models/*`

```php
<?php // app/Models/User.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];

    public function assignedStasi()
    {
        return $this->belongsToMany(Stasi::class, 'penguji_stasi')->withTimestamps();
    }
}
```

```php
<?php // app/Models/Kelas.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['kode','nama'];

    public function mahasiswa()
    { return $this->hasMany(Mahasiswa::class); }
}
```

```php
<?php // app/Models/Mahasiswa.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $fillable = ['nim','nama','kelas_id'];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function nilai() { return $this->hasMany(Nilai::class); }
}
```

```php
<?php // app/Models/Stasi.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stasi extends Model
{
    protected $table = 'stasi';
    protected $fillable = ['nama','deskripsi','aktif'];

    public function komponen() { return $this->hasMany(KomponenStasi::class); }
    public function penguji() { return $this->belongsToMany(User::class, 'penguji_stasi')->withTimestamps(); }
    public function nilai() { return $this->hasMany(Nilai::class); }
}
```

```php
<?php // app/Models/KomponenStasi.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KomponenStasi extends Model
{
    protected $table = 'komponen_stasi';
    protected $fillable = ['stasi_id','nama','bobot','urutan'];

    public function stasi() { return $this->belongsTo(Stasi::class); }
}
```

```php
<?php // app/Models/Jadwal.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = ['nama','mulai','selesai','keterangan'];

    public function peserta() { return $this->belongsToMany(Mahasiswa::class, 'jadwal_mahasiswa')->withTimestamps(); }
    public function nilai() { return $this->hasMany(Nilai::class); }
}
```

```php
<?php // app/Models/GlobalRating.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GlobalRating extends Model
{
    protected $table = 'global_ratings';
    protected $fillable = ['kode','label'];
}
```

```php
<?php // app/Models/Nilai.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $fillable = ['jadwal_id','stasi_id','mahasiswa_id','penguji_id','global_rating_id','total_nilai'];

    public function jadwal() { return $this->belongsTo(Jadwal::class); }
    public function stasi() { return $this->belongsTo(Stasi::class); }
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function penguji() { return $this->belongsTo(User::class, 'penguji_id'); }
    public function globalRating() { return $this->belongsTo(GlobalRating::class); }
    public function detail() { return $this->hasMany(NilaiDetail::class); }
}
```

```php
<?php // app/Models/NilaiDetail.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NilaiDetail extends Model
{
    protected $table = 'nilai_detail';
    protected $fillable = ['nilai_id','komponen_stasi_id','skor'];

    public function nilai() { return $this->belongsTo(Nilai::class); }
    public function komponen() { return $this->belongsTo(KomponenStasi::class, 'komponen_stasi_id'); }
}
```

```php
<?php // app/Models/LogPenilaian.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LogPenilaian extends Model
{
    protected $table = 'log_penilaian';
    protected $fillable = ['nilai_id','penguji_id','catatan'];

    public function nilai() { return $this->belongsTo(Nilai::class); }
    public function penguji() { return $this->belongsTo(User::class, 'penguji_id'); }
}
```

```php
<?php // app/Models/Setting.php (opsional)
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key','value'];
}
```

---

## 7) Policy & Access Control
- Role di `users.role` menentukan akses dasar
- Gate untuk membatasi Penguji hanya bisa menilai stasi yang ditugaskan

```php
<?php // app/Providers/AuthServiceProvider.php
namespace App\Providers;
use App\Models\Stasi;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('menilai-stasi', function (User $user, Stasi $stasi) {
            if ($user->role === 'admin') return true;
            return $user->assignedStasi()->whereKey($stasi->id)->exists();
        });
    }
}
```

Penggunaan di controller:
```php
$this->authorize('menilai-stasi', $stasi);
```

---

## 8) Service: Perhitungan Nilai Berbobot
```php
<?php // app/Services/ScoreService.php
namespace App\Services;
use App\Models\KomponenStasi;

class ScoreService
{
    public function hitungTotal(array $komponenIdKeSkor): float
    {
        // $komponenIdKeSkor: [komponen_stasi_id => skor]
        $komponen = KomponenStasi::whereIn('id', array_keys($komponenIdKeSkor))->get(['id','bobot']);
        $sumBobot = max(1, (int) $komponen->sum('bobot'));
        $total = 0;
        foreach ($komponen as $k) {
            $skor = (float) ($komponenIdKeSkor[$k->id] ?? 0);
            $total += $skor * ($k->bobot / $sumBobot);
        }
        return round($total, 2);
    }
}
```

---

## 9) Controllers (Contoh Inti)

### Admin: CRUD Stasi & Komponen
```php
<?php // app/Http/Controllers/Admin/StasiController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Stasi;
use Illuminate\Http\Request;

class StasiController extends Controller
{
    public function index()
    { $stasi = Stasi::withCount('komponen')->orderBy('id')->paginate(20); return view('admin.stasi.index', compact('stasi')); }

    public function create()
    { return view('admin.stasi.create'); }

    public function store(Request $request)
    {
        $data = $request->validate(['nama'=>'required|string|max:150','deskripsi'=>'nullable|string','aktif'=>'boolean']);
        Stasi::create($data);
        return redirect()->route('admin.stasi.index')->with('ok','Stasi dibuat');
    }

    public function edit(Stasi $stasi)
    { return view('admin.stasi.edit', compact('stasi')); }

    public function update(Request $request, Stasi $stasi)
    {
        $data = $request->validate(['nama'=>'required|string|max:150','deskripsi'=>'nullable|string','aktif'=>'boolean']);
        $stasi->update($data);
        return redirect()->route('admin.stasi.index')->with('ok','Stasi diupdate');
    }

    public function destroy(Stasi $stasi)
    { $stasi->delete(); return back()->with('ok','Stasi dihapus'); }
}
```

```php
<?php // app/Http/Controllers/Admin/KomponenStasiController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Stasi;
use App\Models\KomponenStasi;
use Illuminate\Http\Request;

class KomponenStasiController extends Controller
{
    public function index(Stasi $stasi)
    { $komponen = $stasi->komponen()->orderBy('urutan')->get(); return view('admin.stasi.komponen.index', compact('stasi','komponen')); }

    public function store(Request $request, Stasi $stasi)
    {
        $data = $request->validate([
            'nama'=>'required|string|max:150',
            'bobot'=>'required|integer|min:0|max:100',
            'urutan'=>'nullable|integer|min:0'
        ]);
        $stasi->komponen()->create($data);
        return back()->with('ok','Komponen ditambah');
    }

    public function update(Request $request, Stasi $stasi, KomponenStasi $komponen)
    {
        $data = $request->validate([
            'nama'=>'required|string|max:150',
            'bobot'=>'required|integer|min:0|max:100',
            'urutan'=>'nullable|integer|min:0'
        ]);
        $komponen->update($data);
        return back()->with('ok','Komponen diupdate');
    }

    public function destroy(Stasi $stasi, KomponenStasi $komponen)
    { $komponen->delete(); return back()->with('ok','Komponen dihapus'); }
}
```

### Penguji: Penilaian
```php
<?php // app/Http/Controllers/Penguji/PenilaianController.php
namespace App\Http\Controllers\Penguji;
use App\Http\Controllers\Controller;
use App\Models\{Jadwal, Stasi, Mahasiswa, Nilai, NilaiDetail, GlobalRating, LogPenilaian};
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stasi = Stasi::with('komponen:id,stasi_id')->get();
        return view('penguji.stasi.index', compact('stasi','user'));
    }

    public function form(Stasi $stasi, Jadwal $jadwal, Mahasiswa $mahasiswa)
    {
        $this->authorize('menilai-stasi', $stasi);
        $komponen = $stasi->komponen()->orderBy('urutan')->get();
        $global = GlobalRating::orderBy('id')->get();
        return view('penguji.penilaian.form', compact('stasi','jadwal','mahasiswa','komponen','global'));
    }

    public function store(Request $request, Stasi $stasi, Jadwal $jadwal, Mahasiswa $mahasiswa, ScoreService $score)
    {
        $this->authorize('menilai-stasi', $stasi);
        $data = $request->validate([
            'nilai' => 'required|array',              // [komponen_id => skor]
            'nilai.*' => 'numeric|min:0|max:100',
            'global_rating_id' => 'required|exists:global_ratings,id',
            'catatan' => 'nullable|string'
        ]);

        $total = $score->hitungTotal($data['nilai']);
        $nilai = Nilai::updateOrCreate([
            'jadwal_id'=>$jadwal->id,
            'stasi_id'=>$stasi->id,
            'mahasiswa_id'=>$mahasiswa->id,
            'penguji_id'=>Auth::id(),
        ], [
            'global_rating_id'=>$data['global_rating_id'],
            'total_nilai'=>$total,
        ]);

        foreach ($data['nilai'] as $komponenId => $skor) {
            NilaiDetail::updateOrCreate([
                'nilai_id'=>$nilai->id,
                'komponen_stasi_id'=>$komponenId
            ], ['skor'=>$skor]);
        }

        if (!empty($data['catatan'])) {
            LogPenilaian::create([
                'nilai_id'=>$nilai->id,
                'penguji_id'=>Auth::id(),
                'catatan'=>$data['catatan']
            ]);
        }

        return back()->with('ok','Nilai tersimpan (total: '.$total.')');
    }
}
```

### Rekap & Export
```php
<?php // app/Http/Controllers/Admin/RekapController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Jadwal, Kelas, Mahasiswa, Nilai, Stasi, Setting};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function perKelas(Kelas $kelas)
    {
        $stasi = Stasi::orderBy('id')->get();
        $mhs = $kelas->mahasiswa()->with(['nilai.stasi','nilai.globalRating','nilai.penguji'])->get();
        return view('admin.rekap.kelas', compact('kelas','stasi','mhs'));
    }

    public function perJadwal(Jadwal $jadwal)
    {
        $stasi = Stasi::orderBy('id')->get();
        $peserta = $jadwal->peserta()->with(['nilai'=>function($q) use ($jadwal){
            $q->where('jadwal_id',$jadwal->id)->with(['stasi','globalRating','penguji']);
        }])->get();
        return view('admin.rekap.jadwal', compact('jadwal','stasi','peserta'));
    }

    public function exportPdfJadwal(Jadwal $jadwal)
    {
        $kop = optional(Setting::where('key','kop_surat_path')->first())->value; // path gambar
        $stasi = Stasi::orderBy('id')->get();
        $peserta = $jadwal->peserta()->with(['nilai'=>function($q) use ($jadwal){
            $q->where('jadwal_id',$jadwal->id)->with(['stasi','globalRating','penguji']);
        }])->get();
        $pdf = Pdf::loadView('admin.rekap.pdf', compact('jadwal','stasi','peserta','kop'));
        return $pdf->download('rekap-jadwal-'.$jadwal->id.'.pdf');
    }
}
```

Export Excel (Laravel Excel):
```php
<?php // app/Exports/RekapPerJadwalExport.php
namespace App\Exports;
use App\Models\{Jadwal, Stasi};
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapPerJadwalExport implements FromView
{
    public function __construct(public Jadwal $jadwal) {}

    public function view(): View
    {
        $stasi = Stasi::orderBy('id')->get();
        $peserta = $this->jadwal->peserta()->with(['nilai'=>function($q){
            $q->with(['stasi','globalRating','penguji']);
        }])->get();
        return view('admin.rekap.excel', ['jadwal'=>$this->jadwal,'stasi'=>$stasi,'peserta'=>$peserta]);
    }
}
```

Controller pemanggil Excel:
```php
<?php // app/Http/Controllers/Admin/ExportExcelController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Exports\RekapPerJadwalExport;
use App\Models\Jadwal;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
{
    public function perJadwal(Jadwal $jadwal)
    { return Excel::download(new RekapPerJadwalExport($jadwal), 'rekap-jadwal-'.$jadwal->id.'.xlsx'); }
}
```

---

## 10) Routes (Web)
```php
<?php // routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{StasiController, KomponenStasiController, RekapController, ExportExcelController};
use App\Http\Controllers\Penguji\PenilaianController;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function(){
    // Admin Area
    Route::middleware('can:admin-only')->group(function(){
        Route::prefix('admin')->name('admin.')->group(function(){
            Route::resource('stasi', StasiController::class)->except(['show']);
            Route::get('stasi/{stasi}/komponen', [KomponenStasiController::class,'index'])->name('stasi.komponen.index');
            Route::post('stasi/{stasi}/komponen', [KomponenStasiController::class,'store'])->name('stasi.komponen.store');
            Route::put('stasi/{stasi}/komponen/{komponen}', [KomponenStasiController::class,'update'])->name('stasi.komponen.update');
            Route::delete('stasi/{stasi}/komponen/{komponen}', [KomponenStasiController::class,'destroy'])->name('stasi.komponen.destroy');

            Route::get('rekap/jadwal/{jadwal}', [RekapController::class,'perJadwal'])->name('rekap.jadwal');
            Route::get('rekap/jadwal/{jadwal}/pdf', [RekapController::class,'exportPdfJadwal'])->name('rekap.jadwal.pdf');
            Route::get('rekap/jadwal/{jadwal}/excel', [ExportExcelController::class,'perJadwal'])->name('rekap.jadwal.excel');
        });
    });

    // Penguji Area
    Route::prefix('penguji')->name('penguji.')->group(function(){
        Route::get('stasi', [PenilaianController::class,'index'])->name('stasi');
        Route::get('stasi/{stasi}/jadwal/{jadwal}/mhs/{mahasiswa}', [PenilaianController::class,'form'])->name('form');
        Route::post('stasi/{stasi}/jadwal/{jadwal}/mhs/{mahasiswa}', [PenilaianController::class,'store'])->name('store');
    });
});
```

Middleware `can:admin-only` sederhana (opsional via Gate):
```php
// AuthServiceProvider boot()
Gate::define('admin-only', fn($user) => $user->role === 'admin');
```

---

## 11) Blade + Eldora UI (Tailwind)

### Instalasi Singkat
- Tailwind sudah dibawa Breeze; tambahkan Eldora UI (sesuai dokumentasi Eldora UI):
  - Jika via NPM: `npm i eldora-ui` lalu import plugin di `tailwind.config.js`
  - Jika via CDN (sederhana): link CDN di layout `<head>`

Layout dasar `resources/views/layouts/app.blade.php` (ringkas):
```blade
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'OSCE' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
  <x-eldora-navbar />
  <main class="max-w-7xl mx-auto p-4">
    @if(session('ok'))
      <div class="rounded bg-green-50 text-green-700 px-4 py-2 mb-4">{{ session('ok') }}</div>
    @endif
    {{ $slot ?? '' }}
    @yield('content')
  </main>
</body>
</html>
```

Form Penilaian Penguji (`resources/views/penguji/penilaian/form.blade.php`):
```blade
@extends('layouts.app')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold">Penilaian – {{ $stasi->nama }}</h1>
    <span class="text-sm text-gray-600">Anda adalah penguji yang ditugaskan di stasi ini</span>
  </div>

  <form method="POST" class="space-y-4">
    @csrf
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($komponen as $k)
        <div class="card p-4 bg-white shadow-sm rounded">
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ $k->nama }}</div>
              <div class="text-xs text-gray-500">Bobot: {{ $k->bobot }}%</div>
            </div>
            <input name="nilai[{{ $k->id }}]" type="number" min="0" max="100" step="0.1"
                   class="input input-bordered w-28 text-right" required />
          </div>
        </div>
      @endforeach
    </div>

    <div class="bg-white rounded shadow-sm p-4">
      <label class="block mb-2 text-sm">Global Rating</label>
      <select name="global_rating_id" class="select select-bordered" required>
        <option value="">-- Pilih --</option>
        @foreach($global as $g)
          <option value="{{ $g->id }}">{{ $g->label }}</option>
        @endforeach
      </select>
    </div>

    <div class="bg-white rounded shadow-sm p-4">
      <label class="block mb-2 text-sm">Catatan (opsional)</label>
      <textarea name="catatan" rows="3" class="textarea textarea-bordered w-full"></textarea>
    </div>

    <div class="flex gap-2">
      <button class="btn btn-primary">Simpan Nilai</button>
      <a href="{{ route('penguji.stasi') }}" class="btn">Kembali</a>
    </div>
  </form>
</div>
@endsection
```

Print-friendly PDF view (`resources/views/admin/rekap/pdf.blade.php`):
```blade
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 6px; }
  </style>
</head>
<body>
  @if($kop)
    <div style="margin-bottom:12px"><img src="{{ public_path($kop) }}" style="max-height:80px"></div>
  @endif
  <h3>Rekap Nilai – {{ $jadwal->nama }}</h3>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>NIM</th>
        <th>Nama</th>
        @foreach($stasi as $s)
          <th>{{ $s->nama }}</th>
        @endforeach
        <th>Global Rating</th>
        <th>Nama Penguji</th>
      </tr>
    </thead>
    <tbody>
      @foreach($peserta as $i => $m)
        @php($nilaiByStasi = $m->nilai->keyBy('stasi_id'))
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $m->nim }}</td>
          <td>{{ $m->nama }}</td>
          @foreach($stasi as $s)
            <td>{{ optional($nilaiByStasi[$s->id] ?? null)->total_nilai }}</td>
          @endforeach
          @php($n = $m->nilai->first())
          <td>{{ optional($n?->globalRating)->label }}</td>
          <td>{{ optional($n?->penguji)->name }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
```

---

## 12) Import Mahasiswa via Excel (Ringkas)
- Gunakan Laravel Excel: buat Import class membaca kolom `NIM, Nama, Kelas`
- Pada proses import:
  - Upsert `kelas.kod e`
  - Upsert `mahasiswa` berdasarkan NIM

> Detail implementasi import dapat ditambahkan setelah struktur inti selesai.

---

## 13) Seeders

```php
<?php // database/seeders/DatabaseSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GlobalRatingSeeder::class,
            UserSeeder::class,
            StasiSeeder::class,
            KomponenStasiSeeder::class,
            PenugasanSeeder::class,
        ]);
    }
}
```

```php
<?php // database/seeders/GlobalRatingSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\GlobalRating;

class GlobalRatingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode'=>'LULUS','label'=>'Lulus'],
            ['kode'=>'BORDERLINE','label'=>'Borderline (KKM)'],
            ['kode'=>'TIDAK_LULUS','label'=>'Tidak Lulus'],
        ];
        foreach ($data as $d) GlobalRating::firstOrCreate(['kode'=>$d['kode']], $d);
    }
}
```

```php
<?php // database/seeders/UserSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email'=>'admin@osce.test'], [
            'name'=>'Admin', 'password'=>Hash::make('password'), 'role'=>'admin'
        ]);

        User::firstOrCreate(['email'=>'penguji1@osce.test'], [
            'name'=>'Penguji 1', 'password'=>Hash::make('password'), 'role'=>'penguji'
        ]);
        User::firstOrCreate(['email'=>'penguji2@osce.test'], [
            'name'=>'Penguji 2', 'password'=>Hash::make('password'), 'role'=>'penguji'
        ]);
    }
}
```

```php
<?php // database/seeders/StasiSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Stasi;

class StasiSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Stasi 1','Stasi 2','Stasi 3','Stasi 4','Stasi 5','Stasi 6'];
        foreach ($names as $n) Stasi::firstOrCreate(['nama'=>$n]);
    }
}
```

```php
<?php // database/seeders/KomponenStasiSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Stasi, KomponenStasi};

class KomponenStasiSeeder extends Seeder
{
    public function run(): void
    {
        $stasi = Stasi::all();
        foreach ($stasi as $s) {
            for ($i=1; $i<=5; $i++) {
                KomponenStasi::firstOrCreate([
                    'stasi_id'=>$s->id,
                    'nama'=>"Komponen $i",
                ], [
                    'bobot'=>20,
                    'urutan'=>$i
                ]);
            }
        }
    }
}
```

```php
<?php // database/seeders/PenugasanSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User, Stasi};
use Illuminate\Support\Arr;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $penguji1 = User::where('email','penguji1@osce.test')->first();
        $penguji2 = User::where('email','penguji2@osce.test')->first();
        $stasi = Stasi::orderBy('id')->get();
        if ($penguji1) $penguji1->assignedStasi()->syncWithoutDetaching([$stasi[0]->id, $stasi[1]->id]);
        if ($penguji2) $penguji2->assignedStasi()->syncWithoutDetaching([$stasi[2]->id, $stasi[3]->id]);
    }
}
```

---

## 14) Struktur Folder Proyek (Direkomendasikan)
```
app/
  Http/
    Controllers/
      Admin/
      Penguji/
  Models/
  Policies/
  Services/
config/
database/
  migrations/
  seeders/
public/
resources/
  views/
    admin/
      stasi/
        index.blade.php
        create.blade.php
        edit.blade.php
        komponen/
          index.blade.php
      rekap/
        kelas.blade.php
        jadwal.blade.php
        pdf.blade.php
        excel.blade.php
    penguji/
      stasi/
        index.blade.php
      penilaian/
        form.blade.php
  css/js (Tailwind/Eldora)
routes/
```

---

## 15) Autentikasi (Laravel Breeze)
- Instalasi: `composer require laravel/breeze --dev` lalu `php artisan breeze:install blade`
- Menambahkan kolom `role` pada users (sudah di migration)
- Middleware/gate `admin-only` untuk area admin

---

## 16) Export & Branding (Kop Surat)
- Simpan path kop surat di `settings` (key: `kop_surat_path`)
- Admin mengupload file ke `public/kop/xxx.png`, simpan path di settings
- PDF menggunakan view `admin/rekap/pdf.blade.php`
- Excel menggunakan `FromView` agar format konsisten dengan tampilan

---

## 17) Checklist Implementasi
- [ ] Inisiasi Laravel 11 + Breeze
- [ ] Tailwind + Eldora UI
- [ ] Migrations & Models
- [ ] Seeder (6 stasi, 5 komponen/stasi, 1 admin, 2 penguji + penugasan)
- [ ] Admin: CRUD Stasi & Komponen
- [ ] Admin: Kelas, Mahasiswa, Import Excel
- [ ] Admin: Jadwal + peserta
- [ ] Penguji: Form penilaian + log
- [ ] Rekap + Export PDF & Excel
- [ ] Settings (kop surat)

---

## 18) Perintah Penting (Laragon/Windows)
```powershell
# Clone & install
composer install
cp .env.example .env
php artisan key:generate

# DB
php artisan migrate
php artisan db:seed

# Frontend
npm install
npm run dev

# Akun awal (Seeder):
# admin@osce.test / password
# penguji1@osce.test / password
# penguji2@osce.test / password
```

---

## 19) Catatan Keamanan & Performa
- Gunakan policy/gate untuk batasi akses penguji
- Validasi input ketat (nilai 0–100)
- Index unik untuk mencegah duplikasi nilai
- Cache daftar referensi (stasi, komponen) bila diperlukan
- Gunakan pagination untuk rekap besar

---

Selesai. Dokumen ini bisa langsung dijadikan panduan implementasi tahap demi tahap.

```text
Siap untuk generate migration & model pertama?
```
