import re

with open(r'f:\Laravel\jurnalKelas\resources\views\guru\jadwal\by-kelas.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix breadcrumb by removing everything from the first {{-- Laporan Jurnal Modal --}} up to its @endsection
pattern = r'(\{\{-- Laporan Jurnal Modal --\}\}.*?</div>\{\{-- end x-data --\}\}\n@endsection\n)'
# Wait, let's just replace it directly:
content = re.sub(pattern, '', content, count=1, flags=re.DOTALL)

with open(r'f:\Laravel\jurnalKelas\resources\views\guru\jadwal\by-kelas.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
