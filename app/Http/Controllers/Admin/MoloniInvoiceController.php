<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyMoloniInvoiceRequest;
use App\Http\Requests\StoreMoloniInvoiceRequest;
use App\Http\Requests\UpdateMoloniInvoiceRequest;
use App\Models\MoloniInvoice;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MoloniInvoiceController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('moloni_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MoloniInvoice::query()->select(sprintf('%s.*', (new MoloniInvoice)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'moloni_invoice_show';
                $editGate      = 'moloni_invoice_edit';
                $deleteGate    = 'moloni_invoice_delete';
                $crudRoutePart = 'moloni-invoices';

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
            $table->editColumn('invoice', function ($row) {
                return $row->invoice ? $row->invoice : '';
            });
            $table->editColumn('supplier', function ($row) {
                return $row->supplier ? $row->supplier : '';
            });
            $table->editColumn('file', function ($row) {
                return $row->file ? '<a href="' . $row->file->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '';
            });
            $table->editColumn('ocr', function ($row) {
                return $row->ocr ? $row->ocr : '';
            });
            $table->editColumn('handled', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->handled ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'file', 'handled']);

            return $table->make(true);
        }

        return view('admin.moloniInvoices.index');
    }

    public function create()
    {
        abort_if(Gate::denies('moloni_invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.moloniInvoices.create');
    }

    public function store(StoreMoloniInvoiceRequest $request)
    {
        $moloniInvoice = MoloniInvoice::create($request->all());

        if ($request->input('file', false)) {
            $moloniInvoice->addMedia(storage_path('tmp/uploads/' . basename($request->input('file'))))->toMediaCollection('file');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $moloniInvoice->id]);
        }

        return redirect()->route('admin.moloni-invoices.edit', $moloniInvoice->id);
    }

    public function edit(MoloniInvoice $moloniInvoice)
    {
        abort_if(Gate::denies('moloni_invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.moloniInvoices.edit', compact('moloniInvoice'));
    }

    public function update(UpdateMoloniInvoiceRequest $request, MoloniInvoice $moloniInvoice)
    {
        $moloniInvoice->update($request->all());

        if ($request->input('file', false)) {
            if (! $moloniInvoice->file || $request->input('file') !== $moloniInvoice->file->file_name) {
                if ($moloniInvoice->file) {
                    $moloniInvoice->file->delete();
                }
                $moloniInvoice->addMedia(storage_path('tmp/uploads/' . basename($request->input('file'))))->toMediaCollection('file');
            }
        } elseif ($moloniInvoice->file) {
            $moloniInvoice->file->delete();
        }

        return redirect()->route('admin.moloni-invoices.edit', $moloniInvoice->id);
    }

    public function show(MoloniInvoice $moloniInvoice)
    {
        abort_if(Gate::denies('moloni_invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.moloniInvoices.show', compact('moloniInvoice'));
    }

    public function destroy(MoloniInvoice $moloniInvoice)
    {
        abort_if(Gate::denies('moloni_invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moloniInvoice->delete();

        return back();
    }

    public function massDestroy(MassDestroyMoloniInvoiceRequest $request)
    {
        $moloniInvoices = MoloniInvoice::find(request('ids'));

        foreach ($moloniInvoices as $moloniInvoice) {
            $moloniInvoice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('moloni_invoice_create') && Gate::denies('moloni_invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new MoloniInvoice();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}