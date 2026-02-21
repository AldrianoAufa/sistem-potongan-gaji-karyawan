<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputBulanan extends Model
{
    use HasFactory;

    protected $table = 'input_bulanan';

    protected $fillable = [
        'anggota_id',
        'jenis_potongan_id',
        'bulan',
        'tahun',
        'jumlah_potongan',
        'data_rinci',
    ];

    protected $casts = [
        'data_rinci' => 'array',
        'jumlah_potongan' => 'decimal:2',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jenisPotongan()
    {
        return $this->belongsTo(JenisPotongan::class);
    }

    /**
     * Get the nama bulan in Indonesian.
     */
    public function getNamaBulanAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulanNames[$this->bulan] ?? '';
    }
}
