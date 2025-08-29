<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penelitian extends Model
{
   protected $fillable = [
        'judul_penelitian',
        'link_proposal',
        'link_laporan_kemajuan',
        'link_laporan_akhir',
        'jenis_luaran',
        'link_hki',
        'judul_jurnal',
        'jurnal_vol',
        'jurnal_no',
        'jurnal_name',
        'tahun_jurnal',
        'judul_buku',
        'penerbit_buku',
        'isbn_buku',
        'tahun_buku',
    ];

    protected $casts = [
    'jenis_luaran' => 'array',
];

}
