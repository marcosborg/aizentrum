<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->moveMileageAfter(['vin', 'chassis', 'chassi', 'registration']);
    }

    public function down(): void
    {
        $this->moveMileageAfter(['year', 'model', 'brand_model', 'brand']);
    }

    private function moveMileageAfter(array $anchorNames): void
    {
        DB::transaction(function () use ($anchorNames): void {
            $formIds = DB::table('forms')
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->pluck('id');

            foreach ($formIds as $formId) {
                $fields = DB::table('form_fields')
                    ->where('form_id', $formId)
                    ->whereNull('deleted_at')
                    ->orderBy('position')
                    ->orderBy('id')
                    ->get(['id', 'name']);

                $mileage = $fields->firstWhere('name', 'kms');

                if (! $mileage) {
                    continue;
                }

                $ordered = $fields->reject(fn ($field) => $field->id === $mileage->id)->values();
                $anchorIndex = null;

                foreach ($anchorNames as $anchorName) {
                    $candidateIndex = $ordered->search(fn ($field) => $field->name === $anchorName);

                    if ($candidateIndex !== false) {
                        $anchorIndex = $candidateIndex;
                        break;
                    }
                }

                if ($anchorIndex === null) {
                    continue;
                }

                $ordered->splice($anchorIndex + 1, 0, [$mileage]);

                foreach ($ordered as $index => $field) {
                    DB::table('form_fields')
                        ->where('id', $field->id)
                        ->update(['position' => $index + 1]);
                }
            }
        });
    }
};
