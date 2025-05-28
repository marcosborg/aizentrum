@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.formData.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.form-datas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.formData.fields.id') }}
                        </th>
                        <td>
                            {{ $formData->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formData.fields.form') }}
                        </th>
                        <td>
                            {{ $formData->form->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formData.fields.data') }}
                        </th>
                        <td>
                            {{ $formData->data }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.formData.fields.done') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $formData->done ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.form-datas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection