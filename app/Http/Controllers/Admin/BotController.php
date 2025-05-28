<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBotRequest;
use App\Http\Requests\StoreBotRequest;
use App\Http\Requests\UpdateBotRequest;
use App\Models\Bot;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BotController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('bot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bots = Bot::all();

        return view('admin.bots.index', compact('bots'));
    }

    public function create()
    {
        abort_if(Gate::denies('bot_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bots.create');
    }

    public function store(StoreBotRequest $request)
    {
        $bot = Bot::create($request->all());

        return redirect()->route('admin.bots.index');
    }

    public function edit(Bot $bot)
    {
        abort_if(Gate::denies('bot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bots.edit', compact('bot'));
    }

    public function update(UpdateBotRequest $request, Bot $bot)
    {
        $bot->update($request->all());

        return redirect()->route('admin.bots.index');
    }

    public function show(Bot $bot)
    {
        abort_if(Gate::denies('bot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bots.show', compact('bot'));
    }

    public function destroy(Bot $bot)
    {
        abort_if(Gate::denies('bot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bot->delete();

        return back();
    }

    public function massDestroy(MassDestroyBotRequest $request)
    {
        $bots = Bot::find(request('ids'));

        foreach ($bots as $bot) {
            $bot->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
