<?php

namespace App\Http\Requests;

use App\Models\MoloniInvoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMoloniInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('moloni_invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:moloni_invoices,id',
        ];
    }
}
