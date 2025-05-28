@extends('layouts.admin')
@section('content')
@can('form_field_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.form-fields.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.formField.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.formField.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-FormField">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.label') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.position') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.form') }}
                        </th>
                        <th>
                            {{ trans('cruds.formField.fields.required') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formFields as $key => $formField)
                        <tr data-entry-id="{{ $formField->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $formField->id ?? '' }}
                            </td>
                            <td>
                                {{ $formField->name ?? '' }}
                            </td>
                            <td>
                                {{ $formField->label ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\FormField::TYPE_SELECT[$formField->type] ?? '' }}
                            </td>
                            <td>
                                {{ $formField->position ?? '' }}
                            </td>
                            <td>
                                {{ $formField->form->name ?? '' }}
                            </td>
                            <td>
                                <span style="display:none">{{ $formField->required ?? '' }}</span>
                                <input type="checkbox" disabled="disabled" {{ $formField->required ? 'checked' : '' }}>
                            </td>
                            <td>
                                @can('form_field_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.form-fields.show', $formField->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('form_field_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.form-fields.edit', $formField->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('form_field_delete')
                                    <form action="{{ route('admin.form-fields.destroy', $formField->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('form_field_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.form-fields.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
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

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-FormField:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection