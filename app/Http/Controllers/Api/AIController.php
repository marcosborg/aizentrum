<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;
use App\Models\Bot;

class AIController extends Controller
{

    public function handle(Request $request)
    {
        $user = trim($request->input('user') ?? '');
        $rawMessage = $request->input('message');

        // Validação segura da mensagem e do utilizador
        if ($user === '' || !is_string($rawMessage) || trim($rawMessage) === '') {
            Log::warning('Mensagem inválida ou vazia', ['user' => $user, 'message' => $rawMessage]);
            return response()->json([
                'reply' => 'A tua mensagem parece estar vazia ou num formato não suportado. Envia por favor só texto 😊'
            ], 400);
        }

        $newMessage = trim($rawMessage);

        // Buscar ou criar conversa
        $conversation = Conversation::firstOrCreate(
            ['user' => $user],
            ['messages' => json_encode([])]
        );

        // Validar histórico de mensagens
        $rawMessages = json_decode($conversation->messages, true);
        $messages = [];

        if (is_array($rawMessages)) {
            foreach ($rawMessages as $item) {
                if (
                    is_array($item) &&
                    isset($item['role'], $item['content']) &&
                    is_string($item['content']) &&
                    trim($item['content']) !== ''
                ) {
                    $messages[] = $item;
                }
            }
        }

        // Limitar a 10 mensagens
        $recentHistory = array_slice($messages, -10);

        // Mensagem de sistema
        $systemMessage = Bot::find(1)->instructions;


        // Montar conversa com system, histórico válido e nova mensagem
        $fullConversation = array_merge(
            [['role' => 'system', 'content' => $systemMessage]],
            $recentHistory,
            [['role' => 'user', 'content' => $newMessage]]
        );

        // Chamada à API da OpenAI
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => $fullConversation,
                'temperature' => 0.7,
            ]);

            if (isset($response['choices'][0]['message']['content'])) {
                $botReply = trim($response['choices'][0]['message']['content']);
            } else {
                Log::error('Resposta inesperada da OpenAI', ['response' => $response->json()]);
                $botReply = 'Desculpa, não consegui gerar uma resposta no momento.';
            }
        } catch (\Exception $e) {
            Log::error('Erro ao comunicar com OpenAI', ['exception' => $e]);
            $botReply = 'Desculpa, ocorreu um erro ao tentar responder.';
        }

        // Atualizar histórico
        $messages[] = ['role' => 'user', 'content' => $newMessage];
        $messages[] = ['role' => 'assistant', 'content' => $botReply];

        $conversation->messages = json_encode($messages);
        $conversation->save();

        // Resposta final
        return response()->json([
            'reply' => $botReply
        ]);
    }
}

