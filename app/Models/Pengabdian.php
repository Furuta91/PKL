<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengabdian extends Model
{
       protected $fillable = [
        'tahun_ajaran',
        'periode',
        'user_id',
        'judul_pengabdian',
        'link_proposal',
        'link_laporan_kemajuan',
        'link_laporan_akhir',
        'status',
        'progres',
    ];

    protected $casts = [
    'jenis_luaran' => 'array',
];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function luaransPengabdian()
    {
        return $this->hasMany(LuaranPengabdians::class);
    }
}
