<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAssistantRequest;
use App\Http\Requests\StoreAssistantRequest;
use App\Http\Requests\UpdateAssistantRequest;
use App\Models\Assistant;
use App\Models\Project;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssistantController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('assistant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistants = Assistant::with(['project'])->get();

        return view('admin.assistants.index', compact('assistants'));
    }

    public function create()
    {
        abort_if(Gate::denies('assistant_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.assistants.create', compact('projects'));
    }

    public function store(StoreAssistantRequest $request)
    {
        $assistant = Assistant::create($request->all());

        return redirect()->route('admin.assistants.index');
    }

    public function edit(Assistant $assistant)
    {
        abort_if(Gate::denies('assistant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assistant->load('project');

        return view('admin.assistants.edit', compact('assistant', 'projects'));
    }

    public function update(UpdateAssistantRequest $request, Assistant $assistant)
    {
        $assistant->update($request->all());

        return redirect()->route('admin.assistants.index');
    }

    public function show(Assistant $assistant)
    {
        abort_if(Gate::denies('assistant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistant->load('project');

        return view('admin.assistants.show', compact('assistant'));
    }

    public function destroy(Assistant $assistant)
    {
        abort_if(Gate::denies('assistant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistant->delete();

        return back();
    }

    public function massDestroy(MassDestroyAssistantRequest $request)
    {
        $assistants = Assistant::find(request('ids'));

        foreach ($assistants as $assistant) {
            $assistant->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
