@extends('layouts.admin')
@section('content')

<div class="row">
    <!-- Formulário de pedidos -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                {{ trans('cruds.zcm.title') }}
            </div>

            <div class="card-body">
                <form action="/admin/zcms/orders" method="POST" id="zcm_orders">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Empresa</label>
                                <select class="form-control" name="company" id="company" required>
                                    <option selected disabled>Selecione</option>
                                    <option value="1">Airbags</option>
                                    <option value="2">Technic</option>
                                    <option value="3">Electric</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data de início</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data de fim</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                            </div>
                        </div>
                        <div class="offset-3 col-md-6">
                            <button type="submit" class="btn btn-primary btn-block">Obter dados</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Seção do Chat do Assistente AI -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Assistente Zentrum AI
            </div>
            <div class="card-body">
                <!-- Texto inicial sempre visível -->
                <div id="zcm_assistant_inicial_text">
                    <p>Este assistente foi desenvolvido para facilitar a obtenção de dados de pedidos do Zentrum AI.</p>
                    <p>Para obter os dados, informe a data de início e a data de fim do período desejado e clique em "Obter dados".</p>
                    <p>Os dados serão exibidos na tabela abaixo.</p>
                </div>

                <!-- Área do chat (oculta inicialmente) -->
                <div id="zcm_assistant" class="chat-box" style="display: none;"></div>

                <!-- Campo de entrada e botão de envio (também ocultos inicialmente) -->
                <div id="chat_controls" class="input-group mt-3" style="display: none;">
                    <input type="text" id="chat_input" class="form-control" placeholder="Digite sua pergunta...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="send_message">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Área onde os pedidos serão exibidos -->
<div id="orders_result"></div>

@endsection

@section('scripts')
<script>
    var thread_id = null;
    // Formulário AJAX para pedidos
    $('#zcm_orders').ajaxForm({
        beforeSubmit: function() {
            $.LoadingOverlay('show');
        }
        , success: function(resp) {
            $.LoadingOverlay('hide');
            $('#zcm_assistant_inicial_text').LoadingOverlay('show');
            $('#orders_result').html(resp);
            thread_id = null;
            let data = $('#zcm_data').data('info');
            $.ajax({
                url: '{{ url("admin/zcms/ai-chat") }}', // Endpoint para processar IA
                method: 'POST'
                , data: {
                    message: JSON.stringify(data)
                    , thread_id: thread_id
                , }
                , success: function(response) {
                    console.log(response);
                    $('#zcm_assistant_inicial_text').LoadingOverlay('hide');
                    $('#zcm_assistant_inicial_text').hide();
                    $('#zcm_assistant').show();
                    $('#chat_controls').show();
                    thread_id = response.thread_id;
                    appendMessage('assistant', response.message);
                }
                , error: function(err) {
                    $('#zcm_assistant_inicial_text').LoadingOverlay('hide');
                    $('#zcm_assistant_inicial_text').hide();
                    $('#zcm_assistant').show();
                    console.log(err);
                    appendMessage('assistant', 'Ocorreu um erro. Tente novamente.');
                }
            });
        }
        , error: function(err) {
            $.LoadingOverlay('hide');
            console.log(err);
        }
    });

    // Função para adicionar mensagens ao chat
    function appendMessage(sender, message) {
        $('#zcm_assistant').append(`
            <div class="chat-message ${sender}">
                <strong>${sender === 'user' ? 'Você' : 'Assistente'}:</strong> ${message}
            </div>
        `);
        $('#zcm_assistant').scrollTop($('#zcm_assistant')[0].scrollHeight);
    }

    // Envio de mensagem ao clicar no botão
    $('#send_message').click(function() {
        let userMessage = $('#chat_input').val().trim();
        if (userMessage === '') return;

        appendMessage('user', userMessage);
        $('#chat_input').val('');

        $('#chat_controls').LoadingOverlay('show');

        $.ajax({
            url: '{{ url('admin/zcms/ai-chat') }}', // Endpoint para processar IA
            method: 'POST',
            data: {
                message: userMessage,
                thread_id: thread_id,
            },
            success: function(response) {
                console.log(response);
                $('#chat_controls').LoadingOverlay('hide');
                appendMessage('assistant', response.message);
            },
            error: function(err) {
                $('#chat_controls').LoadingOverlay('hide');
                console.log(err);
                appendMessage('assistant', 'Ocorreu um erro. Tente novamente.');
            }
        });
    });

    // Envio de mensagem ao pressionar Enter
    $('#chat_input').keypress(function(e) {
        if (e.which === 13) {
            $('#send_message').click();
        }
    });

</script>
@endsection

@section('styles')
<style>
    .chat-box {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .chat-message {
        padding: 8px;
        margin-bottom: 5px;
        border-radius: 5px;
    }

    .chat-message.user {
        background: #d1ecf1;
        text-align: right;
    }

    .chat-message.assistant {
        background: #d4edda;
        text-align: left;
    }

</style>
@endsection
