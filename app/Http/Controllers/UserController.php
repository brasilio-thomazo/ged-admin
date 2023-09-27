<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $builder = User::with(['groups', 'groups.privilege'])
            ->whereNot('username', '=', 'admin')
            ->whereNot('username', '=', 'system');
        return response($builder->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        /**
         * @var User
         */
        $user = User::create($request->except(['groups']));
        $user->groups()->attach($request->groups);
        foreach ($user->groups as $group) $group->privilege;
        return response($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        foreach ($user->groups as $group) $group->privilege;
        return response($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $update = array_filter($request->except(['groups', 'password']), function ($value, $key) use ($user) {
            return $value != $user->$key;
        }, ARRAY_FILTER_USE_BOTH);

        if ($request->password) {
            $update['password'] = $request->passwor;
        }

        if (count($update)) {
            $user->update($update);
        }

        $user->groups()->sync($request->groups);
        foreach ($user->groups as $group) $group->privilege;
        return response($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->groups()->detach();
        $user->delete();
        return response([], 204);
    }
}
