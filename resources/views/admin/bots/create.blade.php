@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.bot.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.bots.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.bot.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.bot.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="instructions">{{ trans('cruds.bot.fields.instructions') }}</label>
                <textarea class="form-control {{ $errors->has('instructions') ? 'is-invalid' : '' }}" name="instructions" id="instructions" required>{{ old('instructions') }}</textarea>
                @if($errors->has('instructions'))
                    <div class="invalid-feedback">
                        {{ $errors->first('instructions') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.bot.fields.instructions_helper') }}</span>
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