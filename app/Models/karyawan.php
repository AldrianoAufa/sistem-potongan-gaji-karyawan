<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = ['kode_karyawan', 'nama', 'jabatan_id', 'departemen_id'];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function inputBulanan()
    {
        return $this->hasMany(InputBulanan::class);
    }

    public function potongan()
    {
        return $this->belongsToMany(JenisPotongan::class, 'karyawan_potongan', 'karyawan_id', 'jenis_potongan_id');
    }
}
