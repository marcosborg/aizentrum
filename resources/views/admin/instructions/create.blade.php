@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.instruction.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.instructions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="text">{{ trans('cruds.instruction.fields.text') }}</label>
                <textarea class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" name="text" id="text" required>{{ old('text') }}</textarea>
                @if($errors->has('text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.instruction.fields.text_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="position">{{ trans('cruds.instruction.fields.position') }}</label>
                <input class="form-control {{ $errors->has('position') ? 'is-invalid' : '' }}" type="text" name="position" id="position" value="{{ old('position', '') }}" required>
                @if($errors->has('position'))
                    <div class="invalid-feedback">
                        {{ $errors->first('position') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.instruction.fields.position_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('active') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="active" value="0">
                    <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', 0) == 1 || old('active') === null ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">{{ trans('cruds.instruction.fields.active') }}</label>
                </div>
                @if($errors->has('active'))
                    <div class="invalid-feedback">
                        {{ $errors->first('active') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.instruction.fields.active_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="assistant_id">{{ trans('cruds.instruction.fields.assistant') }}</label>
                <select class="form-control select2 {{ $errors->has('assistant') ? 'is-invalid' : '' }}" name="assistant_id" id="assistant_id">
                    @foreach($assistants as $id => $entry)
                        <option value="{{ $id }}" {{ old('assistant_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('assistant'))
                    <div class="invalid-feedback">
                        {{ $errors->first('assistant') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.instruction.fields.assistant_helper') }}</span>
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