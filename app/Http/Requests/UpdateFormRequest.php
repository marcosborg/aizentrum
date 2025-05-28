<?php

namespace App\Http\Requests;

use App\Models\Form;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateFormRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('form_edit');
    }

    public function rules()
    {
        return [
            'name' => [
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
