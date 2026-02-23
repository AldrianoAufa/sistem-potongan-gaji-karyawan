<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\karyawan;

foreach (karyawan::with('departemen')->get() as $k) {
    echo "Karyawan: {$k->nama} | Dept ID: {$k->departemen_id} | Dept Code: " . ($k->departemen->kode_departemen ?? 'NONE') . "\n";
}
