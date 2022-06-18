<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard', [
            'title' => 'Dashboard'
        ]);
    }

    public function profile()
    {
        return view('profile', [
            'title' => 'Pengaturan',
            'profile' => Auth::user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            Auth::user()->update($request->all());
            return back()->with('success', 'Berhasil mengupdate profil!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
