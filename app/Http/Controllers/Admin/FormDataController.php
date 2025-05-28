<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFormDataRequest;
use App\Http\Requests\StoreFormDataRequest;
use App\Http\Requests\UpdateFormDataRequest;
use App\Models\Form;
use App\Models\FormData;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormDataController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('form_data_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formDatas = FormData::with(['form'])->get();

        return view('admin.formDatas.index', compact('formDatas'));
    }

    public function create()
    {
        abort_if(Gate::denies('form_data_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = Form::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.formDatas.create', compact('forms'));
    }

    public function store(StoreFormDataRequest $request)
    {
        $formData = FormData::create($request->all());

        return redirect()->route('admin.form-datas.index');
    }

    public function edit(FormData $formData)
    {
        abort_if(Gate::denies('form_data_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = Form::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $formData->load('form');

        return view('admin.formDatas.edit', compact('formData', 'forms'));
    }

    public function update(UpdateFormDataRequest $request, FormData $formData)
    {
        $formData->update($request->all());

        return redirect()->route('admin.form-datas.index');
    }

    public function show(FormData $formData)
    {
        abort_if(Gate::denies('form_data_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData->load('form');

        return view('admin.formDatas.show', compact('formData'));
    }

    public function destroy(FormData $formData)
    {
        abort_if(Gate::denies('form_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData->delete();

        return back();
    }

    public function massDestroy(MassDestroyFormDataRequest $request)
    {
        $formDatas = FormData::find(request('ids'));

        foreach ($formDatas as $formData) {
            $formData->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
