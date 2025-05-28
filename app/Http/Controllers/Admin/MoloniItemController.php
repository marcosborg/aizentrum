<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMoloniItemRequest;
use App\Http\Requests\StoreMoloniItemRequest;
use App\Http\Requests\UpdateMoloniItemRequest;
use App\Models\MoloniInvoice;
use App\Models\MoloniItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MoloniItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('moloni_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MoloniItem::with(['moloni_invoice'])->select(sprintf('%s.*', (new MoloniItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'moloni_item_show';
                $editGate      = 'moloni_item_edit';
                $deleteGate    = 'moloni_item_delete';
                $crudRoutePart = 'moloni-items';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('moloni_invoice_invoice', function ($row) {
                return $row->moloni_invoice ? $row->moloni_invoice->invoice : '';
            });

            $table->editColumn('moloni_invoice.invoice', function ($row) {
                return $row->moloni_invoice ? (is_string($row->moloni_invoice) ? $row->moloni_invoice : $row->moloni_invoice->invoice) : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('qty', function ($row) {
                return $row->qty ? $row->qty : '';
            });
            $table->editColumn('handled', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->handled ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'moloni_invoice', 'handled']);

            return $table->make(true);
        }

        $moloni_invoices = MoloniInvoice::get();

        return view('admin.moloniItems.index', compact('moloni_invoices'));
    }

    public function create()
    {
        abort_if(Gate::denies('moloni_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moloni_invoices = MoloniInvoice::pluck('invoice', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.moloniItems.create', compact('moloni_invoices'));
    }

    public function store(StoreMoloniItemRequest $request)
    {
        $moloniItem = MoloniItem::create($request->all());

        return redirect()->route('admin.moloni-items.index');
    }

    public function edit(MoloniItem $moloniItem)
    {
        abort_if(Gate::denies('moloni_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moloni_invoices = MoloniInvoice::pluck('invoice', 'id')->prepend(trans('global.pleaseSelect'), '');

        $moloniItem->load('moloni_invoice');

        return view('admin.moloniItems.edit', compact('moloniItem', 'moloni_invoices'));
    }

    public function update(UpdateMoloniItemRequest $request, MoloniItem $moloniItem)
    {
        $moloniItem->update($request->all());

        return redirect()->route('admin.moloni-items.index');
    }

    public function show(MoloniItem $moloniItem)
    {
        abort_if(Gate::denies('moloni_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moloniItem->load('moloni_invoice');

        return view('admin.moloniItems.show', compact('moloniItem'));
    }

    public function destroy(MoloniItem $moloniItem)
    {
        abort_if(Gate::denies('moloni_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moloniItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyMoloniItemRequest $request)
    {
        $moloniItems = MoloniItem::find(request('ids'));

        foreach ($moloniItems as $moloniItem) {
            $moloniItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
