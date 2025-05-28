@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.project.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.projects.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.project.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.project.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="openai_id">{{ trans('cruds.project.fields.openai') }}</label>
                <select class="form-control select2 {{ $errors->has('openai') ? 'is-invalid' : '' }}" name="openai_id" id="openai_id" required>
                    @foreach($openais as $id => $entry)
                        <option value="{{ $id }}" {{ old('openai_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('openai'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.project.fields.openai_helper') }}</span>
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