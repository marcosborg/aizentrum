<?php

namespace App\Http\Requests;

use App\Models\MoloniItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMoloniItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('moloni_item_edit');
    }

    public function rules()
    {
        return [
            'moloni_invoice_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'required',
            ],
            'qty' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
