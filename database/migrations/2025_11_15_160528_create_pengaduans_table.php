<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('code_pengaduan')->unique();
            $table->text('perihal');
            $table->string('nama_terlapor');
            $table->foreignId('jenis_pengaduan_id')->constrained('combos')->onDelete('set null');
            $table->foreignId('saluran_aduan_id')->constrained('combos')->onDelete('set null');
            $table->foreignId('direktorat')->constrained('owners')->onDelete('set null');
            $table->string('email_pelapor');
            $table->string('telepon_pelapor');
            $table->timestamp('waktu_kejadian');
            $table->timestamp('tanggal_pengaduan');
            $table->string('uraian');
            $table->text('alamat_kejadian');
            $table->json('lampiran')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Tambahkan soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengaduans');
    }
};