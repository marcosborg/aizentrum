<?php

namespace App\Http\Requests;

use App\Models\MoloniInvoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMoloniInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('moloni_invoice_edit');
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