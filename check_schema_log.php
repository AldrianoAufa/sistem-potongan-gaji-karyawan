<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select('SHOW COLUMNS FROM karyawan');
$output = "";
foreach ($columns as $c) {
    $output .= "Field: {$c->Field} | Type: {$c->Type}\n";
}
file_put_contents('schema_log.txt', $output);
