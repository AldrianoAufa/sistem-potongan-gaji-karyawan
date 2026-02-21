<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = ['kode_anggota', 'nama', 'jabatan_id'];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function inputBulanan()
    {
        return $this->hasMany(InputBulanan::class);
    }
}
