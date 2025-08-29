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
        Schema::create('pengabdians', function (Blueprint $table) {
          $table->id();
           $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tahun_ajaran');
            $table->string('periode');
            $table->string('judul_pengabdian');
            $table->string('link_proposal')->nullable();
            $table->string('link_laporan_kemajuan')->nullable();
            $table->string('link_laporan_akhir')->nullable();
            $table->enum('status', ['In Progress', 'Pending', 'Approved', 'Rejected'])
              ->default(value: 'In Progress');
            $table->unsignedTinyInteger('progres')
              ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengabdians');
    }
};
