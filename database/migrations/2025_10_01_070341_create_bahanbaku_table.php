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
        Schema::create('bahanbaku', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama');
            $table->string('kategori');
            $table->integer('jumlah');
            $table->datetime('tanggal_masuk');
            $table->datetime('tanggal_kadaluarsa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahanbaku');
    }
};
