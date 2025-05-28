@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.training.title') }}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <div class="card">
                    <img src="https://robohash.org/{{ $assistant->name }}">
                    <div class="card-body">
                        <strong>{{ $assistant->name }}</strong>
                        <p><small>{{ $assistant->project->name }}</small></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success" onclick="modifyAssistant()"
                            id="modify-assistant-button">Sync</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Instructions
                    </div>
                    <div class="card-body">
                        <form action="/admin/trainings/instructions/create" id="instructions_create" method="post">
                            @csrf
                            <input type="hidden" name="assistant_id" value="{{ $assistant->id }}">
                            <div class="form-group">
                                <label>Add instruction</label>
                                <textarea class="form-control" name="text"></textarea>
                            </div>
                            <button class="btn btn-success">Add</button>
                        </form>
                    </div>
                    <ul class="list-group list-group-flush" id="instructions_list"></ul>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Test your assistant
                    </div>
                    <div class="card-body" style="height: 40vh;" id="chat-content"></div>
                    <div class="card-footer" id="message-card-footer">
                        <div class="form-group">
                            <label>Mensagem</label>
                            <textarea class="form-control" id="message-textarea"></textarea>
                        </div>
                        <button type="button" class="btn btn-success" onclick="sendMessage()">Send
                            message</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
@parent
<script>
    const project = "{{ $assistant->project->name }}";
    const assistant_id = {{ $assistant->id }};
    var thread_id = null;
    var log_id = null;
    $(() => {
        $('#instructions_create').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                $.LoadingOverlay('hide');
                loadInstructions();
                $('textarea[name="text"]').val('');
                Swal.fire({
                    title: "Success!",
                    text: "New instruction inserted.",
                    icon: "success"
                });
            },
            error: (error) => {
                $.LoadingOverlay('hide');
                var html = '';
                error.responseJSON.errors.text.forEach(element => {
                    html += element + '<br>';
                });
                Swal.fire({
                    title: 'Validation error',
                    icon: 'error',
                    html: html,
                });
            }
        })
        loadInstructions();
    });

    sendMessage = () => {
        let message = $('#message-textarea').val();
        $('#message-textarea').val('');
        if(message.length > 0){
            let loading = $('#message-card-footer');
            loading.LoadingOverlay('show');
            addMessageToLog('user', message).then((resp) => {
                log_id = resp.log_id;
            }, (error) => {
                console.log(error);
            });
            addMessageToContent ('user', message);
            if(!thread_id){
                createThreadAndRun(message).then((resp) => {
                    thread_id = resp.thread_id;
                    let run_id = resp.id;
                    let interval = setInterval(() => {
                        listRunSteps(thread_id, run_id).then((resp) => {
                            let has_more = resp.has_more;
                            getRunStatus(thread_id, run_id).then((resp) => {
                                let status = resp.status;
                                if(status == 'completed' && has_more == false){
                                    clearInterval(interval);
                                    getMessages(thread_id).then((resp) => {
                                        message = resp.data[0].content[0].text.value;
                                        addMessageToLog('chat', message);
                                        addMessageToContent ('chat', message);
                                        loading.LoadingOverlay('hide');
                                    });
                                }
                            });
                        });
                    }, 2000);
                });
            } else {
                addMessage(thread_id, message).then((resp) => {
                    runTheThread (thread_id).then((resp) => {
                        let run_id = resp.id;
                        let interval = setInterval(() => {
                            listRunSteps(thread_id, run_id).then((resp) => {
                                let type = resp.data[0].type;
                                if(type == 'message_creation') {
                                    let has_more = resp.has_more;
                                    getRunStatus(thread_id, run_id).then((resp) => {
                                        let status = resp.status;
                                        if(status == 'completed' && has_more == false){
                                            clearInterval(interval);
                                            getMessages(thread_id).then((resp) => {
                                                message = resp.data[0].content[0].text.value;
                                                addMessageToLog('chat', message);
                                                addMessageToContent ('chat', message);
                                                loading.LoadingOverlay('hide');
                                            });
                                        }
                                    });
                                } else if (type == 'tool_calls') {
                                    clearInterval(interval);
                                    let function_name = resp.data[0].step_details.tool_calls[0].function.name;
                                    let data = JSON.parse(resp.data[0].step_details.tool_calls[0].function.arguments);
                                    let tool_call_id = resp.data[0].step_details.tool_calls[0].id;
                                    if(function_name == 'get_products'){
                                        getProducts(data.symbol).then((resp) => {
                                            let output = JSON.stringify(resp);
                                            submitToolOutputsToRun(thread_id, run_id, tool_call_id, output).then((resp) => {
                                                let run_id = resp.id;
                                                let interval = setInterval(() => {
                                                    getRunStatus(thread_id, run_id).then((resp) => {
                                                        status = resp.status;
                                                        if(status == 'completed'){
                                                            clearInterval(interval);
                                                            getMessages(thread_id).then((resp) => {
                                                                message = resp.data[0].content[0].text.value;
                                                                addMessageToContent ('chat', message);
                                                                loading.LoadingOverlay('hide');
                                                            });
                                                        }
                                                    });
                                                }, 2000);
                                            });
                                        });
                                    }
                                    if(function_name == 'send_email'){
                                        addMessageToContent ('chat', 'A enviar. Aguarde um momento.');
                                        sendEmail(data).then(() => {
                                            submitToolOutputsToRun(thread_id, run_id, tool_call_id, true).then((resp) => {
                                                let run_id = resp.id;
                                                let interval = setInterval(() => {
                                                    getRunStatus(thread_id, run_id).then((resp) => {
                                                        status = resp.status;
                                                        if(status == 'completed'){
                                                            clearInterval(interval);
                                                            getMessages(thread_id).then((resp) => {
                                                                message = resp.data[0].content[0].text.value;
                                                                addMessageToContent ('chat', message);
                                                                loading.LoadingOverlay('hide');
                                                            });
                                                        }
                                                    });
                                                }, 2000);
                                            });
                                        });
                                    }
                                }
                            });
                        }, 2000);
                    });
                });
            }
        }
    }

    submitToolOutputsToRun = (thread_id, run_id, tool_call_id, output) => {

        let data = {
            thread_id: thread_id,
            run_id: run_id,
            tool_call_id: tool_call_id,
            output: output
        }

        return new Promise((resolve, reject) => {
            $.post({
                url: '/admin/trainings/chat/submit-tool-outputs-to-run',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                success: (resp) => {
                    resolve(resp);
                },
                error: (err) => {
                    reject(err);
                }
            });
        });

    }

    runTheThread = async (thread_id) => {
        return $.get('/admin/trainings/chat/run-the-thread/' + assistant_id + '/' + thread_id);
    }

    addMessage = async (thread_id, message) => {
        let data = {
            thread_id: thread_id,
            message: message
        }
        return new Promise((resolve, reject) => {
            $.post({
                url: '/admin/trainings/chat/add-message',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                success: (resp) => {
                    resolve(resp);
                },
                error: (err) => {
                    reject(err);
                }
            });
        });
    }

    getMessages = async (thread_id) => {
        return $.get('/admin/trainings/chat/get-messages/' + thread_id);
    }

    createThreadAndRun = async () => {
        try {
            let data = {
                assistant_id: assistant_id
            };
            return new Promise((resolve, reject) => {
                $.post({
                    url: '/admin/trainings/chat/create-thread-and-run',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: (resp) => {
                        resolve(resp);
                    },
                    error: (jqXHR, textStatus, errorThrown) => {
                        reject(errorThrown);
                    }
                });
            });
        } catch (err) {
            console.error(err);
        }
    };

    listRunSteps = async (thread_id, run_id) => {
        return $.get('/admin/trainings/chat/list-run-steps/' + thread_id + '/' + run_id);
    }

    getRunStatus = async (thread_id, run_id) => {
        return $.get('/admin/trainings/chat/get-run-status/' + thread_id + '/' + run_id);
    }
    
    loadInstructions = () => {
        $.LoadingOverlay('show');
        $.get('/admin/trainings/instructions/load/' + assistant_id).then((resp) => {
            $('#instructions_list').html(resp);
            $.LoadingOverlay('hide');
        });
    }

    deleteInstruction = (instruction_id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                if (result.isConfirmed) {
                    $.LoadingOverlay('show');
                    $.get('/admin/trainings/instructions/delete/' + instruction_id).then(() => {
                        $.LoadingOverlay('hide');
                        loadInstructions();
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your file has been deleted.",
                            icon: "success"
                        });
                    });
            }
        });
    }

    modifyAssistant = () => {
        let button = $('#modify-assistant-button');
        button.LoadingOverlay('show');
        var instructions = '';
        $('.instruction-text').each(function() {
            instructions += $(this).text() + ' ';
        });
        let data = {
            instructions: instructions,
            assistant_id: assistant_id
        }
        $.post({
            url: '/admin/trainings/instructions/sync-assistant',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: () => {
                button.LoadingOverlay('hide');
                Swal.fire('Updated');
            },
            error: (error) => {
                button.LoadingOverlay('hide');
                Swal.fire('Sync error');
            }
        });
    }

    listMessages = async () => {
        try {
            const messages = await $.get('/admin/trainings/chat/list-messages/' + assistant_id + '/' + thread_id);
            return messages
        } catch (error) {
            return error;
        }
    }

    getProducts = async (search) => {
        try {
            return await $.get('/admin/trainings/api/search/' + assistant_id + '/' + search);
        } catch (error) {
            return error;
        }
    }

    sendEmail = async (data) => {
        try {
            return $.post({
                url: '/admin/trainings/chat/send-email',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data
            });
        } catch (error) {
            console.log(error);
        }
    }

    addMessageToContent = (role, message) => {
        let html = '';
        switch (role) {
            case 'user':
                html += '<div class="user-box">';
                html += '<div class="user">';
                html += '<div class="inner-text">' + message + '</div>';
                html += '</div>';
                html += '</div>';
                break;
            default:
                html += '<div class="chat-box">';
                html += '<div class="chat">';
                html += '<div class="inner-text">' + message + '</div>';
                html += '</div>';
                html += '</div>';
            break;
        }
        let chatContent = $('#chat-content');
        chatContent.append(html);
        chatContent.scrollTop(chatContent[0].scrollHeight);
    }

    addMessageToLog = async (role, message) => {
        let data = {
            project: project,
            log_id: log_id,
            role: role,
            message: message
        }
        try {
            return $.post({
                url: '/admin/trainings/chat/log',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data
            });          
        } catch (error) {
            console.log(error);
        }
    };

</script>
@endsection

@section('styles')
<style>
    .chat {
        border: solid 1px rgb(0 0 255 / 50%);
        display: inline-block;
        padding: 10px;
        background: rgb(0 0 255 / 50%);
        border-radius: 15px;
        color: #ffffff;
    }

    .user {
        border: solid 1px rgb(0 128 0 / 50%);
        display: inline-block;
        padding: 10px;
        background: rgb(0 128 0 / 50%);
        border-radius: 15px;
        color: #ffffff;
    }

    .chat-box {
        margin-bottom: 10px;
        text-align: left;
    }

    .user-box {
        margin-bottom: 10px;
        text-align: right;
    }

    #chat-content {
        overflow-y: scroll;
        height: 40vh;
    }
</style>
@endsection