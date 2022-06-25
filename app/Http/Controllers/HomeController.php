<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.dashboard', [
            'title' => 'Dashboard'
        ]);
    }

    public function profile()
    {
        return view('pages.profile', [
            'title' => 'Pengaturan',
            'profile' => Auth::user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->except('photo');
            $data['photo'] = $this->updateImage($request, $user->photo);
            $user->update($data);
            return back()->with('success', 'Berhasil mengupdate profil!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function truncate()
    {
        try {
            Artisan::call('migrate:fresh --seed');
            Auth::logout();
            return back()->with('success', 'Berhasil mereset data!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
