<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;
use App\Models\User;

$count = 0;
$existing = 0;
$reset = 0;

Karyawan::chunk(100, function ($karyawans) use (&$count, &$existing, &$reset) {
    foreach ($karyawans as $k) {
        $user = User::where('karyawan_id', $k->id)->first();

        if ($user) {
            // Reset password to NIK (User model 'hashed' cast will auto-hash)
            $user->update(['password' => $k->kode_karyawan]);
            $reset++;
            $existing++;
            continue;
        }

        // Delete any old user with same username
        User::where('username', $k->kode_karyawan)->delete();

        // Create new user (User model 'hashed' cast will auto-hash the password)
        User::create([
            'username'    => $k->kode_karyawan,
            'password'    => $k->kode_karyawan,
            'role'        => 'user',
            'karyawan_id' => $k->id,
        ]);
        $count++;
    }
});

echo "Done! Created {$count} new accounts. Reset {$reset} existing passwords. Total existing: {$existing}.\n";
