@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bot.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bot.fields.id') }}
                        </th>
                        <td>
                            {{ $bot->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bot.fields.name') }}
                        </th>
                        <td>
                            {{ $bot->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bot.fields.instructions') }}
                        </th>
                        <td>
                            {{ $bot->instructions }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection