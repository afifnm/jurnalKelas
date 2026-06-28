<?php

$dir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $originalContent = $content;

        // In javascript blocks, replace jadwal.jam_mulai -> jadwal.jam_pelajaran?.jam_mulai
        $content = str_replace('jadwal.jam_mulai', 'jadwal.jam_pelajaran?.jam_mulai', $content);
        $content = str_replace('jadwal.jam_selesai', 'jadwal.jam_pelajaran?.jam_selesai', $content);
        $content = str_replace('jadwal.hari', 'jadwal.jam_pelajaran?.hari', $content);

        // Also item.jam_mulai if it comes from jadwal
        // Let's check item.hari -> item.jam_pelajaran?.hari
        $content = preg_replace('/item\.hari(?!_)/', 'item.jam_pelajaran?.hari', $content);
        $content = preg_replace('/item\.jam_mulai/', 'item.jam_pelajaran?.jam_mulai', $content);
        $content = preg_replace('/item\.jam_selesai/', 'item.jam_pelajaran?.jam_selesai', $content);

        if ($content !== $originalContent) {
            file_put_contents($file->getRealPath(), $content);
            echo "Updated JS in: " . $file->getRealPath() . "\n";
        }
    }
}
