@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.formData.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.form-datas.update", [$formData->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="form_id">{{ trans('cruds.formData.fields.form') }}</label>
                <select class="form-control select2 {{ $errors->has('form') ? 'is-invalid' : '' }}" name="form_id" id="form_id" required>
                    @foreach($forms as $id => $entry)
                        <option value="{{ $id }}" {{ (old('form_id') ? old('form_id') : $formData->form->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('form'))
                    <div class="invalid-feedback">
                        {{ $errors->first('form') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formData.fields.form_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="data">{{ trans('cruds.formData.fields.data') }}</label>
                <textarea class="form-control {{ $errors->has('data') ? 'is-invalid' : '' }}" name="data" id="data">{{ old('data', $formData->data) }}</textarea>
                @if($errors->has('data'))
                    <div class="invalid-feedback">
                        {{ $errors->first('data') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formData.fields.data_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('done') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="done" value="0">
                    <input class="form-check-input" type="checkbox" name="done" id="done" value="1" {{ $formData->done || old('done', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="done">{{ trans('cruds.formData.fields.done') }}</label>
                </div>
                @if($errors->has('done'))
                    <div class="invalid-feedback">
                        {{ $errors->first('done') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formData.fields.done_helper') }}</span>
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