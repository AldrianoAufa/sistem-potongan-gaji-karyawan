<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate the exact controller logic
echo "=== SIMULATING CONTROLLER INDEX METHOD ===" . PHP_EOL;

// Step 1: Get all jenis potongan with inputBulanan count
$jenisPotongan = \App\Models\JenisPotongan::withCount('inputBulanan')
    ->orderBy('kode_potongan')
    ->paginate(15);

echo "Step 1 - Records with withCount: " . $jenisPotongan->count() . PHP_EOL;

// Step 2: Load karyawan relationship
$jenisPotongan->load(['karyawan' => function ($q) {
    $q->select('karyawan.id', 'kode_karyawan', 'nama', 'jabatan_id', 'departemen_id')
      ->with(['jabatan:id,nama_jabatan', 'departemen:id,nama_departemen'])
      ->orderBy('nama');
}]);

echo "Step 2 - After loading karyawan: " . $jenisPotongan->count() . PHP_EOL;

// Step 3: Check each item
echo PHP_EOL . "=== INDIVIDUAL ITEMS ===" . PHP_EOL;
foreach ($jenisPotongan as $i => $item) {
    $karyawanCount = $item->karyawan->count();
    echo sprintf(
        "Item %d: %s - %s (karyawan: %d, input: %d)" . PHP_EOL,
        $i + 1,
        $item->kode_potongan,
        $item->nama_potongan,
        $karyawanCount,
        $item->input_bulanan_count
    );
}

echo PHP_EOL . "=== PAGINATION INFO ===" . PHP_EOL;
echo "Total: " . $jenisPotongan->total() . PHP_EOL;
echo "First Item: " . $jenisPotongan->firstItem() . PHP_EOL;
echo "Last Item: " . $jenisPotongan->lastItem() . PHP_EOL;
echo "Current Page: " . $jenisPotongan->currentPage() . PHP_EOL;
echo "Last Page: " . $jenisPotongan->lastPage() . PHP_EOL;
