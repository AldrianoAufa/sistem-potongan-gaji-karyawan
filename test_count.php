<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departemen;

$d = Departemen::withCount('karyawan')->find(1);
echo "Dept: {$d->nama_departemen} | Karyawan Count: {$d->karyawan_count}\n";
