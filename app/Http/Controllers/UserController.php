<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->title = 'Karyawan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(User::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->id == auth()->user()->id) {
                        return '<a class="badge badge-secondary mx-auto p-2" href="' . route('profile.show') . '">Pengaturan</a>';
                    }

                    if ($row->role == 'manager') {
                        return '<span class="badge badge-warning mx-auto p-2">Aksi Dilarang</span>';
                    }

                    return '<a href="' . route('user.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                            <a href="' . route('user.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                            <form class="d-inline" method="POST" action="' . route('user.destroy', $row) . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                            </form>';
                })
                ->editColumn('role', function($row) {
                    return strtoupper($row->role);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.user.index', [
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
        return view('pages.user.create', [
            'title' => $this->buildTitle('baru')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->except('photo');
            $data['password'] = Hash::make($data['password']);
            $data['photo'] = $this->storeImage($request);
            User::create($data);
            return redirect()->route('user.index')->with('success', 'Berhasil menambahkan karyawan!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function show(User $karyawan)
    {
        return view('pages.user.show', [
            'title' => $this->buildTitle('detail'),
            'user' => $karyawan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function edit(User $karyawan)
    {
        return view('pages.user.edit', [
            'title' => $this->buildTitle('edit'),
            'user' => $karyawan
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $karyawan)
    {
        try {
            $data = $request->except('photo');
            $data['photo'] = $this->updateImage($request, $karyawan->photo);
            $karyawan->update($data);
            return back()->with('success', 'Berhasil mengedit karyawan!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $karyawan)
    {
        try {
            $this->deleteImage($karyawan->photo);
            $karyawan->delete();
            return back()->with('success', 'Berhasil menghapus karyawan!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $data = User::all();
        $manager = User::where('role', 'manager')->first();
        $filter = null;
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_data_karyawan_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.user.print', [
            'title' => 'Laporan Data Karyawan',
            'user' => auth()->user(),
            'filter' => $filter ?? '-',
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
