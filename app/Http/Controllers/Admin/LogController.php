<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLogRequest;
use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use App\Models\Log;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logs = Log::all();

        return view('admin.logs.index', compact('logs'));
    }

    public function create()
    {
        abort_if(Gate::denies('log_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logs.create');
    }

    public function store(StoreLogRequest $request)
    {
        $log = Log::create($request->all());

        return redirect()->route('admin.logs.index');
    }

    public function edit(Log $log)
    {
        abort_if(Gate::denies('log_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logs.edit', compact('log'));
    }

    public function update(UpdateLogRequest $request, Log $log)
    {
        $log->update($request->all());

        return redirect()->route('admin.logs.index');
    }

    public function show(Log $log)
    {
        abort_if(Gate::denies('log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logs.show', compact('log'));
    }

    public function destroy(Log $log)
    {
        abort_if(Gate::denies('log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $log->delete();

        return back();
    }

    public function massDestroy(MassDestroyLogRequest $request)
    {
        $logs = Log::find(request('ids'));

        foreach ($logs as $log) {
            $log->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
