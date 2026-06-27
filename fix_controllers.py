import re

files = [
    r'f:\Laravel\jurnalKelas\app\Http\Controllers\Admin\JadwalViewController.php',
    r'f:\Laravel\jurnalKelas\app\Http\Controllers\Guru\JadwalViewController.php',
    r'f:\Laravel\jurnalKelas\app\Http\Controllers\Ks\JadwalViewController.php',
]

old_by_guru = r"""        \$jadwalPerGuru = collect\(\);
        foreach \(\$guruList as \$guru\) \{
            \$jadwal = Jadwal::with\(\['kelas', 'mapel'\]\)
                ->where\('guru_id', \$guru->id\)
                ->when\(\$tahunId, fn\(\$q\) => \$q->where\('tahun_ajaran_id', \$tahunId\)\)
                ->orderBy\('hari'\)
                ->orderBy\('jam_mulai'\)
                ->get\(\)
                ->groupBy\('hari'\);

            \$jadwalPerGuru\[\$guru->id\] = \[
                'guru'   => \$guru,
                'jadwal' => \$jadwal,
            \];
        \}"""

new_by_guru = """        $allJadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->whereIn('guru_id', $guruList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('guru_id');

        $jadwalPerGuru = collect();
        foreach ($guruList as $guru) {
            $jadwal = $allJadwalGuru->get($guru->id, collect())->groupBy('hari');
            $jadwalPerGuru[$guru->id] = [
                'guru'   => $guru,
                'jadwal' => $jadwal,
            ];
        }"""

old_by_guru_g = r"""        \$jadwalPerGuru = collect\(\);
        foreach \(\$guruList as \$g\) \{
            \$jadwal = Jadwal::with\(\['kelas', 'mapel'\]\)
                ->where\('guru_id', \$g->id\)
                ->when\(\$tahunId, fn\(\$q\) => \$q->where\('tahun_ajaran_id', \$tahunId\)\)
                ->orderBy\('hari'\)
                ->orderBy\('jam_mulai'\)
                ->get\(\)
                ->groupBy\('hari'\);

            \$jadwalPerGuru\[\$g->id\] = \[
                'guru'   => \$g,
                'jadwal' => \$jadwal,
            \];
        \}"""

new_by_guru_g = """        $allJadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->whereIn('guru_id', $guruList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('guru_id');

        $jadwalPerGuru = collect();
        foreach ($guruList as $g) {
            $jadwal = $allJadwalGuru->get($g->id, collect())->groupBy('hari');
            $jadwalPerGuru[$g->id] = [
                'guru'   => $g,
                'jadwal' => $jadwal,
            ];
        }"""


old_by_kelas = r"""        \$jadwalPerKelas = collect\(\);
        foreach \(\$kelasList as \$kelas\) \{
            \$jadwal = Jadwal::with\(\['guru', 'mapel'\]\)
                ->where\('kelas_id', \$kelas->id\)
                ->when\(\$tahunId, fn\(\$q\) => \$q->where\('tahun_ajaran_id', \$tahunId\)\)
                ->orderBy\('hari'\)
                ->orderBy\('jam_mulai'\)
                ->get\(\)
                ->groupBy\('hari'\);

            \$jadwalPerKelas\[\$kelas->id\] = \[
                'kelas'  => \$kelas,
                'jadwal' => \$jadwal,
            \];
        \}"""

new_by_kelas = """        $allJadwalKelas = Jadwal::with(['guru', 'mapel'])
            ->whereIn('kelas_id', $kelasList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('kelas_id');

        $jadwalPerKelas = collect();
        foreach ($kelasList as $kelas) {
            $jadwal = $allJadwalKelas->get($kelas->id, collect())->groupBy('hari');
            $jadwalPerKelas[$kelas->id] = [
                'kelas'  => $kelas,
                'jadwal' => $jadwal,
            ];
        }"""


for file in files:
    try:
        with open(file, 'r', encoding='utf-8') as f:
            content = f.read()

        content = re.sub(old_by_guru, new_by_guru, content)
        content = re.sub(old_by_guru_g, new_by_guru_g, content)
        content = re.sub(old_by_kelas, new_by_kelas, content)

        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print("Updated", file)
    except Exception as e:
        print("Error processing", file, e)
