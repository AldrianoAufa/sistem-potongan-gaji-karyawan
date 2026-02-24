<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate what the controller does
$departemen = App\Models\Departemen::find(6);
echo "Departemen: " . $departemen->kode_departemen . " - " . $departemen->nama_departemen . PHP_EOL;

$karyawan = $departemen->karyawan()->with('jabatan')->orderBy('nama')->get();
echo "Karyawan count: " . $karyawan->count() . PHP_EOL;

// Also test eager loaded
$dept2 = App\Models\Departemen::with('karyawan')->find(6);
echo "Eager loaded karyawan count: " . $dept2->karyawan->count() . PHP_EOL;

// Check SQL
echo "SQL: " . $departemen->karyawan()->toSql() . PHP_EOL;
echo "Bindings: " . json_encode($departemen->karyawan()->getBindings()) . PHP_EOL;

// Raw check
$rawCount = App\Models\Karyawan::where('departemen_id', 6)->count();
echo "Raw count where departemen_id=6: " . $rawCount . PHP_EOL;
