@extends('layouts.admin')
@section('content')
@can('moloni_item_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.moloni-items.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.moloniItem.title_singular') }}
            </a>

        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.moloniItem.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-MoloniItem">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.moloni_invoice') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniInvoice.fields.invoice') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.suplier') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.reference') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.qty') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.moloniItem.fields.synced') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($moloni_invoices as $key => $item)
                                <option value="{{ $item->invoice }}">{{ $item->invoice }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('moloni_item_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.moloni-items.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)

@endcan

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.moloni-items.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'moloni_invoice_invoice', name: 'moloni_invoice.invoice' },
{ data: 'moloni_invoice.invoice', name: 'moloni_invoice.invoice' },
{ data: 'suplier', name: 'suplier' },
{ data: 'reference', name: 'reference' },
{ data: 'name', name: 'name' },
{ data: 'qty', name: 'qty' },
{ data: 'price', name: 'price' },
{ data: 'synced', name: 'synced' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-MoloniItem').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
let visibleColumnsIndexes = null;
$('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
      if (visibleColumnsIndexes !== null) {
        index = visibleColumnsIndexes[index]
      }

      table
        .column(index)
        .search(value, strict)
        .draw()
  });
table.on('column-visibility.dt', function(e, settings, column, state) {
      visibleColumnsIndexes = []
      table.columns(":visible").every(function(colIdx) {
          visibleColumnsIndexes.push(colIdx);
      });
  })
});

</script>
@endsection