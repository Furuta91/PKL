<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('luaran_penelitians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penelitian_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_luaran');

            // HKI
            $table->string('link_hki')->nullable();

            // Jurnal
            $table->string('judul_jurnal')->nullable();
            $table->string('jurnal_vol')->nullable();
            $table->string('jurnal_no')->nullable();
            $table->string('jurnal_name')->nullable();
            $table->year('tahun_jurnal')->nullable();

            // Buku
            $table->string('judul_buku')->nullable();
            $table->string('penerbit_buku')->nullable();
            $table->string('isbn_buku')->nullable();
            $table->year('tahun_buku')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luaran_penelitians');
    }
};
