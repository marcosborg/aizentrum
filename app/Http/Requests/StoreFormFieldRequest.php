<?php

namespace App\Http\Requests;

use App\Models\FormField;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFormFieldRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('form_field_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'label' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'form_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
