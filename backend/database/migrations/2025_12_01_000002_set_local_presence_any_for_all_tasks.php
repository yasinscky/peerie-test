<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tasks')->update([
            'local_presence_options' => 'any',
        ]);
    }

    public function down(): void
    {
        DB::table('tasks')
            ->where('local_presence_options', 'any')
            ->update([
                'local_presence_options' => null,
            ]);
    }
};


