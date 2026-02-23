<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departemen;

$id = 1;
$d = Departemen::find($id);
if (!$d) {
    die("Departemen ID {$id} not found!\n");
}

echo "Departemen: {$d->nama_departemen} (ID: {$d->id})\n";
$karyawan = $d->karyawan;
echo "Karyawan Count (via relationship): " . $karyawan->count() . "\n";

foreach ($karyawan as $k) {
    echo "- {$k->nama} (Dept ID: {$k->departemen_id})\n";
}

$manual = \App\Models\karyawan::where('departemen_id', $id)->get();
echo "Karyawan Count (via manual query): " . $manual->count() . "\n";
