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

    // App\Models\Penelitian.php

    protected static function booted()
    {
        static::saved(function ($penelitian) {
            $penelitian->recalculateProgress();
        });
    }

    public function recalculateProgress(): void
    {
        $totalFields = 0;
        $filled      = 0;

        // =========================
        // 🔹 Hitung field repeater
        // =========================
        foreach ($this->luaransPengabdian as $luaran) {
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
            $filled      += collect($fields)->filter(fn($val) => !empty($val))->count();
        }

        // =========================
        // 🔹 Hitung field top-level
        // =========================
        $topLevel = [
            $this->link_proposal,
            $this->link_laporan_kemajuan,
            $this->link_laporan_akhir,
        ];

        $totalFields += count($topLevel);
        $filled      += collect($topLevel)->filter(fn($val) => !empty($val))->count();

        // =========================
        // 🔹 Final Progress
        // =========================
        $progress = $totalFields > 0 ? round(($filled / $totalFields) * 100) : 0;

        $this->updateQuietly([
            'progres' => $progress,
            'status'  => $progress >= 100 ? 'Pending' : 'On Progress',
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function luaransPengabdian()
    {
        return $this->hasMany(LuaranPengabdians::class);
    }
}