@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.logMessage.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.log-messages.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="log_id">{{ trans('cruds.logMessage.fields.log') }}</label>
                <select class="form-control select2 {{ $errors->has('log') ? 'is-invalid' : '' }}" name="log_id" id="log_id" required>
                    @foreach($logs as $id => $entry)
                        <option value="{{ $id }}" {{ old('log_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('log'))
                    <div class="invalid-feedback">
                        {{ $errors->first('log') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.logMessage.fields.log_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.logMessage.fields.role') }}</label>
                @foreach(App\Models\LogMessage::ROLE_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('role') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="role_{{ $key }}" name="role" value="{{ $key }}" {{ old('role', '') === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="role_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('role'))
                    <div class="invalid-feedback">
                        {{ $errors->first('role') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.logMessage.fields.role_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="message">{{ trans('cruds.logMessage.fields.message') }}</label>
                <textarea class="form-control {{ $errors->has('message') ? 'is-invalid' : '' }}" name="message" id="message" required>{{ old('message') }}</textarea>
                @if($errors->has('message'))
                    <div class="invalid-feedback">
                        {{ $errors->first('message') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.logMessage.fields.message_helper') }}</span>
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