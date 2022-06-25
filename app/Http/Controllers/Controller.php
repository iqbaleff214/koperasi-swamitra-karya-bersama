<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $title = "Dashboard";
    protected $code = "SK";

    protected function buildTitle($section = 'section') {
        return "{$this->title} <span class=\"font-weight-light h5\">$section</span>";
    }

    protected function buildTransactionCode($id) {
        return sprintf($this->code . "-%05d", $id);
    }

    protected function storeImage(Request $request) {
        if ($request->hasFile('photo')) {
            $photo = time() . '.' . $request->file('photo')->extension();
            Storage::putFileAs('public', $request->file('photo'), $photo);
            return $photo;
        }
        return null;
    }

    protected function deleteImage($name)
    {
        if ($name) {
            Storage::delete("public/$name");
            return true;
        }
        return false;
    }

    protected function updateImage(Request $request, $name) {
        if ($request->hasFile('photo')) $this->deleteImage($name);
        return $this->storeImage($request);
    }
}
