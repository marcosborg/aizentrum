<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyFormRequest;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\Form;
use App\Models\Project;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class FormController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('form_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = Form::with(['project', 'media'])->get();

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        abort_if(Gate::denies('form_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.forms.create', compact('projects'));
    }

    public function store(StoreFormRequest $request)
    {
        $form = Form::create($request->all());

        if ($request->input('logo', false)) {
            $form->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $form->id]);
        }

        return redirect()->route('admin.forms.index');
    }

    public function edit(Form $form)
    {
        abort_if(Gate::denies('form_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $form->load('project');

        return view('admin.forms.edit', compact('form', 'projects'));
    }

    public function update(UpdateFormRequest $request, Form $form)
    {
        $form->update($request->all());

        if ($request->input('logo', false)) {
            if (! $form->logo || $request->input('logo') !== $form->logo->file_name) {
                if ($form->logo) {
                    $form->logo->delete();
                }
                $form->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
            }
        } elseif ($form->logo) {
            $form->logo->delete();
        }

        return redirect()->route('admin.forms.index');
    }

    public function show(Form $form)
    {
        abort_if(Gate::denies('form_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $form->load('project');

        return view('admin.forms.show', compact('form'));
    }

    public function destroy(Form $form)
    {
        abort_if(Gate::denies('form_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $form->delete();

        return back();
    }

    public function massDestroy(MassDestroyFormRequest $request)
    {
        $forms = Form::find(request('ids'));

        foreach ($forms as $form) {
            $form->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('form_create') && Gate::denies('form_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Form();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
