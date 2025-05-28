<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use App\Models\Form;
use App\Models\FormField;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\FormData;

class FormsAssemblyController extends Controller
{

    use MediaUploadingTrait;

    public function index($form_id = null)
    {

        abort_if(Gate::denies('forms_assembly_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all();

        if (session()->has('project_id')) {
            $project_id = session()->get('project_id');
        } else {
            $project_id = $projects[0]->id;
            session()->put('project_id', $project_id);
        }

        $forms = Form::where('project_id', $project_id)
            ->get();

        if ($forms->count() > 0 && !$form_id) {
            $form_id = $forms[0]->id;
        }

        return view('admin.formsAssemblies.index', compact([
            'projects',
            'project_id',
            'forms',
            'form_id'
        ]));
    }

    public function changeProjectId($project_id)
    {
        session()->put('project_id', $project_id);
        return redirect('/admin/forms-assemblies');
    }

    public function createFormField(Request $request)
    {
        $request->validate([
            'name' => [
                'string',
                'required',
            ],
            'label' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'form_id' => [
                'required',
                'integer',
            ],
        ]);

        $last_form_field = FormField::where('form_id', $request->form_id)
            ->orderBy('position', 'desc')
            ->first();

        $position = 1;

        if ($last_form_field) {
            $position = $last_form_field->position + 1;
        }

        $form_field = new FormField;
        $form_field->name = $request->name;
        $form_field->label = $request->label;
        $form_field->type = $request->type;
        $form_field->form_id = $request->form_id;
        $form_field->position = $position;
        $form_field->save();
    }

    public function formAjax($form_id)
    {
        $form = Form::where('id', $form_id)->with('form_fields')->first();

        return view('admin.formsAssemblies.form_ajax', compact('form'));
    }

    public function updatePositions(Request $request)
    {
        $data = json_decode($request->data);

        foreach ($data as $value) {
            $form_field = FormField::find($value->id);
            $form_field->position = $value->position;
            $form_field->save();
        }

    }

    public function newForm(Request $request)
    {
        $form = new Form;
        $form->name = $request->name;
        $form->project_id = $request->project_id;
        $form->save();

        return $form;
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

    public function deleteField($form_field_id)
    {
        return FormField::find($form_field_id)->delete();
    }

    public function deleteForm($form_id)
    {
        return Form::find($form_id)->delete();
    }

    public function formSend(Request $request)
    {

        return $request;

        $form_data = new FormData;
        $form_data->form_id = $request->form_id;
        $form_data->data = json_encode($request->data);
        $form_data->save();
    }

}
