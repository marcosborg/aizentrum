@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.assistant.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.assistants.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.assistant.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assistant.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="assist_code">{{ trans('cruds.assistant.fields.assist_code') }}</label>
                <input class="form-control {{ $errors->has('assist_code') ? 'is-invalid' : '' }}" type="text" name="assist_code" id="assist_code" value="{{ old('assist_code', '') }}" required>
                @if($errors->has('assist_code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('assist_code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assistant.fields.assist_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="project_id">{{ trans('cruds.assistant.fields.project') }}</label>
                <select class="form-control select2 {{ $errors->has('project') ? 'is-invalid' : '' }}" name="project_id" id="project_id" required>
                    @foreach($projects as $id => $entry)
                        <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('project'))
                    <div class="invalid-feedback">
                        {{ $errors->first('project') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assistant.fields.project_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection