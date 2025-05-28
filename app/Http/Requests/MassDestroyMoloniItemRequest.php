<?php

namespace App\Http\Requests;

use App\Models\MoloniItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMoloniItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('moloni_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:moloni_items,id',
        ];
    }
}
