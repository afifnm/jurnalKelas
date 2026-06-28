<?php

$dir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $originalContent = $content;

        // Replace $j->jam_mulai -> $j->jamPelajaran->jam_mulai
        $content = str_replace('$j->jam_mulai', '$j->jamPelajaran->jam_mulai', $content);
        $content = str_replace('$j->jam_selesai', '$j->jamPelajaran->jam_selesai', $content);
        
        // Also $selectedJadwal and $autoFilledJadwal
        $content = str_replace('$selectedJadwal->jam_mulai', '$selectedJadwal->jamPelajaran->jam_mulai', $content);
        $content = str_replace('$selectedJadwal->jam_selesai', '$selectedJadwal->jamPelajaran->jam_selesai', $content);
        $content = str_replace('$autoFilledJadwal->jam_mulai', '$autoFilledJadwal->jamPelajaran->jam_mulai', $content);
        $content = str_replace('$autoFilledJadwal->jam_selesai', '$autoFilledJadwal->jamPelajaran->jam_selesai', $content);

        // Sort replacements
        $content = str_replace("sortBy('jam_mulai')", "sortBy(fn(\$j) => \$j->jamPelajaran->jam_ke)", $content);
        $content = str_replace("sortBy('hari')", "sortBy(fn(\$j) => \$j->jamPelajaran->hari)", $content);

        if ($content !== $originalContent) {
            file_put_contents($file->getRealPath(), $content);
            echo "Updated: " . $file->getRealPath() . "\n";
        }
    }
}
