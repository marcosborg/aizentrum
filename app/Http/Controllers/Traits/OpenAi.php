<?php

namespace App\Http\Controllers\Traits;

trait OpenAi
{
    public function modifyAssistant($openaiApiKey, $assistant_id, $instructions)
    {
        $ch = curl_init('https://api.openai.com/v1/assistants/' . $assistant_id);

        $data = json_encode([
            "instructions" => $instructions,
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openaiApiKey,
            'OpenAI-Beta: assistants=v2'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function createThreadAndRun($openaiApiKey, $assistant_code, $message)
    {

        $payload = [
            "assistant_id" => $assistant_code,
            "thread" => [
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $message
                    ]
                ]
            ]
        ];

        // ✅ Codificar corretamente o JSON
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.openai.com/v1/threads/runs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function listRunSteps($openaiApiKey, $thread_id, $run_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id . '/steps',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function retrieveRun($openaiApiKey, $thread_id, $run_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function listMessages($openaiApiKey, $thread_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function createMessage($openaiApiKey, $thread_id, $message)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
              "role": "user",
              "content": "' . $message . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function createRun($openaiApiKey, $assist_code, $thread_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"assistant_id": "' . $assist_code . '"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function submitToolOutputsToRun($openaiApiKey, $thread_id, $run_id, $tool_call_id, $output)
    {

        $data = [
            "tool_outputs" => [
                [
                    "tool_call_id" => $tool_call_id,
                    "output" => $output
                ]
            ]
        ];

        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id . '/submit_tool_outputs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v2',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}
