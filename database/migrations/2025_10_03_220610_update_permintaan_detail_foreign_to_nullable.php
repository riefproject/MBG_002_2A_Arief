<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_detail', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->unsignedBigInteger('bahan_id')->nullable()->change();
            $table->string('bahan_nama_snapshot', 120)->nullable()->after('bahan_id');
            $table->string('bahan_satuan_snapshot', 30)->nullable()->after('bahan_nama_snapshot');
            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahan_baku')
                ->nullOnDelete();
        });

        DB::table('permintaan_detail')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                $bahanIds = collect($rows)->pluck('bahan_id')->filter()->unique();

                if ($bahanIds->isEmpty()) {
                    return;
                }

                $bahanMap = DB::table('bahan_baku')
                    ->whereIn('id', $bahanIds)
                    ->get(['id', 'nama', 'satuan'])
                    ->keyBy('id');

                foreach ($rows as $row) {
                    $snapshot = $row->bahan_id ? $bahanMap->get($row->bahan_id) : null;

                    if ($snapshot) {
                        DB::table('permintaan_detail')
                            ->where('id', $row->id)
                            ->update([
                                'bahan_nama_snapshot' => $snapshot->nama,
                                'bahan_satuan_snapshot' => $snapshot->satuan,
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('permintaan_detail', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->dropColumn(['bahan_nama_snapshot', 'bahan_satuan_snapshot']);
            $table->unsignedBigInteger('bahan_id')->nullable(false)->change();
            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahan_baku')
                ->restrictOnDelete();
        });
    }
};
