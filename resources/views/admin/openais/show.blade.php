@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.openai.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.openais.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.openai.fields.id') }}
                        </th>
                        <td>
                            {{ $openai->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.openai.fields.name') }}
                        </th>
                        <td>
                            {{ $openai->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.openai.fields.organization') }}
                        </th>
                        <td>
                            {{ $openai->organization }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.openai.fields.openai_api_key') }}
                        </th>
                        <td>
                            {{ $openai->openai_api_key }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.openais.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection