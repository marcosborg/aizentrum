@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Nova fatura de fornecedor
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route("admin.moloni-invoices.store") }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="required" for="invoice">{{ trans('cruds.moloniInvoice.fields.invoice') }}</label>
                        <input class="form-control {{ $errors->has('invoice') ? 'is-invalid' : '' }}" type="text" name="invoice" id="invoice" value="{{ old('invoice', '') }}" required>
                        @if($errors->has('invoice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.moloniInvoice.fields.invoice_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="supplier">{{ trans('cruds.moloniInvoice.fields.supplier') }}</label>
                        <input class="form-control {{ $errors->has('supplier') ? 'is-invalid' : '' }}" type="text" name="supplier" id="supplier" value="{{ old('supplier', '') }}" required>
                        @if($errors->has('supplier'))
                            <div class="invalid-feedback">
                                {{ $errors->first('supplier') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.moloniInvoice.fields.supplier_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="file">{{ trans('cruds.moloniInvoice.fields.file') }}</label>
                        <div class="needsclick dropzone {{ $errors->has('file') ? 'is-invalid' : '' }}" id="file-dropzone">
                        </div>
                        @if($errors->has('file'))
                            <div class="invalid-feedback">
                                {{ $errors->first('file') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.moloniInvoice.fields.file_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="ocr">{{ trans('cruds.moloniInvoice.fields.ocr') }}</label>
                        <textarea class="form-control {{ $errors->has('ocr') ? 'is-invalid' : '' }}" name="ocr" id="ocr">{{ old('ocr') }}</textarea>
                        @if($errors->has('ocr'))
                            <div class="invalid-feedback">
                                {{ $errors->first('ocr') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.moloniInvoice.fields.ocr_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <div class="form-check {{ $errors->has('handled') ? 'is-invalid' : '' }}">
                            <input type="hidden" name="handled" value="0">
                            <input class="form-check-input" type="checkbox" name="handled" id="handled" value="1" {{ old('handled', 0) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="handled">{{ trans('cruds.moloniInvoice.fields.handled') }}</label>
                        </div>
                        @if($errors->has('handled'))
                            <div class="invalid-feedback">
                                {{ $errors->first('handled') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.moloniInvoice.fields.handled_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    Dropzone.options.fileDropzone = {
    url: '{{ route('admin.moloni-invoices.storeMedia') }}',
    maxFilesize: 5, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').find('input[name="file"]').remove()
      $('form').append('<input type="hidden" name="file" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="file"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($moloniInvoice) && $moloniInvoice->file)
      var file = {!! json_encode($moloniInvoice->file) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="file" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection