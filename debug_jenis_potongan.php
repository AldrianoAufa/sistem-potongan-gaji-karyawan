<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the exact query from controller
$jenisPotongan = \App\Models\JenisPotongan::withCount('inputBulanan')
    ->orderBy('kode_potongan')
    ->get();

echo "=== DEBUG JENIS POTONGAN ===" . PHP_EOL;
echo "Total records returned: " . $jenisPotongan->count() . PHP_EOL . PHP_EOL;

foreach ($jenisPotongan as $item) {
    $karyawanCount = $item->karyawan()->count();
    echo sprintf(
        "- %s: %s (karyawan: %d, input_bulanan: %d)" . PHP_EOL,
        $item->kode_potongan,
        $item->nama_potongan,
        $karyawanCount,
        $item->input_bulanan_count
    );
}

echo PHP_EOL . "=== SEPARATE CHECKS ===" . PHP_EOL;
echo "Total in database: " . \App\Models\JenisPotongan::count() . PHP_EOL;
echo "With karyawan: " . \App\Models\JenisPotongan::has('karyawan')->count() . PHP_EOL;
echo "Without karyawan: " . \App\Models\JenisPotongan::doesntHave('karyawan')->count() . PHP_EOL;
