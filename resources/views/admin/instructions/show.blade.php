@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.instruction.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.instructions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.instruction.fields.id') }}
                        </th>
                        <td>
                            {{ $instruction->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.instruction.fields.text') }}
                        </th>
                        <td>
                            {{ $instruction->text }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.instruction.fields.position') }}
                        </th>
                        <td>
                            {{ $instruction->position }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.instruction.fields.active') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $instruction->active ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.instruction.fields.assistant') }}
                        </th>
                        <td>
                            {{ $instruction->assistant->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.instructions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection