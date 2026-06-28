<?php

function fixFile($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    $orig = $content;

    // Fix query relation in Guru/JurnalController
    $content = str_replace(
        "Jadwal::with(['kelas', 'mapel'])\n            ->where('guru_id', \$guru->id)\n            ->where('hari', \$hariIni)",
        "Jadwal::select('jadwal.*')\n            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')\n            ->with(['kelas', 'mapel', 'jamPelajaran'])\n            ->where('guru_id', \$guru->id)\n            ->where('jam_pelajaran.hari', \$hariIni)",
        $content
    );

    $content = str_replace(
        "->orderBy('jam_mulai')",
        "->orderBy('jam_pelajaran.jam_ke')",
        $content
    );

    // Fix PHP object references $j->jam_mulai
    $content = str_replace('$j->jam_mulai', '$j->jamPelajaran->jam_mulai', $content);
    $content = str_replace('$j->jam_selesai', '$j->jamPelajaran->jam_selesai', $content);

    // In edit/update we might also have $j->jam_mulai
    $content = str_replace('$jadwal->jam_mulai', '$jadwal->jamPelajaran->jam_mulai', $content);
    $content = str_replace('$jadwal->jam_selesai', '$jadwal->jamPelajaran->jam_selesai', $content);

    if ($orig !== $content) {
        file_put_contents($path, $content);
        echo "Fixed " . basename($path) . "\n";
    }
}

fixFile(__DIR__ . '/app/Http/Controllers/Guru/JurnalController.php');
