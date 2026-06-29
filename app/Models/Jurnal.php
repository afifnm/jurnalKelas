<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JurnalLog;

use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal';

    protected $fillable = [
        'jadwal_id', 'guru_id', 'kelas_id', 'mapel_id', 'tahun_ajaran_id',
        'tanggal', 'materi', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class)->withTrashed();
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id')->withTrashed();
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class)->withTrashed();
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class)->withTrashed();
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(JurnalLampiran::class);
    }

    public function log(): HasMany
    {
        return $this->hasMany(JurnalLog::class);
    }

    /**
     * Cek apakah jurnal diisi dalam rentang jam mengajar (jadwal).
     * Mengembalikan true jika created_at berada dalam window jam jadwal (dengan toleransi 15 menit sebelum s.d. 30 menit sesudah).
     */
    public function isInputDalamJamMengajar(): bool
    {
        if (! $this->jadwal || ! $this->jadwal->jamPelajaran) {
            return false;
        }

        $createdAt  = $this->created_at;
        $jamMulai   = Carbon::createFromTimeString($this->jadwal->jamPelajaran->jam_mulai)->setDateFrom($createdAt);
        $jamSelesai = Carbon::createFromTimeString($this->jadwal->jamPelajaran->jam_selesai)->setDateFrom($createdAt);

        // Cek apakah jadwal ada grup (beberapa jam berurutan), ambil jam selesai terakhir lewat relasi grup
        $windowMulai   = $jamMulai->copy()->subMinutes(15);
        $windowSelesai = $jamSelesai->copy()->addMinutes(30);

        return $createdAt->between($windowMulai, $windowSelesai);
    }

    /**
     * Build map [jurnal_id => ['mulai'=>'HH:MM','selesai'=>'HH:MM','jumlah'=>n]]
     * untuk seluruh collection jurnal tanpa N+1.
     * Jadwal pada jurnal harus sudah di-eager-load dengan jamPelajaran.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Pagination\AbstractPaginator  $jurnal
     */
    public static function buildJamSesiMap($jurnal): array
    {
        // Kumpulkan kombinasi unik (mapel+kelas+tahun+hari) dari semua jurnal di halaman
        $kombo = collect();
        foreach ($jurnal as $j) {
            if ($j->jadwal && $j->jadwal->jamPelajaran) {
                $kombo->push([
                    'mapel_id'        => $j->jadwal->mapel_id,
                    'kelas_id'        => $j->jadwal->kelas_id,
                    'tahun_ajaran_id' => $j->jadwal->tahun_ajaran_id,
                    'hari'            => $j->jadwal->jamPelajaran->hari,
                ]);
            }
        }
        $kombo = $kombo->unique(fn($k) => implode('-', $k));

        if ($kombo->isEmpty()) {
            return [];
        }

        // Satu query untuk semua jadwal yang dibutuhkan
        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with('jamPelajaran')
            ->where(function ($q) use ($kombo) {
                foreach ($kombo as $k) {
                    $q->orWhere(function ($q2) use ($k) {
                        $q2->where('jadwal.mapel_id', $k['mapel_id'])
                           ->where('jadwal.kelas_id', $k['kelas_id'])
                           ->where('jadwal.tahun_ajaran_id', $k['tahun_ajaran_id'])
                           ->where('jam_pelajaran.hari', $k['hari']);
                    });
                }
            })
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy(fn($j) => $j->mapel_id . '-' . $j->kelas_id . '-' . $j->tahun_ajaran_id . '-' . $j->jamPelajaran->hari);

        // Pre-compute grup untuk setiap kombo
        $grupMap = [];
        foreach ($allJadwal as $key => $jadwalGroup) {
            $grups = Jadwal::grupkanBerurutan($jadwalGroup);
            foreach ($grups as $grup) {
                foreach ($grup['ids'] as $jadwalId) {
                    $first = $grup['jadwal']->first();
                    $last  = $grup['jadwal']->last();
                    $grupMap[$jadwalId] = [
                        'mulai'   => substr($first->jamPelajaran->jam_mulai, 0, 5),
                        'selesai' => substr($last->jamPelajaran->jam_selesai, 0, 5),
                        'jumlah'  => $grup['jadwal']->count(),
                    ];
                }
            }
        }

        // Susun map per jurnal_id
        $result = [];
        foreach ($jurnal as $j) {
            if ($j->jadwal_id && isset($grupMap[$j->jadwal_id])) {
                $result[$j->id] = $grupMap[$j->jadwal_id];
            }
        }
        return $result;
    }

    public function isEditableByGuru(): bool
    {
        return true;
    }
}
