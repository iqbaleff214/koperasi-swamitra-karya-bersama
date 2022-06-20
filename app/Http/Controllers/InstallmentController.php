<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class InstallmentController extends Controller
{
    public function __construct()
    {
        $this->title = 'Transaksi - Pembayaran Pinjaman';
        $this->code = 'SI';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Deposit::with(['customer', 'loan'])->where('type', 'wajib')->orderBy('created_at');

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
                        return '<a href="' . route('transaction.installment.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                                <a href="' . route('transaction.installment.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                                <form class="d-inline" method="POST" action="' . route('transaction.installment.destroy', $row) . '">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                                </form>';
                    }

                    return '<form class="d-inline" method="POST" action="' . route('transaction.installment.destroy', $row) . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                    </form>';
                })
                ->addColumn('balance', function($row) {
                    if ($row->loan) {
                        return 'Rp' . number_format($row->loan->amount, 2, ',', '.');
                    }

                    return 'Rp0';
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
        return view('pages.transaction.installment.index', [
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
        return view('pages.transaction.installment.create', [
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
            $pembayaran = Deposit::where('customer_id', $request->customer_id)->latest()->first();
            $data = $request->all();
            $data['previous_balance'] = $pembayaran->current_balance ?? 0;
            $data['current_balance'] = $data['previous_balance'] + $request->amount;
            Deposit::create($data);
            return redirect()->route('transaction.installment.index')->with('success', 'Berhasil menambahkan pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            dd($th);
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposit  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $pembayaran)
    {
        return view('pages.transaction.installment.show', [
            'title' => $this->buildTitle('detail'),
            'deposit' => $pembayaran,
            'code' => $this->buildTransactionCode($pembayaran->id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deposit  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(Deposit $pembayaran)
    {
        return view('pages.transaction.installment.edit', [
            'title' => $this->buildTitle('edit'),
            'code' => $this->buildTransactionCode($pembayaran->id),
            'deposit' => $pembayaran,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDepositRequest  $request
     * @param  \App\Models\Deposit  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepositRequest $request, Deposit $pembayaran)
    {
        try {
            $pembayaran->update($request->all());
            return back()->with('success', 'Berhasil mengedit pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $pembayaran)
    {
        try {
            $pembayaran->delete();
            return back()->with('success', 'Berhasil menghapus pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        $data = Deposit::selectRaw("customer_id, DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) AS saldo")->where('customer_id', $request->customer_id)->groupByRaw('customer_id, DATE(created_at)')->orderByRaw('DATE(created_at) ASC')->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_pembayaran_pinjaman_no_rekening_' . $customer->number  . '_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.transaction.installment.print', [
            'title' => 'Laporan Pembayaran Pinjaman',
            'user' => auth()->user(),
            'customer' => $customer,
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data,
            'total' => 0
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
