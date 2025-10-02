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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('kategori', 60);
            $table->integer('jumlah');
            $table->string('satuan', 20);
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa');
            $table->enum('status', ['tersedia', 'segera_kadaluarsa', 'kadaluarsa', 'habis']);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('bahan_baku');
    }
};
