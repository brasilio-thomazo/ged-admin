<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrivilegeRequest;
use App\Http\Requests\UpdatePrivilegeRequest;
use App\Models\Privilege;

class PrivilegeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrivilegeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Privilege $privilege)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrivilegeRequest $request, Privilege $privilege)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Privilege $privilege)
    {
        //
    }
}
