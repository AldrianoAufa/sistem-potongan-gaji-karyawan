<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$count = 0;
$existing = 0;

Karyawan::chunk(100, function ($karyawans) use (&$count, &$existing) {
    foreach ($karyawans as $k) {
        // Skip if user already exists for this karyawan
        if (User::where('karyawan_id', $k->id)->exists()) {
            $existing++;
            continue;
        }

        // Delete any old user with same username
        User::where('username', $k->kode_karyawan)->delete();

        User::create([
            'username'    => $k->kode_karyawan,
            'password'    => Hash::make($k->kode_karyawan),
            'role'        => 'user',
            'karyawan_id' => $k->id,
        ]);
        $count++;
    }
});

echo "Done! Created {$count} user accounts. Skipped {$existing} (already had account).\n";
