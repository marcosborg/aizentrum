@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-4">
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.moloniInvoice.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.moloni-invoices.update", [$moloniInvoice->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="invoice">{{ trans('cruds.moloniInvoice.fields.invoice') }}</label>
                <input class="form-control {{ $errors->has('invoice') ? 'is-invalid' : '' }}" type="text" name="invoice" id="invoice" value="{{ old('invoice', $moloniInvoice->invoice) }}" required>
                @if($errors->has('invoice'))
                    <div class="invalid-feedback">
                        {{ $errors->first('invoice') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moloniInvoice.fields.invoice_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="supplier">{{ trans('cruds.moloniInvoice.fields.supplier') }}</label>
                <input class="form-control {{ $errors->has('supplier') ? 'is-invalid' : '' }}" type="text" name="supplier" id="supplier" value="{{ old('supplier', $moloniInvoice->supplier) }}" required>
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
                <textarea class="form-control {{ $errors->has('ocr') ? 'is-invalid' : '' }}" name="ocr" id="ocr">{{ old('ocr', $moloniInvoice->ocr) }}</textarea>
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
                    <input class="form-check-input" type="checkbox" name="handled" id="handled" value="1" {{ $moloniInvoice->handled || old('handled', 0) === 1 ? 'checked' : '' }}>
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
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                Fatura
            </div>
            <div class="card-body">
                <canvas id="pdf-canvas" style="border:1px solid #ccc; max-width: 100%;"></canvas>
                <button id="btn-capturar" class="btn btn-success btn-sm mt-2">Capturar códigos</button>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                Referencias
            </div>
            <div class="card-body">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    const url = "{{ $moloniInvoice->file->getUrl() }}"; // URL do teu PDF

    const canvas = document.getElementById('pdf-canvas');
    const context = canvas.getContext('2d');

    // URL do ficheiro worker (obrigatório para funcionar corretamente)
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    // Carrega o PDF e renderiza a primeira página
    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
            const scale = 0.5;
            const viewport = page.getViewport({ scale });

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext);
        });
    });

</script>
<script>
    document.getElementById('btn-capturar').addEventListener('click', function () {
        const button = this;
        button.disabled = true;
        button.textContent = 'A processar...';

        fetch("{{ route('admin.moloni-new-invoices.process-ocr', $moloniInvoice->id) }}", {

            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                document.getElementById('ocr').value = data.ocr;
                alert("OCR extraído com sucesso!");
            } else {
                alert("Erro ao processar OCR.");
            }
        })
        .catch(() => alert("Erro de ligação ao servidor."))
        .finally(() => {
            button.disabled = false;
            button.textContent = 'Capturar códigos';
        });
    });
</script>

@endsection