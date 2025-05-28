<?php

namespace App\Http\Requests;

use App\Models\Instruction;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInstructionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('instruction_create');
    }

    public function rules()
    {
        return [
            'text' => [
                'required',
            ],
            'position' => [
                'string',
                'required',
            ],
        ];
    }
}
