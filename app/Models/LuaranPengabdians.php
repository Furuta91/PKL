<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuaranPengabdians extends Model
{
    protected $fillable = [
        'pengabdian_id',
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

    public function pengabdian()
    {
        return $this->belongsTo(related: Pengabdian::class);
    }

    protected static function booted()
    {
        static::saved(function ($luaran) {
            if ($luaran->pengabdian) {
                $luaran->pengabdian->recalculateProgress();
            }
        });

        static::deleted(function ($luaran) {
            if ($luaran->pengabdian) {
                $luaran->pengabdian->recalculateProgress();
            }
        });
    }
}
