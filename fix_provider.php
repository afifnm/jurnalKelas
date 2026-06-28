<?php

$path = __DIR__ . '/app/Providers/AppServiceProvider.php';
$content = file_get_contents($path);

// Fix guru part
$content = str_replace(
    "Jadwal::with(['kelas', 'mapel'])\n                    ->where('guru_id', \$user->id)\n                    ->where('hari', \$hariIni)",
    "Jadwal::select('jadwal.*')\n                    ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')\n                    ->with(['kelas', 'mapel', 'jamPelajaran'])\n                    ->where('guru_id', \$user->id)\n                    ->where('jam_pelajaran.hari', \$hariIni)",
    $content
);
$content = str_replace(
    "->orderBy('jam_mulai')",
    "->orderBy('jam_pelajaran.jam_ke')",
    $content
);
$content = str_replace(
    "\$j->jam_mulai",
    "\$j->jamPelajaran->jam_mulai",
    $content
);

// Fix admin/ks part
$content = str_replace(
    "Jadwal::with('guru')\n                    ->where('hari', \$hariIni)",
    "Jadwal::select('jadwal.*')\n                    ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')\n                    ->with(['guru', 'jamPelajaran'])\n                    ->where('jam_pelajaran.hari', \$hariIni)",
    $content
);

file_put_contents($path, $content);
echo "Fixed AppServiceProvider.php\n";
