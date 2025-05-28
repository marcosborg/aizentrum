<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Iftech;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\OpenAi;
use App\Models\Company;

class ZcmController extends Controller
{

    use Iftech;
    use OpenAi;

    public function index()
    {
        abort_if(Gate::denies('zcm_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.zcms.index');
    }

    public function orders(Request $request)
    {

        $access_token =  $this->login();
        $orders = $this->zcmOrders($access_token, $request->company, $request->start_date, $request->end_date);
        $newArray = [];
        foreach ($orders['orders'] as $order) {
            $newArray[] = [
                'id' => $order['id'],
                'date' => $order['created_at'],
                'client' => $order['client']['name'],
                'salesman' => $order['user']['name'],
                'calltype' => $order['call_type']['name'],
                'status' => $order['status']['name'],
                'car' => ($order['requestlines'] ? $order['requestlines']['car']['brand'] ?? '' : '') . ' ' . ($order['requestlines'] ? $order['requestlines']['car']['brand'] ?? '' : ''),
                'product' => $order['requestlines']['product']['name'] ?? '',
                'request' => $order['requestlines']['obs'] ?? '',
                'declined' => $order['declined'] ? $order['declined']['desc'] : '',
            ];
        }

        return view('admin.zcms.ajax', compact('newArray'));
    }

    public function aiChat(Request $request)
    {

        $openaiApiKey = env('OPENAI_API_KEY');
        $assistant_code = 'asst_Y9W4ZeLytuyp1vU6oG5211Ow';

        if ($request->thread_id == NULL) {
            // ✅ Verifica se o request contém 'message'
            if (!$request->has('message')) {
                return response()->json(['error' => 'O campo message é obrigatório.'], 400);
            }

            // ✅ Decodifica o JSON para um array
            $decodedMessages = json_decode($request->message, true);

            // ❌ Se a decodificação falhar, retorna erro
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'JSON inválido: ' . json_last_error_msg()], 400);
            }

            // ✅ Formatar a mensagem inicial de maneira clara para o OpenAI
            $formattedMessage = "Aqui estão os pedidos recebidos para análise:\n\n";
            foreach ($decodedMessages as $msg) {
                $formattedMessage .= "Cliente: {$msg['client']}\n";
                $formattedMessage .= "Data: {$msg['date']}\n";
                $formattedMessage .= "Vendedor: {$msg['salesman']}\n";
                $formattedMessage .= "Tipo de Chamada: {$msg['calltype']}\n";
                $formattedMessage .= "Status: {$msg['status']}\n";
                $formattedMessage .= "Carro: {$msg['car']}\n";
                $formattedMessage .= "Produto: {$msg['product']}\n";
                $formattedMessage .= "Solicitação: {$msg['request']}\n";
                $formattedMessage .= "Recusa: {$msg['declined']}\n";
                $formattedMessage .= "-------------------------\n";
            }
            $inicial_run = $this->createThreadAndRun($openaiApiKey, $assistant_code, $formattedMessage);
            $run = $this->retrieveRun($openaiApiKey, $inicial_run->thread_id, $inicial_run->id);
            if ($run->status == 'in_progress' || $run->status == 'queued') {
                while ($run->status == 'in_progress' || $run->status == 'queued') {
                    sleep(1);
                    $run = $this->retrieveRun($openaiApiKey, $run->thread_id, $run->id);
                }
            }
            $messages = $this->listMessages($openaiApiKey, $run->thread_id);
            return [
                'message' => $messages->data[0]->content[0]->text->value,
                'thread_id' => $run->thread_id,
            ];
        } else {
            $this->createMessage($openaiApiKey, $request->thread_id, $request->message);
            $run = $this->createRun($openaiApiKey, $assistant_code, $request->thread_id);
            if ($run->status == 'in_progress' || $run->status == 'queued') {
                while ($run->status == 'in_progress' || $run->status == 'queued') {
                    sleep(1);
                    $run = $this->retrieveRun($openaiApiKey, $run->thread_id, $run->id);
                }
            }
            $messages = $this->listMessages($openaiApiKey, $run->thread_id);
            return [
                'message' => $messages->data[0]->content[0]->text->value,
                'thread_id' => $run->thread_id,
            ];
        }
    }
}
