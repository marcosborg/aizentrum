@extends('layouts.admin')
@section('content')
@can('log_message_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.log-messages.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.logMessage.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.logMessage.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-LogMessage">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.logMessage.fields.id') }}
                        </th>
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
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logMessages as $key => $logMessage)
                        <tr data-entry-id="{{ $logMessage->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $logMessage->id ?? '' }}
                            </td>
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
                                @can('log_message_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.log-messages.show', $logMessage->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('log_message_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.log-messages.edit', $logMessage->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('log_message_delete')
                                    <form action="{{ route('admin.log-messages.destroy', $logMessage->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('log_message_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.log-messages.massDestroy') }}",
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
  let table = $('.datatable-LogMessage:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection