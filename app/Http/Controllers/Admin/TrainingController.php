<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\ChatContact;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Assistant;
use App\Models\Instruction;
use App\Http\Controllers\Traits\OpenAi;
use App\Http\Controllers\Traits\PrestashopApi;
use Illuminate\Support\Facades\Notification;
use App\Models\Log;
use App\Models\LogMessage;

class TrainingController extends Controller
{
    use OpenAi;
    use PrestashopApi;

    public function index()
    {
        abort_if(Gate::denies('training_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistants = Assistant::all()->load('project');

        return view('admin.trainings.index', compact('assistants'));
    }

    public function assistant($assistant_id)
    {
        abort_if(Gate::denies('training_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistant = Assistant::find($assistant_id)->load('project');

        return view('admin.trainings.assistant', compact('assistant'));
    }

    public function instructionsCreate(Request $request)
    {

        $request->validate([
            'text' => 'required'
        ], [], [
            'text' => 'Instruction'
        ]);

        $assistant_id = $request->assistant_id;
        $text = $request->text;

        $instructions = Instruction::where('assistant_id', $assistant_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($instructions) {
            $nextPosition = $instructions->position + 1;
        } else {
            $nextPosition = 1;
        }

        $active = 1;

        $instruction = new Instruction;
        $instruction->assistant_id = $assistant_id;
        $instruction->text = $text;
        $instruction->position = $nextPosition;
        $instruction->active = $active;
        $instruction->save();

    }

    public function instructionsLoad($assistant_id)
    {
        $instructions = Instruction::where('assistant_id', $assistant_id)->orderBy('position')->get();

        return view('admin.trainings.instructions-load', compact('instructions'));
    }

    public function instructionDelete($instruction_id)
    {
        Instruction::find($instruction_id)->delete();
    }

    public function syncAssistant(Request $request)
    {

        $assistant_id = $request->assistant_id;
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $instructions = $request->instructions;
        $assist_code = $assistant->assist_code;

        return $this->modifyAssistant($openaiApiKey, $assist_code, $instructions);
    }

    public function chatCreateThreadAndRun(Request $request)
    {
        $assistant_id = $request->assistant_id;
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $assist_code = $assistant->assist_code;
        $message = $request->message;

        return $this->createThreadAndRun($openaiApiKey, $assist_code, $message);
    }

    public function chatListRunSteps($thread_id, $run_id)
    {
        return $this->listRunSteps(env('OPENAI_API_KEY'), $thread_id, $run_id);
    }

    public function getRunStatus($thread_id, $run_id)
    {
        return $this->retrieveRun(env('OPENAI_API_KEY'), $thread_id, $run_id);
    }

    public function getMessages($thread_id)
    {
        return $this->listMessages(env('OPENAI_API_KEY'), $thread_id);
    }

    public function addMessage(Request $request)
    {

        $openaiApiKey = env('OPENAI_API_KEY');
        $thread_id = $request->thread_id;
        $message = $request->message;

        return $this->createMessage($openaiApiKey, $thread_id, $message);
    }

    public function runTheThread($assistant_id, $thread_id)
    {

        $openaiApiKey = env('OPENAI_API_KEY');
        $assist_code = Assistant::find($assistant_id)->assist_code;

        return $this->createRun($openaiApiKey, $assist_code, $thread_id);
    }

    public function apiSearch($assistant_id, $search)
    {
        $assistant = Assistant::find($assistant_id)->load('project');

        switch ($assistant->project->name) {
            case 'Techniczentrum':
                $website = 'https://airbagszentrum.com';
                break;
            case 'Electriczentrum':
                $website = 'https://airbagszentrum.com';
                break;
            default:
                $website = 'https://airbagszentrum.com';
                break;
        }

        return $this->zentrumSearch($website, $search);

    }

    public function chatSubmitToolOutputsToRun(Request $request)
    {

        $openaiApiKey = env('OPENAI_API_KEY');
        $thread_id = $request->thread_id;
        $run_id = $request->run_id;
        $tool_call_id = $request->tool_call_id;
        $output = $request->output;

        return $this->submitToolOutputsToRun($openaiApiKey, $thread_id, $run_id, $tool_call_id, $output);
    }

    public function sendEmail(Request $request)
    {
        $data = $request->data;

        Notification::route('mail', 'm.borges.mail@gmail.com')
            ->notify(new ChatContact($data));
    }

    public function log(Request $request)
    {
        $log_id = $request->log_id;
        $project = $request->project;
        $role = $request->role;
        $message = $request->message;

        if (!$log_id) {
            $log = new Log;
            $log->project = $project;
            $log->save();
            $log_id = $log->id;
        }

        $log_message = new LogMessage;
        $log_message->log_id = $log_id;
        $log_message->role = $role;
        $log_message->message = $message;
        $log_message->save();

        return $log_message;

    }

}
