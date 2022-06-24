<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\User;
use App\Traits\LoanTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InstallmentController extends Controller
{
    use LoanTrait;

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
                ->rawColumns(['action', 'customer', 'balance'])
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
            DB::beginTransaction();
            $pembayaran = Deposit::where('customer_id', $request->customer_id)->latest()->first();
            $data = $request->all();
            $data['previous_balance'] = $pembayaran->current_balance ?? 0;
            $data['current_balance'] = $data['previous_balance'] + $request->amount;
            Deposit::create($data);
            if ($pembayaran && $request->type == 'wajib' && $request->loan_id) {
                $this->paidLoan($request->loan_id);
            }
            DB::commit();
            return redirect()->route('transaction.installment.index')->with('success', 'Berhasil menambahkan pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
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
            DB::beginTransaction();
            $pembayaran->update($request->all());
            if ($pembayaran && $request->type == 'wajib' && $pembayaran->loan_id) {
                $this->paidLoan($pembayaran->loan_id);
            }
            DB::commit();
            return back()->with('success', 'Berhasil mengedit pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
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
            DB::beginTransaction();
            $type = $pembayaran->type;
            $id = $pembayaran->loan_id;
            $pembayaran->delete();
            if ($type == 'wajib' && $id) {
                $this->paidLoan($id);
            }
            DB::commit();
            return back()->with('success', 'Berhasil menghapus pembayaran simpanan nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $filter = $request->validate([
            'time_from' => 'required',
            'time_to' => 'required',
        ]);

        $time_from = Carbon::parse($filter['time_from']);
        $time_to = Carbon::parse($filter['time_to']);

        // SELECT DATE(MAX(deposits.created_at)) as tanggal, SUM(deposits.amount) AS bayar, loans.amount AS hutang, customers.name FROM deposits JOIN customers ON customers.id = deposits.customer_id JOIN loans ON loans.id = deposits.loan_id WHERE deposits.type='wajib' AND deposits.loan_id IS NOT NULL GROUP BY deposits.loan_id, deposits.customer_id;

        $data = Deposit::selectRaw("DATE(MAX(deposits.created_at)) as tanggal, SUM(deposits.amount) AS bayar, loans.amount AS hutang, customers.name, customers.number")
            ->join('loans', 'loans.id', '=', 'deposits.loan_id')
            ->join('customers', 'customers.id', '=', 'deposits.customer_id')
            ->where('type', 'wajib')
            ->groupByRaw('deposits.loan_id, deposits.customer_id')
            ->havingRaw('MONTH(MAX(deposits.created_at)) >= ? AND MONTH(MAX(deposits.created_at)) <= ? AND YEAR(MAX(deposits.created_at)) >= ? AND YEAR(MAX(deposits.created_at)) <= ?', [$time_from->month, $time_to->month, $time_from->year, $time_to->year])
            ->orderByRaw('DATE(MAX(deposits.created_at)) ASC')
            ->get();

        // dd($data);
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_pembayaran_pinjaman_periode_' . $filter['time_from'] . '_-_' . $filter['time_to']  . '_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.transaction.installment.print', [
            'title' => 'Laporan Pembayaran Pinjaman',
            'user' => auth()->user(),
            'filter' => $time_from->isoFormat('MMMM Y') . " - " . $time_to->isoFormat('MMMM Y'),
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data,
            'total' => 0
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream($filename);
    }
}
