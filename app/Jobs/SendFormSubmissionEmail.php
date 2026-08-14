<?php

namespace App\Jobs;

use App\Models\FormData;
use App\Notifications\FormSubmit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendFormSubmissionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $formDataId)
    {
    }

    public function handle(): void
    {
        $formData = FormData::with('form.project')->findOrFail($this->formDataId);
        $formData->data = json_decode($formData->data);

        try {
            Notification::route('mail', config('mail.commercial_address'))
                ->notify(new FormSubmit($formData));
        } catch (\Throwable $exception) {
            Log::error('Public form email delivery failed', [
                'form_data_id' => $formData->id,
                'exception' => $exception,
            ]);
        }
    }
}
