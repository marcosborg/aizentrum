<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TechnicalAssistanteController extends Controller
{
    public function responder(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|min:3',
            'contexto' => 'nullable|array',
        ]);

        $mensagemUser = $request->input('mensagem');
        $contexto = $request->input('contexto');

        // Histórico da conversa na sessão
        $historico = session()->get('chat_history', []);

        // Construímos a mensagem de contexto técnico apenas se existir
        if ($contexto) {
            $mensagemContexto = "Contexto Técnico: 
- Número da Fatura: {$contexto['invoice_number']}
- Produto: {$contexto['product']}
- Veículo: {$contexto['car']}
- Comercial: {$contexto['comercial']}";
        } else {
            $mensagemContexto = "Contexto técnico não fornecido.";
        }

        // Prepara as mensagens para o GPT: sistema + histórico + nova pergunta
        $messages = [
            [
                'role' => 'system',
                'content' => 'És o Zé da Zentrum, um assistente técnico pós-venda amigável. Ajuda os clientes a esclarecer dúvidas sobre peças recebidas e problemas técnicos, sempre com clareza e simpatia.'
            ],
            [
                'role' => 'system',
                'content' => $mensagemContexto
            ],
        ];

        // Junta o histórico anterior
        foreach ($historico as $mensagem) {
            $messages[] = $mensagem;
        }

        // Adiciona a nova pergunta do utilizador
        $messages[] = ['role' => 'user', 'content' => $mensagemUser];

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if (!$response->successful()) {
                Log::error('Erro na API OpenAI: ' . $response->body());
                return response()->json([
                    'resposta' => 'Desculpa, ocorreu um erro ao tentar responder (API). Tenta novamente mais tarde.'
                ], 500);
            }

            $respostaTexto = $response->json()['choices'][0]['message']['content'];

            // Atualiza o histórico na sessão
            $historico[] = ['role' => 'user', 'content' => $mensagemUser];
            $historico[] = ['role' => 'assistant', 'content' => $respostaTexto];
            session()->put('chat_history', $historico);

            return response()->json(['resposta' => $respostaTexto]);
        } catch (\Exception $e) {
            Log::error('Exceção na chamada ao GPT: ' . $e->getMessage());
            return response()->json([
                'resposta' => 'Desculpa, ocorreu um erro ao tentar responder. Por favor tenta novamente mais tarde.'
            ], 500);
        }
    }

    public function resetChat()
    {
        session()->forget('chat_history');
        return response()->json(['success' => true]);
    }
}
