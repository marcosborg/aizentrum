@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.formField.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.form-fields.update", [$formField->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.formField.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $formField->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="label">{{ trans('cruds.formField.fields.label') }}</label>
                <input class="form-control {{ $errors->has('label') ? 'is-invalid' : '' }}" type="text" name="label" id="label" value="{{ old('label', $formField->label) }}" required>
                @if($errors->has('label'))
                    <div class="invalid-feedback">
                        {{ $errors->first('label') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.label_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.formField.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\FormField::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $formField->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="position">{{ trans('cruds.formField.fields.position') }}</label>
                <input class="form-control {{ $errors->has('position') ? 'is-invalid' : '' }}" type="number" name="position" id="position" value="{{ old('position', $formField->position) }}" step="1">
                @if($errors->has('position'))
                    <div class="invalid-feedback">
                        {{ $errors->first('position') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.position_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="form_id">{{ trans('cruds.formField.fields.form') }}</label>
                <select class="form-control select2 {{ $errors->has('form') ? 'is-invalid' : '' }}" name="form_id" id="form_id" required>
                    @foreach($forms as $id => $entry)
                        <option value="{{ $id }}" {{ (old('form_id') ? old('form_id') : $formField->form->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('form'))
                    <div class="invalid-feedback">
                        {{ $errors->first('form') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.form_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('required') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="required" value="0">
                    <input class="form-check-input" type="checkbox" name="required" id="required" value="1" {{ $formField->required || old('required', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="required">{{ trans('cruds.formField.fields.required') }}</label>
                </div>
                @if($errors->has('required'))
                    <div class="invalid-feedback">
                        {{ $errors->first('required') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.formField.fields.required_helper') }}</span>
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