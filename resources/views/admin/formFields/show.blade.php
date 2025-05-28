@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.formField.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.form-fields.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.id') }}
                        </th>
                        <td>
                            {{ $formField->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.name') }}
                        </th>
                        <td>
                            {{ $formField->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.label') }}
                        </th>
                        <td>
                            {{ $formField->label }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\FormField::TYPE_SELECT[$formField->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.position') }}
                        </th>
                        <td>
                            {{ $formField->position }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.form') }}
                        </th>
                        <td>
                            {{ $formField->form->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formField.fields.required') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $formField->required ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.form-fields.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection