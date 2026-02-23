<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departemen;
use App\Models\karyawan;

echo "DEPARTEMEN LIST:\n";
foreach (Departemen::all() as $d) {
    echo "ID: {$d->id} | Code: {$d->kode_departemen} | Name: {$d->nama_departemen}\n";
}

echo "\nKARYAWAN LIST:\n";
foreach (karyawan::all() as $k) {
    echo "ID: {$k->id} | Name: {$k->nama} | Dept ID: {$k->departemen_id}\n";
}
