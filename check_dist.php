<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\karyawan;
use App\Models\Departemen;

$results = \Illuminate\Support\Facades\DB::table('karyawan')
    ->select('departemen_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->groupBy('departemen_id')
    ->get();

foreach ($results as $r) {
    $deptName = $r->departemen_id ? (Departemen::find($r->departemen_id)->nama_departemen ?? 'INVALID DEPT ID') : 'NULL';
    echo "Dept ID: " . ($r->departemen_id ?? 'NULL') . " | Name: {$deptName} | Count: {$r->total}\n";
}
