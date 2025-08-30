<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuaranPenelitians extends Model
{
    protected $fillable = [
        'penelitian_id',
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

    public function penelitian()
    {
        return $this->belongsTo(Penelitian::class);
    }

    // App/Models/LuaranPenelitian.php

    protected static function booted()
    {
        static::saved(function ($luaran) {
            if ($luaran->penelitian) {
                $luaran->penelitian->recalculateProgress();
            }
        });

        static::deleted(function ($luaran) {
            if ($luaran->penelitian) {
                $luaran->penelitian->recalculateProgress();
            }
        });
    }
}
