<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tasks')
            ->whereNotNull('allowed_capacities')
            ->orderBy('id')
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    $raw = $task->allowed_capacities;

                    if ($raw === null) {
                        continue;
                    }

                    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

                    if (!is_array($decoded)) {
                        continue;
                    }

                    $capacities = array_values(array_unique(array_map(static function ($value) {
                        return (int) $value;
                    }, $decoded)));

                    $filtered = array_values(array_filter($capacities, static function (int $value) {
                        return $value !== 6;
                    }));

                    if ($filtered === $capacities) {
                        continue;
                    }

                    if (count($filtered) === 0) {
                        DB::table('tasks')
                            ->where('id', $task->id)
                            ->update(['allowed_capacities' => null]);
                    } else {
                        DB::table('tasks')
                            ->where('id', $task->id)
                            ->update(['allowed_capacities' => json_encode($filtered)]);
                    }
                }
            });
    }

    public function down(): void
    {
        // No-op: we do not restore removed capacity constraints
    }
};


