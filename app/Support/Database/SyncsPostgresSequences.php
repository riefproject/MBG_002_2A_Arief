<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait SyncsPostgresSequences
{
    protected function syncPostgresSequence(string $table, string $column = 'id'): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $max = DB::table($table)->max($column);
        $sequence = "pg_get_serial_sequence('{$table}','{$column}')";

        if ($max === null) {
            DB::statement("SELECT setval({$sequence}, 1, false)");

            return;
        }

        DB::statement("SELECT setval({$sequence}, {$max}, true)");
    }
}
