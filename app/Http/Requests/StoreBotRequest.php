<?php

namespace App\Http\Requests;

use App\Models\Bot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bot_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'instructions' => [
                'required',
            ],
        ];
    }
}
