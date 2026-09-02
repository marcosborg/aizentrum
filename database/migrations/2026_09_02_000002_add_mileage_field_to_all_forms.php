<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $forms = DB::table('forms')
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get(['id']);

            foreach ($forms as $form) {
                $fields = DB::table('form_fields')
                    ->where('form_id', $form->id)
                    ->whereNull('deleted_at')
                    ->orderBy('position')
                    ->get(['name', 'position']);

                if ($fields->contains('name', 'kms')) {
                    continue;
                }

                $anchor = $fields->firstWhere('name', 'year')
                    ?? $fields->firstWhere('name', 'model')
                    ?? $fields->firstWhere('name', 'brand_model')
                    ?? $fields->firstWhere('name', 'brand');

                $position = $anchor
                    ? ((int) $anchor->position + 1)
                    : ((int) $fields->max('position') + 1);

                DB::table('form_fields')
                    ->where('form_id', $form->id)
                    ->whereNull('deleted_at')
                    ->where('position', '>=', $position)
                    ->increment('position');

                DB::table('form_fields')->insert([
                    'name' => 'kms',
                    'label' => 'Quilómetros',
                    'type' => 'text',
                    'position' => $position,
                    'form_id' => $form->id,
                    'required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $fields = DB::table('form_fields')
                ->where('name', 'kms')
                ->whereNotIn('form_id', [1, 3])
                ->whereNull('deleted_at')
                ->get(['id', 'form_id', 'position']);

            foreach ($fields as $field) {
                DB::table('form_fields')->where('id', $field->id)->delete();

                DB::table('form_fields')
                    ->where('form_id', $field->form_id)
                    ->whereNull('deleted_at')
                    ->where('position', '>', $field->position)
                    ->decrement('position');
            }
        });
    }
};
