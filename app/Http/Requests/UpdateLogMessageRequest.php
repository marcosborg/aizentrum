<?php

namespace App\Http\Requests;

use App\Models\LogMessage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateLogMessageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('log_message_edit');
    }

    public function rules()
    {
        return [
            'log_id' => [
                'required',
                'integer',
            ],
            'role' => [
                'required',
            ],
            'message' => [
                'required',
            ],
        ];
    }
}
