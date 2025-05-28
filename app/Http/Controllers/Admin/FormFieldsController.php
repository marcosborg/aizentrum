<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFormFieldRequest;
use App\Http\Requests\StoreFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Models\Form;
use App\Models\FormField;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormFieldsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('form_field_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formFields = FormField::with(['form'])->get();

        return view('admin.formFields.index', compact('formFields'));
    }

    public function create()
    {
        abort_if(Gate::denies('form_field_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = Form::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.formFields.create', compact('forms'));
    }

    public function store(StoreFormFieldRequest $request)
    {
        $formField = FormField::create($request->all());

        return redirect()->route('admin.form-fields.index');
    }

    public function edit(FormField $formField)
    {
        abort_if(Gate::denies('form_field_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = Form::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $formField->load('form');

        return view('admin.formFields.edit', compact('formField', 'forms'));
    }

    public function update(UpdateFormFieldRequest $request, FormField $formField)
    {
        $formField->update($request->all());

        return redirect()->route('admin.form-fields.index');
    }

    public function show(FormField $formField)
    {
        abort_if(Gate::denies('form_field_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formField->load('form');

        return view('admin.formFields.show', compact('formField'));
    }

    public function destroy(FormField $formField)
    {
        abort_if(Gate::denies('form_field_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formField->delete();

        return back();
    }

    public function massDestroy(MassDestroyFormFieldRequest $request)
    {
        $formFields = FormField::find(request('ids'));

        foreach ($formFields as $formField) {
            $formField->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
