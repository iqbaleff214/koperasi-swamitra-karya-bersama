<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForeclosureRequest;
use App\Http\Requests\UpdateForeclosureRequest;
use App\Models\Customer;
use App\Models\Foreclosure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ForeclosureController extends Controller
{
    public function __construct()
    {
        $this->title = 'Kolektor - Penarikan Jaminan';
        $this->code = 'PI';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Foreclosure::with(['customer', 'loan', 'collateral']);

            if ($request->from) {
                $data = $data->whereDate('date', '>=', $request->from);
            }

            if ($request->to) {
                $data = $data->whereDate('date', '<=', $request->to);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->customer) {
                        return '<a href="' . route('collection.foreclosure.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                                <a href="' . route('collection.foreclosure.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                                <form class="d-inline" method="POST" action="' . route('collection.foreclosure.destroy', $row) . '">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                                </form>';
                    }

                    return '<form class="d-inline" method="POST" action="' . route('collection.foreclosure.destroy', $row) . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                    </form>';
                })
                ->addColumn('loan', function($row) {
                    if ($row->loan) {
                        return 'Rp' . number_format($row->loan->amount, 2, ',', '.') . '<small class="small d-block">Kode Transaksi: PI-' . sprintf('%05d', $row->loan_id) . '</small>';
                    }

                    return 'Rp0';
                })
                ->editColumn('created_at', function($row) {
                    return Carbon::parse($row->created_at)->isoFormat('DD-MM-Y');
                })
                ->editColumn('date', function($row) {
                    return Carbon::parse($row->date)->isoFormat('DD-MM-Y');
                })
                ->addColumn('customer', function($row) {
                    if ($row->customer) {
                        return $row->customer->name . '<small class="small d-block">No. Rek: ' . $row->customer->number . '</small>';
                    }

                    return 'Nasabah Tidak Ditemukan';
                })
                ->addColumn('collateral', function($row) {
                    if ($row->collateral) {
                        return ($row->collateral->name ?? $row->collateral->description) . '<small class="small d-block">Rp' . number_format($row->collateral->value, 2, ',', '.') . '</small>';
                    }

                    return 'Nasabah Tidak Ditemukan';
                })
                ->addColumn('total_amount', function($row) {
                    if ($row->collateral) {
                        return 'Rp' . number_format($row->collateral->value - $row->remaining_amount, 2, ',', '.');
                    }

                    return 'Rp' . number_format(0 - $row->remaining_amount, 2, ',', '.');
                })
                ->editColumn('return_amount', function($row) {
                    return 'Rp' . number_format($row->return_amount, 2, ',', '.');
                })
                ->editColumn('remaining_amount', function($row) {
                    return 'Rp' . number_format($row->remaining_amount, 2, ',', '.');
                })
                ->editColumn('collateral_amount', function($row) {
                    return 'Rp' . number_format($row->collateral_amount, 2, ',', '.');
                })
                ->rawColumns(['action', 'customer', 'loan', 'collateral'])
                ->make(true);
        }
        return view('pages.collection.foreclosure.index', [
            'title' => $this->title
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.collection.foreclosure.create', [
            'title' => $this->buildTitle('baru'),
            'customers' => Customer::where('status', 'active')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreForeclosureRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreForeclosureRequest $request)
    {
        try {
            DB::beginTransaction();
            Customer::find($request->customer_id)->update(['status' => 'blacklist']);
            Foreclosure::create($request->only(['date', 'collateral_amount', 'remaining_amount', 'return_amount', 'customer_id', 'loan_id', 'collateral_id']));
            DB::commit();
            return redirect()->route('collection.foreclosure.index')->with('success', 'Berhasil menarik jaminan pinjaman nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Foreclosure  $penarikan_jaminan
     * @return \Illuminate\Http\Response
     */
    public function show(Foreclosure $penarikan_jaminan)
    {
        return view('pages.collection.foreclosure.show', [
            'title' => $this->buildTitle('detail'),
            'foreclosure' => $penarikan_jaminan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Foreclosure  $penarikan_jaminan
     * @return \Illuminate\Http\Response
     */
    public function edit(Foreclosure $penarikan_jaminan)
    {
        return view('pages.collection.foreclosure.edit', [
            'title' => $this->buildTitle('edit'),
            'foreclosure' => $penarikan_jaminan
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateForeclosureRequest  $request
     * @param  \App\Models\Foreclosure  $penarikan_jaminan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateForeclosureRequest $request, Foreclosure $penarikan_jaminan)
    {
        try {
            $penarikan_jaminan->update($request->all());
            return back()->with('success', 'Berhasil mengedit penarikan jaminan pinjaman nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Foreclosure  $penarikan_jaminan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Foreclosure $penarikan_jaminan)
    {
        try {
            $penarikan_jaminan->delete();
            return back()->with('success', 'Berhasil menghapus penarikan jaminan pinjaman nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $filter = $request->validate([
            'time_from' => 'required',
            'time_to' => 'required',
        ]);

        $time_from = date('d-m-Y', strtotime($filter['time_from']));
        $time_to = date('d-m-Y', strtotime($filter['time_to']));

        $data = Foreclosure::with(['customer', 'collateral'])->whereBetween('created_at', $filter)->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_penarikan_jaminan_nasabah_bermasalah_periode_' . $time_from . '_-_' . $time_to  . '_' . time() . '.pdf';
        $pdf = PDF::loadView('pages.collection.foreclosure.print', [
            'title' => 'Laporan Penarikan Jaminan Nasabah Bermasalah',
            'user' => auth()->user(),
            'filter' => "$time_from - $time_to",
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
