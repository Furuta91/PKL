<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penelitian extends Model
{
   protected $fillable = [
        'tahun_ajaran',
        'periode',
        'user_id',
        'judul_penelitian',
        'link_proposal',
        'link_laporan_kemajuan',
        'link_laporan_akhir',
        'status',
        'progres',
    ];

    protected $casts = [
    'jenis_luaran' => 'array',
];

    protected static function booted()
{
    static::saved(function ($penelitian) {
        $totalFields = 0;
        $filled = 0;

        foreach ($penelitian->luaransPenelitian as $luaran) {
            $fields = [
                $luaran->link_hki,
                $luaran->judul_jurnal,
                $luaran->jurnal_vol,
                $luaran->jurnal_no,
                $luaran->jurnal_name,
                $luaran->tahun_jurnal,
                $luaran->judul_buku,
                $luaran->penerbit_buku,
                $luaran->isbn_buku,
                $luaran->tahun_buku,
            ];

            $totalFields += count($fields);
            $filled += collect($fields)->filter(fn ($val) => !empty($val))->count();
        }

        $progress = $totalFields > 0 ? round(($filled / $totalFields) * 100) : 0;

        // update ulang progres ke DB
        $penelitian->updateQuietly([
            'progres' => $progress,
        ]);
    });

}

// App/Models/Penelitian.php

public function recalculateProgress()
{
    $totalFields = 0;
    $filled = 0;

    foreach ($this->luaransPenelitian as $luaran) {
        $fields = [
            $luaran->link_hki,
            $luaran->judul_jurnal,
            $luaran->jurnal_vol,
            $luaran->jurnal_no,
            $luaran->jurnal_name,
            $luaran->tahun_jurnal,
            $luaran->judul_buku,
            $luaran->penerbit_buku,
            $luaran->isbn_buku,
            $luaran->tahun_buku,
        ];

        $totalFields += count($fields);
        $filled += collect($fields)->filter(fn ($val) => !empty($val))->count();
    }

    $progress = $totalFields > 0 ? round(($filled / $totalFields) * 100) : 0;

    $this->updateQuietly(['progres' => $progress]);
}



public function user()
{
    return $this->belongsTo(User::class);
}

public function luaransPenelitian()
{
    return $this->hasMany(LuaranPenelitians::class);
}


}