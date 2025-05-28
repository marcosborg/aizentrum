<?php

namespace App\Http\Requests;

use App\Models\Log;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreLogRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('log_create');
    }

    public function rules()
    {
        return [
            'project' => [
                'string',
                'required',
            ],
        ];
    }
}
