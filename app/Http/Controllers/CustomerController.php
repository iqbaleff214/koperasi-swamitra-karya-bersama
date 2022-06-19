<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->title = 'Nasabah';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Customer::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('customer.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                            <a href="' . route('customer.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                            <form class="d-inline" method="POST" action="' . route('customer.destroy', $row) . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                            </form>';
                })
                ->editColumn('joined_at', function($row) {
                    return Carbon::parse($row->joined_at)->isoFormat('DD-MM-Y');
                })
                ->editColumn('status', function($row) {
                    if ($row->status == 'blacklist') {
                        return '<span class="badge d-block p-2 badge-danger">Blacklist</span>';
                    }
                    return '<span class="badge d-block p-2 badge-success">Active</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('pages.customer.index', [
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
        return view('pages.customer.create', [
            'title' => $this->buildTitle('baru')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->except('amount');
            $customer = Customer::create($data);
            Deposit::create([
                'amount' => $request->get('amount'),
                'current_balance' => $request->get('amount'),
                'type' => 'pokok',
                'customer_id' => $customer->id,
            ]);

            DB::commit();
            return redirect()->route('customer.index')->with('success', 'Berhasil menambahkan nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $nasabah)
    {
        // $data = Deposit::selectRaw("customer_id, DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) AS saldo")->where('customer_id', 5)->groupByRaw('customer_id, DATE(created_at)')->orderByRaw('DATE(created_at) DESC')->get();
        // dd($data);
        // SELECT SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits WHERE customer_id = 5;
        // SELECT customer_id, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits GROUP By customer_id;
        // SELECT DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits WHERE customer_id = 5 GROUP BY DATE(created_at);
        // SELECT customer_id, DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits GROUP BY customer_id, DATE(created_at);
        return view('pages.customer.show', [
            'title' => $this->buildTitle('detail'),
            'user' => $nasabah
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $nasabah)
    {
        return view('pages.customer.edit', [
            'title' => $this->buildTitle('edit'),
            'user' => $nasabah
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $nasabah)
    {
        try {
            $nasabah->update($request->all());
            return back()->with('success', 'Berhasil mengedit nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $nasabah)
    {
        try {
            $nasabah->delete();
            return back()->with('success', 'Berhasil menghapus nasabah!');
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

        $data = Customer::whereBetween('joined_at', $filter)->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_data_nasabah_periode_' . $time_from . '_-_' . $time_to  . '_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.customer.print', [
            'title' => 'Laporan Data Nasabah',
            'user' => auth()->user(),
            'filter' => "$time_from sampai $time_to",
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    public function loan($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => Loan::where('customer_id', $id)->get(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }
}
