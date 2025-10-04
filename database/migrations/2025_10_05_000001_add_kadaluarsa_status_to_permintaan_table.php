<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('permintaan', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("ALTER TABLE permintaan ADD COLUMN status_new TEXT CHECK(status_new IN ('menunggu','disetujui','ditolak','kadaluarsa')) DEFAULT 'menunggu' NOT NULL");
            DB::statement('UPDATE permintaan SET status_new = status');
            DB::statement('ALTER TABLE permintaan DROP COLUMN status');
            DB::statement('ALTER TABLE permintaan RENAME COLUMN status_new TO status');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE permintaan MODIFY status ENUM('menunggu','disetujui','ditolak','kadaluarsa') NOT NULL DEFAULT 'menunggu'");

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE permintaan DROP CONSTRAINT IF EXISTS permintaan_status_check');
            DB::statement("ALTER TABLE permintaan ADD CONSTRAINT permintaan_status_check CHECK (status IN ('menunggu','disetujui','ditolak','kadaluarsa'))");

            return;
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('permintaan', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("ALTER TABLE permintaan ADD COLUMN status_old TEXT CHECK(status_old IN ('menunggu','disetujui','ditolak')) DEFAULT 'menunggu' NOT NULL");
            DB::statement("UPDATE permintaan SET status_old = CASE WHEN status = 'kadaluarsa' THEN 'menunggu' ELSE status END");
            DB::statement('ALTER TABLE permintaan DROP COLUMN status');
            DB::statement('ALTER TABLE permintaan RENAME COLUMN status_old TO status');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE permintaan MODIFY status ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu'");
        }

        if ($driver === 'pgsql') {
            DB::table('permintaan')
                ->where('status', 'kadaluarsa')
                ->update(['status' => 'menunggu']);

            DB::statement('ALTER TABLE permintaan DROP CONSTRAINT IF EXISTS permintaan_status_check');
            DB::statement("ALTER TABLE permintaan ADD CONSTRAINT permintaan_status_check CHECK (status IN ('menunggu','disetujui','ditolak'))");
        }
    }
};
