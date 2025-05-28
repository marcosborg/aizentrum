<?php

namespace App\Http\Requests;

use App\Models\MoloniInvoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMoloniInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('moloni_invoice_create');
    }

    public function rules()
    {
        return [
            'invoice' => [
                'string',
                'required',
            ],
            'supplier' => [
                'string',
                'required',
            ],
            'file' => [
                'required',
            ],
        ];
    }
}