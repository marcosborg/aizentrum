<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FormData;

class FormsInboxController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('forms_inbox_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $form_datas = FormData::orderBy('id', 'desc')->get();

        return view('admin.formsInboxes.index', compact('form_datas'));
    }

    public function form($form_data_id)
    {
        abort_if(Gate::denies('forms_inbox_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = FormData::find($form_data_id);
        $formData->data = json_decode($formData->data, true);

        return view('admin.formsInboxes.form', compact('formData'));
    }

}
