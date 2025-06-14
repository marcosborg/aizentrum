@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.moloniItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.moloni-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.id') }}
                        </th>
                        <td>
                            {{ $moloniItem->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.moloni_invoice') }}
                        </th>
                        <td>
                            {{ $moloniItem->moloni_invoice->invoice ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.suplier') }}
                        </th>
                        <td>
                            {{ $moloniItem->suplier }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.reference') }}
                        </th>
                        <td>
                            {{ $moloniItem->reference }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.name') }}
                        </th>
                        <td>
                            {{ $moloniItem->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.qty') }}
                        </th>
                        <td>
                            {{ $moloniItem->qty }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.price') }}
                        </th>
                        <td>
                            {{ $moloniItem->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.moloniItem.fields.synced') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $moloniItem->synced ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.moloni-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection