<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Privilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    private function validatePrivilege(Request $request, $id = null)
    {
        $privilege = ['group' => null, 'user' => null, 'client' => null, 'app' => null];
        $privilege = array_merge($privilege, $request->privilege);
        if (isset($privilege['user']) && $privilege['user'] == 'rw' && !isset($privilege['group'])) {
            $privilege['group'] = 'r';
        }

        if ($privilege['client'] && preg_match('/^r|rw$/', $privilege['client']) && !$privilege['app']) {
            $privilege['app'] = 'r';
        }

        if (isset($privilege['app']) && preg_match('/^r|rw$/', $privilege['app']) && !$privilege['client']) {
            $privilege['client'] = 'r';
        }

        $rule = Rule::unique('privileges', 'group')
            ->where('user', $privilege['user'])
            ->where('client', $privilege['client'])
            ->where('app', $privilege['app']);

        if ($id) {
            $rule->ignore($id);
        }

        return Validator::make(
            ['privilege' => $privilege['group']],
            ['privilege' => $rule]
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $build = Group::with('privilege')->whereNot('name', 'Administradores');
        return response($build->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $this->validatePrivilege($request)->validate();
        $privilege = $request->privilege;
        /**
         * @var Group
         */
        $group = Group::create($request->except(['privilege']));
        $privilege['authorities'] = $privilege;
        $group->privilege()->save(new Privilege($privilege));
        $group['privilege'] = $group->privilege;
        return response($group, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group['privilege'] = $group->privilege;
        return response($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->privilege;
        $this->validatePrivilege($request, $group->privilege->id)->validate();
        $privilege = $request->privilege;

        if (count($privilege)) {
            $privilege['authorities'] = $privilege;
            $group->privilege()->update($privilege);
            $group->refresh();
        }

        if ($group->name != $request->name) {
            $group->update(['name' => $request->name]);
        }

        return response($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->privilege()->delete();
        $group->delete();
        return response([], 204);
    }
}
