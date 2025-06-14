@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.moloniItem.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.moloni-items.update", [$moloniItem->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="moloni_invoice_id">{{ trans('cruds.moloniItem.fields.moloni_invoice') }}</label>
                <select class="form-control select2 {{ $errors->has('moloni_invoice') ? 'is-invalid' : '' }}" name="moloni_invoice_id" id="moloni_invoice_id" required>
                    @foreach($moloni_invoices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('moloni_invoice_id') ? old('moloni_invoice_id') : $moloniItem->moloni_invoice->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('moloni_invoice'))
                    <div class="invalid-feedback">
                        {{ $errors->first('moloni_invoice') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.moloni_invoice_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="suplier">{{ trans('cruds.moloniItem.fields.suplier') }}</label>
                <input class="form-control {{ $errors->has('suplier') ? 'is-invalid' : '' }}" type="text" name="suplier" id="suplier" value="{{ old('suplier', $moloniItem->suplier) }}" required>
                @if($errors->has('suplier'))
                    <div class="invalid-feedback">
                        {{ $errors->first('suplier') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.suplier_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="reference">{{ trans('cruds.moloniItem.fields.reference') }}</label>
                <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text" name="reference" id="reference" value="{{ old('reference', $moloniItem->reference) }}" required>
                @if($errors->has('reference'))
                    <div class="invalid-feedback">
                        {{ $errors->first('reference') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.reference_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.moloniItem.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $moloniItem->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="qty">{{ trans('cruds.moloniItem.fields.qty') }}</label>
                <input class="form-control {{ $errors->has('qty') ? 'is-invalid' : '' }}" type="number" name="qty" id="qty" value="{{ old('qty', $moloniItem->qty) }}" step="1" required>
                @if($errors->has('qty'))
                    <div class="invalid-feedback">
                        {{ $errors->first('qty') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.qty_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price">{{ trans('cruds.moloniItem.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', $moloniItem->price) }}" step="0.01" required>
                @if($errors->has('price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('synced') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="synced" value="0">
                    <input class="form-check-input" type="checkbox" name="synced" id="synced" value="1" {{ $moloniItem->synced || old('synced', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="synced">{{ trans('cruds.moloniItem.fields.synced') }}</label>
                </div>
                @if($errors->has('synced'))
                    <div class="invalid-feedback">
                        {{ $errors->first('synced') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniItem.fields.synced_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection