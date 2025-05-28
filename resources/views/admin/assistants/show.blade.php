@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.assistant.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.assistants.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.assistant.fields.id') }}
                        </th>
                        <td>
                            {{ $assistant->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assistant.fields.name') }}
                        </th>
                        <td>
                            {{ $assistant->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assistant.fields.assist_code') }}
                        </th>
                        <td>
                            {{ $assistant->assist_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assistant.fields.project') }}
                        </th>
                        <td>
                            {{ $assistant->project->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.assistants.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection