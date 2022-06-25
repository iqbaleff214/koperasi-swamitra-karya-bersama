<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->title = 'Kolektor - Nasabah Bermasalah';
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
            $data = Visit::with(['customer', 'loan', 'user']);

            if ($request->from) {
                $data = $data->whereDate('created_at', '>=', $request->from);
            }

            if ($request->to) {
                $data = $data->whereDate('created_at', '<=', $request->to);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->customer) {
                        return '<a href="' . route('collection.visit.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                                <a href="' . route('collection.visit.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                                <form class="d-inline" method="POST" action="' . route('collection.visit.destroy', $row) . '">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                                </form>';
                    }

                    return '<form class="d-inline" method="POST" action="' . route('collection.visit.destroy', $row) . '">
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
                ->editColumn('id', function($row) {
                    return $this->buildTransactionCode($row->id);
                })
                ->editColumn('created_at', function($row) {
                    return Carbon::parse($row->created_at)->isoFormat('DD-MM-Y');
                })
                ->addColumn('customer', function($row) {
                    if ($row->customer) {
                        return $row->customer->name . '<small class="small d-block">No. Rek: ' . $row->customer->number . '</small>';
                    }

                    return 'Nasabah Tidak Ditemukan';
                })
                ->addColumn('user', function($row) {
                    if ($row->user) {
                        return $row->user->name;
                    }

                    return 'Kolektor Tidak Ditemukan';
                })
                ->editColumn('remaining_amount', function($row) {
                    return 'Rp' . number_format($row->remaining_amount, 2, ',', '.');
                })
                ->rawColumns(['action', 'customer', 'loan'])
                ->make(true);
        }
        return view('pages.collection.visit.index', [
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
        return view('pages.collection.visit.create', [
            'title' => $this->buildTitle('baru'),
            'customers' => Customer::where('status', 'active')->get(),
            'users' => User::where('role', 'collector')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVisitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVisitRequest $request)
    {
        try {
            Visit::create($request->all());
            return redirect()->route('collection.visit.index')->with('success', 'Berhasil menambahkan nasabah bermasalah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visit  $nasabah_bermasalah
     * @return \Illuminate\Http\Response
     */
    public function show(Visit $nasabah_bermasalah)
    {
        return view('pages.collection.visit.show', [
            'title' => $this->buildTitle('detail'),
            'visit' => $nasabah_bermasalah,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Visit  $nasabah_bermasalah
     * @return \Illuminate\Http\Response
     */
    public function edit(Visit $nasabah_bermasalah)
    {
        return view('pages.collection.visit.edit', [
            'title' => $this->buildTitle('edit'),
            'users' => User::where('role', 'collector')->get(),
            'visit' => $nasabah_bermasalah,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVisitRequest  $request
     * @param  \App\Models\Visit  $nasabah_bermasalah
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVisitRequest $request, Visit $nasabah_bermasalah)
    {
        try {
            $nasabah_bermasalah->update($request->all());
            return back()->with('success', 'Berhasil mengedit nasabah bermasalah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visit  $nasabah_bermasalah
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visit $nasabah_bermasalah)
    {
        try {
            $nasabah_bermasalah->delete();
            return back()->with('success', 'Berhasil menghapus nasabah bermasalah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $data = Visit::with(['customer', 'user', 'loan'])->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_kolektor_-_nasabah_bermasalah_' . time() . '.pdf';
        $pdf = PDF::loadView('pages.collection.visit.print', [
            'title' => 'Laporan Kolektor (Nasabah Bermasalah)',
            'user' => auth()->user(),
            'filter' => "-",
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
