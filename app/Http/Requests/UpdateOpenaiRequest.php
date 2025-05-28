<?php

namespace App\Http\Requests;

use App\Models\Openai;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOpenaiRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('openai_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'organization' => [
                'string',
                'required',
            ],
            'openai_api_key' => [
                'string',
                'required',
            ],
        ];
    }
}
