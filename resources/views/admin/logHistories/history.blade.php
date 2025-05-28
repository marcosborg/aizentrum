@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.logHistory.title') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.logMessage.fields.log') }}
                        </th>
                        <th>
                            {{ trans('cruds.logMessage.fields.role') }}
                        </th>
                        <th>
                            {{ trans('cruds.logMessage.fields.message') }}
                        </th>
                        <th>
                            {{ trans('cruds.logMessage.fields.created_at') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logMessages as $key => $logMessage)
                    <tr>
                        <td>
                            {{ $logMessage->log->project ?? '' }}
                        </td>
                        <td>
                            {{ App\Models\LogMessage::ROLE_RADIO[$logMessage->role] ?? '' }}
                        </td>
                        <td>
                            {{ $logMessage->message ?? '' }}
                        </td>
                        <td>
                            {{ $logMessage->created_at ?? '' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection