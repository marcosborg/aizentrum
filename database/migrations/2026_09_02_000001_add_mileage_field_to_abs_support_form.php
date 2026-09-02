<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            if (DB::table('form_fields')->where('id', 258)->exists()) {
                return;
            }

            DB::table('form_fields')
                ->where('form_id', 1)
                ->where('position', '>', 9)
                ->increment('position');

            DB::table('form_fields')->insert([
                'id' => 258,
                'name' => 'kms',
                'label' => 'Quilómetros',
                'type' => 'text',
                'position' => 10,
                'form_id' => 1,
                'required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            if (! DB::table('form_fields')->where('id', 258)->exists()) {
                return;
            }

            DB::table('form_fields')->where('id', 258)->delete();

            DB::table('form_fields')
                ->where('form_id', 1)
                ->where('position', '>', 10)
                ->decrement('position');
        });
    }
};
