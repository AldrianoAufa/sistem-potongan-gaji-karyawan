<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\karyawan;
use Illuminate\Support\Facades\DB;

$raw = DB::table('karyawan')->where('id', 1)->first();
echo "RAW DATA for Karyawan ID 1:\n";
foreach ((array)$raw as $key => $val) {
    echo "{$key}: (" . gettype($val) . ") " . var_export($val, true) . "\n";
}
