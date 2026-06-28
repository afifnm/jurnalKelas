<?php

$files = [
    __DIR__ . '/resources/views/admin/jadwal/by-kelas.blade.php',
    __DIR__ . '/resources/views/admin/jadwal/by-guru.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Add jam_pelajaran_id to defaultForm
        $content = str_replace(
            "guru_id: '', mapel_id: '', hari: '', jam_mulai: '', jam_selesai: '', jam_ke: '',",
            "guru_id: '', mapel_id: '', hari: '', jam_mulai: '', jam_selesai: '', jam_ke: '', jam_pelajaran_id: '',",
            $content
        );

        // Update jam_pelajaran_id inside openEdit
        $content = str_replace(
            "jam_ke:          slot ? String(slot.jam_ke) : '',",
            "jam_ke:          slot ? String(slot.jam_ke) : '',\n                jam_pelajaran_id: slot ? String(slot.id) : '',",
            $content
        );

        // Update onJamKeChange
        $replacement = <<<EOF
        onJamKeChange() {
            const slot = this.jamSlots.find(s => String(s.jam_ke) === String(this.form.jam_ke));
            if (slot) {
                this.form.jam_mulai   = (slot.jam_mulai   || '').substring(0, 5);
                this.form.jam_selesai = (slot.jam_selesai || '').substring(0, 5);
                this.form.jam_pelajaran_id = String(slot.id);
            } else {
                this.form.jam_mulai   = '';
                this.form.jam_selesai = '';
                this.form.jam_pelajaran_id = '';
            }
        },
EOF;
        $content = preg_replace('/onJamKeChange\(\)\s*\{[^\}]*\}/s', $replacement, $content);
        
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}

// Now mapping.blade.php
$mappingFile = __DIR__ . '/resources/views/admin/jadwal/mapping.blade.php';
if (file_exists($mappingFile)) {
    $content = file_get_contents($mappingFile);
    
    // Replace payload in updateMapel
    $content = str_replace(
        "body: JSON.stringify({ tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, hari, jam_mulai: mulai, jam_selesai: selesai, overwrite_id: overWrittenId })",
        "body: JSON.stringify({ tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, jam_pelajaran_id: slot.id, overwrite_id: overWrittenId })",
        $content
    );
    
    file_put_contents($mappingFile, $content);
    echo "Updated $mappingFile\n";
}

