<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOpenaiRequest;
use App\Http\Requests\StoreOpenaiRequest;
use App\Http\Requests\UpdateOpenaiRequest;
use App\Models\Openai;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OpenaiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('openai_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $openais = Openai::all();

        return view('admin.openais.index', compact('openais'));
    }

    public function create()
    {
        abort_if(Gate::denies('openai_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.openais.create');
    }

    public function store(StoreOpenaiRequest $request)
    {
        $openai = Openai::create($request->all());

        return redirect()->route('admin.openais.index');
    }

    public function edit(Openai $openai)
    {
        abort_if(Gate::denies('openai_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.openais.edit', compact('openai'));
    }

    public function update(UpdateOpenaiRequest $request, Openai $openai)
    {
        $openai->update($request->all());

        return redirect()->route('admin.openais.index');
    }

    public function show(Openai $openai)
    {
        abort_if(Gate::denies('openai_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.openais.show', compact('openai'));
    }

    public function destroy(Openai $openai)
    {
        abort_if(Gate::denies('openai_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $openai->delete();

        return back();
    }

    public function massDestroy(MassDestroyOpenaiRequest $request)
    {
        $openais = Openai::find(request('ids'));

        foreach ($openais as $openai) {
            $openai->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
