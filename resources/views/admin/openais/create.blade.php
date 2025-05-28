@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.openai.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.openais.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.openai.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.openai.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="organization">{{ trans('cruds.openai.fields.organization') }}</label>
                <input class="form-control {{ $errors->has('organization') ? 'is-invalid' : '' }}" type="text" name="organization" id="organization" value="{{ old('organization', '') }}" required>
                @if($errors->has('organization'))
                    <div class="invalid-feedback">
                        {{ $errors->first('organization') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.openai.fields.organization_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="openai_api_key">{{ trans('cruds.openai.fields.openai_api_key') }}</label>
                <input class="form-control {{ $errors->has('openai_api_key') ? 'is-invalid' : '' }}" type="text" name="openai_api_key" id="openai_api_key" value="{{ old('openai_api_key', '') }}" required>
                @if($errors->has('openai_api_key'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai_api_key') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.openai.fields.openai_api_key_helper') }}</span>
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