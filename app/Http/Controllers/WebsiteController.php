<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\LogMessage;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{

    public function assistant($assistant_id)
    {

        $assistant = Assistant::find($assistant_id)->load('project');

        return view('website.home', compact('assistant'));
    }

    public function storeThreadInSession($thread_id)
    {
        session()->put('thread_id', $thread_id);
        return session()->get('thread_id');
    }

    public function storeLogInSession($log_id)
    {
        session()->put('log_id', $log_id);
        return session()->get('log_id');
    }

    public function checkIfThreadInSession()
    {
        return session()->get('thread_id');
    }

    public function checkIfLogInSession()
    {
        return session()->get('log_id');
    }

    public function getAllMessages($log_id)
    {
        return LogMessage::where('log_id', $log_id)->get();
    }

    public function suporteTecnico()
    {
        return view('website.suporte_tecnico');
    }

}
