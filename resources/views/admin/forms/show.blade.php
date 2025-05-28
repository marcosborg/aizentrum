@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.form.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.forms.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.form.fields.id') }}
                        </th>
                        <td>
                            {{ $form->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.form.fields.name') }}
                        </th>
                        <td>
                            {{ $form->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.form.fields.project') }}
                        </th>
                        <td>
                            {{ $form->project->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.form.fields.logo') }}
                        </th>
                        <td>
                            @if($form->logo)
                                <a href="{{ $form->logo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $form->logo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.forms.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection