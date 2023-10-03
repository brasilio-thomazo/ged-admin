<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function login(AuthRequest $request): Response
    {
        if (!auth('web')->attempt($request->except('remember'))) {
            throw ValidationException::withMessages(['password' => 'password incorrect']);
        }
        /**
         * @var User
         */
        $user = auth('web')->user();
        $user->groups;

        return response($user);
    }

    public function me(): Response
    {
        /**
         * @var User
         */
        $user = auth('web')->user();
        $user->groups;
        return response($user);
    }

    public function logout()
    {
        auth("web")->logout();
        return response([], 204);
    }
}
