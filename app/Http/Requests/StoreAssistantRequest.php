<?php

namespace App\Http\Requests;

use App\Models\Assistant;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAssistantRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('assistant_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'assist_code' => [
                'string',
                'required',
            ],
            'project_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
