@extends('layouts.admin')
@section('content')

@section('styles')
<style>
    .delete {
        float: right;
        font-size: 12px;
        color: red;
    }
</style>
@endsection

<div class="card">
    <div class="card-header">
        {{ trans('cruds.formsAssembly.title') }}
        <span class="pull-right"><strong>All forms of this project: </strong><a target="_new"
                href="{{ url('/form/all/' . $project_id) }}">{{ url('/form/all/' . $project_id) }}</a></span>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs">
            @foreach ($projects as $project)
            <li class="nav-item">
                <a class="nav-link {{ $project->id == $project_id ? 'active' : '' }}"
                    href="/admin/forms-assemblies/change-project-id/{{ $project->id }}">{{ $project->name }}</a>
            </li>
            @endforeach
        </ul>
        <div class="row">
            <div class="col-md-4">
                <div class="card mt-4">
                    <div class="card-header">
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#new_form">Novo
                            formulário</button>
                    </div>
                    <div class="card-body">
                        @if ($forms->count() == 0)
                        <div class="alert alert-primary" role="alert">
                            Ainda não existem formulários.
                        </div>
                        @else
                        <div class="list-group">
                            @foreach ($forms as $f)
                            <a href="/admin/forms-assemblies/{{ $f->id }}"
                                class="list-group-item list-group-item-action {{ $form_id == $f->id ? 'active' : '' }}"
                                aria-current="true">{{
                                $f->name }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8" id="form_ajax"></div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add_field" tabindex="-1" aria-labelledby="add_field_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_field_label">Add field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/forms-assemblies/create-form-field" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" name="label" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="textarea">Textarea</option>
                            <option value="radio">Rádio</option>
                            <option value="file">File</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                    <input type="hidden" name="form_id" value="{{ $form_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="new_form" tabindex="-1" aria-labelledby="new_form_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="new_form_label">New form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/forms-assemblies/new-form" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Project</label>
                        <select name="project_id" required class="form-control">
                            <option selected disabled>Please select</option>
                            @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="logo">{{ trans('cruds.form.fields.logo') }}</label>
                        <div class="needsclick dropzone {{ $errors->has('logo') ? 'is-invalid' : '' }}"
                            id="logo-dropzone">
                        </div>
                        @if($errors->has('logo'))
                        <div class="invalid-feedback">
                            {{ $errors->first('logo') }}
                        </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.form.fields.logo_helper') }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://malsup.github.io/jquery.form.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
</script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    $(() => {
        var form_id = {{ $form_id }};
        getFormAjax(form_id).then(() => {
            $('#sortable').sortable({
                update: (event, ui) => {
                    var cardData = [];
                    $("#sortable .card").each(function(index) {
                        var position = index + 1;
                        var id = $(this).data("id");
                        cardData.push({ id: id, position: position });
                    });
                    $.post({
                        url: '/admin/forms-assemblies/update-positions',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: JSON.stringify(cardData)
                        },
                        success: () => {
                            getFormAjax(form_id);
                        },
                        error: (error) => {
                            console.log(error);
                        }
                    });
                }
            });
        });
        $('#add_field form').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                location.reload();
            },
            error: () => {
                console.log(error);
            }
        });
        $('#new_form form').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: (resp) => {
                console.log(resp);
                $.LoadingOverlay('hide');
                Swal.fire('saved').then(()=> {
                    window.location.href="/admin/forms-assemblies/" + resp.id;
                });
            },
            error: (error) => {
                $.LoadingOverlay('hide');
                console.log(error);
            }
        });
    });
    
    getFormAjax = async (form_id) => {
        try {
            return $.get('/admin/forms-assemblies/form-ajax/' + form_id).then((resp) => {
                $('#form_ajax').html(resp);
            });   
        } catch (error) {
            console.log(error);
        }
    }

    deleteField = (form_field_id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                $.get('/admin/forms-assemblies/delete-field/' + form_field_id).then(() => {
                    getFormAjax({{ $form_id }});
                });     
            }
        });
    }

    deleteForm = (form_id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                $.get('/admin/forms-assemblies/delete-form/' + form_id).then(() => {
                    window.location.href='/admin/forms-assemblies';
                });     
            }
        });
    }

    submitForm = (form_id) => {
        var fields = $('.form-field');
        let data = [];
        let validation = '';
        fields.each((i, v) => {
            let label = $(v).data('label');
            let value = $(v).val();
            let name = $(v).attr('name');
            let type = $(v).data('type');
            let required = $(v).data('required');
            let data_field = {
                label: label,
                value: value,
                name: name,
                type: type,
                required: required
            };
            if(required == true && value == ''){
                validation += '<p>The field "' + label + '" is required.</p>';
            }
            if(type == 'radio'){
                if($(v).is(':checked')) {
                    data.push(data_field);
                }
            } else if(type == 'checkbox') {
                if($(v).is(':checked')) {
                    data_field.value = true
                    data.push(data_field);
                } else {
                    data_field.value = false
                    data.push(data_field);
                    if(data_field.required == true){
                        validation += '<p>The field "' + label + '" is required.</p>';
                    }
                }
            } else {
                data.push(data_field);
            }
        });
        if(validation !== ''){
            Swal.fire({
                title: "Validation!",
                html: validation,
                icon: "error"
            });
        } else {
            $.post({
                url: '/admin/forms-assemblies/form-send',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    form_id: form_id,
                    data: data
                },
                success: () => {
                    Swal.fire({
                        title: "Success!",
                        text: "The form was sended!",
                        icon: "success"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: (error) => {
                    console.log(error);
                }
            })
        }
    }

</script>
<script>
    Dropzone.options.logoDropzone = {
    url: '/admin/forms-assemblies/new-form/media',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="logo"]').remove()
      $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="logo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($form) && $form->logo)
      var file = {!! json_encode($form->logo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection