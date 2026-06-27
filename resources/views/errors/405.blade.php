@include('errors.layout', [
    'code'        => '405',
    'emoji'       => '🚫',
    'title'       => 'Metode Tidak Diizinkan',
    'description' => 'Permintaan yang kamu kirim menggunakan metode HTTP yang tidak didukung oleh halaman ini.',
])
