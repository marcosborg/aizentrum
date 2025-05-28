@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.log.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.logs.update", [$log->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="project">{{ trans('cruds.log.fields.project') }}</label>
                <input class="form-control {{ $errors->has('project') ? 'is-invalid' : '' }}" type="text" name="project" id="project" value="{{ old('project', $log->project) }}" required>
                @if($errors->has('project'))
                    <div class="invalid-feedback">
                        {{ $errors->first('project') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.log.fields.project_helper') }}</span>
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