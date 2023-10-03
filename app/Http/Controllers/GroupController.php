<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $build = Group::whereNot('is_admin', true);
        return response($build->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $data = $request->all();
        $data['authorities'] = Group::makeAuthorities($data['privilege']);
        $group = Group::create($data);
        return response($group, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return response($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {

        $data = $request->all();
        $data['authorities'] = Group::makeAuthorities($data['privilege']);
        $group->update($data);
        return response($group, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response([], 204);
    }
}
