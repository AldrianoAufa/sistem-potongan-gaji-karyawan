<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPotongan extends Model
{
    use HasFactory;

    protected $table = 'jenis_potongan';

    protected $fillable = ['kode_potongan', 'nama_potongan'];

    public function inputBulanan()
    {
        return $this->hasMany(InputBulanan::class);
    }
}
