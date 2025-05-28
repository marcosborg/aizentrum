@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.formsInbox.title') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-FormData">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.formData.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.formData.fields.form') }}
                        </th>
                        <th>
                            Enviado em
                        </th>
                        <th>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($form_datas as $key => $formData)
                    <tr>
                        <td>
                            {{ $formData->id ?? '' }}
                        </td>
                        <td>
                            {{ $formData->form->name ?? '' }}
                        </td>
                        <td>
                            {{ $formData->created_at ?? '' }}
                        </td>
                        <td>
                            <a class="btn btn-xs btn-primary"
                                href="{{ route('admin.forms-inboxes.form', $formData->id) }}">
                                {{ trans('global.view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection