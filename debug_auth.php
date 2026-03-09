<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;
use App\Models\User;

echo "Sample Users:\n";
foreach (User::limit(10)->get() as $u) {
    echo "ID: {$u->id}, Username: {$u->username}, Role: {$u->role}\n";
}

$u = User::where('username', '1234.45')->first();
if ($u) {
    echo "\nUser 1234.45 exists: ID: {$u->id}, Role: {$u->role}\n";
} else {
    echo "\nUser 1234.45 does NOT exist as Username.\n";
}

$k = Karyawan::where('kode_karyawan', '1234.45')->first();
if ($k) {
    echo "\nKaryawan 1234.45 exists: ID: {$k->id}, Nama: {$k->nama}\n";
} else {
    echo "\nKaryawan 1234.45 does NOT exist.\n";
}
