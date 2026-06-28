<?php

function fixFile($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    $orig = $content;

    $content = str_replace(
        "Jadwal::with(['guru', 'kelas', 'mapel'])\n            ->where('hari', \$hariIni)",
        "Jadwal::select('jadwal.*')\n            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')\n            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])\n            ->where('jam_pelajaran.hari', \$hariIni)",
        $content
    );
    $content = str_replace(
        "Jadwal::with(['guru', 'kelas', 'mapel'])\n            ->where('hari', \$hariIni)",
        "Jadwal::select('jadwal.*')\n            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')\n            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])\n            ->where('jam_pelajaran.hari', \$hariIni)",
        $content
    );
    $content = str_replace(
        "->orderBy('jam_mulai')",
        "->orderBy('jam_pelajaran.jam_ke')",
        $content
    );

    if ($orig !== $content) {
        file_put_contents($path, $content);
        echo "Fixed Admin/Dashboard\n";
    }
}

fixFile(__DIR__ . '/app/Http/Controllers/Admin/DashboardController.php');
