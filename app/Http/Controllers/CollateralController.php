<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollateralRequest;
use App\Http\Requests\UpdateCollateralRequest;
use App\Models\Collateral;

class CollateralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCollateralRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCollateralRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function show(Collateral $collateral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function edit(Collateral $collateral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCollateralRequest  $request
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCollateralRequest $request, Collateral $collateral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collateral $collateral)
    {
        //
    }
}
