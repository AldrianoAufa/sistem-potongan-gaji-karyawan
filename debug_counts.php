<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departemen;
use App\Models\karyawan;

$depts = Departemen::all();
foreach ($depts as $d) {
    $count = $d->karyawan()->count();
    echo "Dept ID: {$d->id} | Code: {$d->kode_departemen} | Name: {$d->nama_departemen} | Employee Count: {$count}\n";
}

$orphans = karyawan::whereNull('departemen_id')->count();
echo "Employees with NO Dept ID: {$orphans}\n";

$bad_depts = karyawan::whereNotNull('departemen_id')
    ->whereNotIn('departemen_id', Departemen::pluck('id'))
    ->count();
echo "Employees with INVALID Dept ID: {$bad_depts}\n";
