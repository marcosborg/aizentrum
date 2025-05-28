@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.moloniInvoice.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.moloni-invoices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.id') }}
                        </th>
                        <td>
                            {{ $moloniInvoice->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.invoice') }}
                        </th>
                        <td>
                            {{ $moloniInvoice->invoice }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.supplier') }}
                        </th>
                        <td>
                            {{ $moloniInvoice->supplier }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.file') }}
                        </th>
                        <td>
                            @if($moloniInvoice->file)
                                <a href="{{ $moloniInvoice->file->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.ocr') }}
                        </th>
                        <td>
                            {{ $moloniInvoice->ocr }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniInvoice.fields.handled') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $moloniInvoice->handled ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.moloni-invoices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection