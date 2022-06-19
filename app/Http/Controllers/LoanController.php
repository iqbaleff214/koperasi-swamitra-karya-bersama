<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Collateral;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->title = 'Transaksi - Pinjaman';
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
            $data = Loan::with(['customer', 'collateral']);

            if ($request->from) {
                $data = $data->whereDate('created_at', '>=', $request->from);
            }

            if ($request->to) {
                $data = $data->whereDate('created_at', '<=', $request->to);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('transaction.loan.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                            <a href="' . route('transaction.loan.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                            <form class="d-inline" method="POST" action="' . route('transaction.loan.destroy', $row) . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                            </form>';
                })
                ->editColumn('id', function($row) {
                    return $this->buildTransactionCode($row->id);
                })
                ->editColumn('created_at', function($row) {
                    return Carbon::parse($row->created_at)->isoFormat('DD-MM-Y');
                })
                ->editColumn('customer', function($row) {
                    return $row->customer->name . '<small class="small d-block">No. Rek: ' . $row->customer->number . '</small>';
                })
                ->editColumn('collateral', function($row) {
                    return $row->collateral->name . '<small class="small d-block">Rp' . number_format($row->collateral->value, 2, ',', '.') . '</small>';
                })
                ->editColumn('installment', function($row) {
                    return $row->period . ' x Rp' . number_format($row->installment, 2, ',', '.') . '<small class="small d-block">= Rp' . number_format($row->return_amount, 2, ',', '.') . '</small>';
                })
                ->editColumn('amount', function($row) {
                    return 'Rp' . number_format($row->amount, 2, ',', '.');
                })
                ->rawColumns(['action', 'customer', 'collateral', 'installment'])
                ->make(true);
        }
        return view('pages.transaction.loan.index', [
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
        return view('pages.transaction.loan.create', [
            'title' => $this->buildTitle('baru'),
            'customers' => Customer::where('status', 'active')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLoanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLoanRequest $request)
    {
        try {
            DB::beginTransaction();

            $collateral = Collateral::create($request->only(['customer_id', 'name', 'value', 'description']));

            $data = $request->except(['name', 'value', 'description']);
            $data['collateral_id'] = $collateral->id;

            Loan::create($data);

            DB::commit();
            return redirect()->route('transaction.loan.index')->with('success', 'Berhasil meminjamkan pinjaman kepada nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loan  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $pinjaman)
    {
        return view('pages.transaction.loan.show', [
            'title' => $this->buildTitle('detail'),
            'code' => $this->buildTransactionCode($pinjaman->id),
            'loan' => $pinjaman,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loan  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $pinjaman)
    {
        return view('pages.transaction.loan.edit', [
            'title' => $this->buildTitle('edit'),
            'code' => $this->buildTransactionCode($pinjaman->id),
            'loan' => $pinjaman,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLoanRequest  $request
     * @param  \App\Models\Loan  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLoanRequest $request, Loan $pinjaman)
    {
        try {
            $pinjaman->update($request->all());
            return back()->with('success', 'Berhasil mengedit pinjaman nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loan  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $pinjaman)
    {
        try {
            $pinjaman->delete();
            return back()->with('success', 'Berhasil menghapus pinjaman nasabah!');
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

        $data = Loan::with(['customer', 'collateral'])->whereBetween('created_at', $filter)->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_pinjaman_nasabah_periode_' . $time_from . '_-_' . $time_to  . '_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.transaction.loan.print', [
            'title' => 'Laporan Pinjaman Nasabah',
            'user' => auth()->user(),
            'filter' => "$time_from sampai $time_to",
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
