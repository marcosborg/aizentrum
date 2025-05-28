<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInstructionRequest;
use App\Http\Requests\StoreInstructionRequest;
use App\Http\Requests\UpdateInstructionRequest;
use App\Models\Assistant;
use App\Models\Instruction;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstructionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('instruction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $instructions = Instruction::with(['assistant'])->get();

        return view('admin.instructions.index', compact('instructions'));
    }

    public function create()
    {
        abort_if(Gate::denies('instruction_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistants = Assistant::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.instructions.create', compact('assistants'));
    }

    public function store(StoreInstructionRequest $request)
    {
        $instruction = Instruction::create($request->all());

        return redirect()->route('admin.instructions.index');
    }

    public function edit(Instruction $instruction)
    {
        abort_if(Gate::denies('instruction_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistants = Assistant::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $instruction->load('assistant');

        return view('admin.instructions.edit', compact('assistants', 'instruction'));
    }

    public function update(UpdateInstructionRequest $request, Instruction $instruction)
    {
        $instruction->update($request->all());

        return redirect()->route('admin.instructions.index');
    }

    public function show(Instruction $instruction)
    {
        abort_if(Gate::denies('instruction_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $instruction->load('assistant');

        return view('admin.instructions.show', compact('instruction'));
    }

    public function destroy(Instruction $instruction)
    {
        abort_if(Gate::denies('instruction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $instruction->delete();

        return back();
    }

    public function massDestroy(MassDestroyInstructionRequest $request)
    {
        $instructions = Instruction::find(request('ids'));

        foreach ($instructions as $instruction) {
            $instruction->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
