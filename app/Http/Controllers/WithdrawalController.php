<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;

class WithdrawalController extends Controller
{

    public function __construct()
    {
        $this->title = 'Transaksi - Penarikan';
        $this->code = 'PE';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Deposit::with(['customer'])->where('type', 'penarikan')->orderBy('created_at');

            if ($request->customer) {
                $data = $data->where('customer_id', $request->customer);
            }

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
                        return '<a href="' . route('transaction.withdrawal.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                                <a href="' . route('transaction.withdrawal.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                                <form class="d-inline" method="POST" action="' . route('transaction.withdrawal.destroy', $row) . '">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                                </form>';
                    }

                    return '<form class="d-inline" method="POST" action="' . route('transaction.withdrawal.destroy', $row) . '">
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
                    if ($row->customer) {
                        return $row->customer->name . '<small class="small d-block">No. Rek: ' . $row->customer->number . '</small>';
                    }

                    return 'Nasabah Tidak Ditemukan';
                })
                ->editColumn('amount', function($row) {
                    return 'Rp' . number_format($row->amount, 2, ',', '.');
                })
                ->editColumn('current_balance', function($row) {
                    return 'Rp' . number_format($row->current_balance, 2, ',', '.');
                })
                ->rawColumns(['action', 'customer'])
                ->make(true);
        }
        return view('pages.transaction.withdrawal.index', [
            'title' => $this->title,
            'customers' => Customer::where('status', 'active')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.transaction.withdrawal.create', [
            'title' => $this->buildTitle('baru'),
            'customers' => Customer::where('status', 'active')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDepositRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepositRequest $request)
    {
        try {
            DB::beginTransaction();
            $simpanan = Deposit::where('customer_id', $request->customer_id)->latest()->first();
            $data = $request->all();
            $data['previous_balance'] = $simpanan->current_balance ?? 0;
            $data['current_balance'] = $data['previous_balance'] - $request->amount;
            Deposit::create($data);
            DB::commit();
            return redirect()->route('transaction.withdrawal.index')->with('success', 'Berhasil menarik simpanan nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposit  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $penarikan)
    {
        return view('pages.transaction.withdrawal.show', [
            'title' => $this->buildTitle('detail'),
            'deposit' => $penarikan,
            'code' => $this->buildTransactionCode($penarikan->id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deposit  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function edit(Deposit $penarikan)
    {
        return view('pages.transaction.withdrawal.edit', [
            'title' => $this->buildTitle('edit'),
            'code' => $this->buildTransactionCode($penarikan->id),
            'deposit' => $penarikan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDepositRequest  $request
     * @param  \App\Models\Deposit  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepositRequest $request, Deposit $penarikan)
    {
        try {
            $penarikan->update($request->all());
            return back()->with('success', 'Berhasil mengedit penarikan simpanan nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $penarikan)
    {
        try {
            $penarikan->delete();
            return back()->with('success', 'Berhasil menghapus penarikan simpanan nasabah!');
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

        $data = Deposit::with('customer')->where('type', 'penarikan')->whereBetween('created_at', $filter)->get();
        // dd($data);
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_penarikan_nasabah_periode_' . $time_from . '_-_' . $time_to  . '_' . time() . '.pdf';
        $pdf = PDF::loadView('pages.transaction.withdrawal.print', [
            'title' => 'Laporan Penarikan Nasabah',
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
