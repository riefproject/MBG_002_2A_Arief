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
        Schema::create('permintaan_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('permintaan_id');
            $table->unsignedBigInteger('bahan_id');
            $table->unsignedInteger('jumlah_diminta');

            $table->foreign('permintaan_id')
                ->references('id')
                ->on('permintaan')
                ->cascadeOnDelete();

            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahan_baku')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_detail');
    }
};
