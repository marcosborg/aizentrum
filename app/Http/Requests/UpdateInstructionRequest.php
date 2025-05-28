<?php

namespace App\Http\Requests;

use App\Models\Instruction;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateInstructionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('instruction_edit');
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
