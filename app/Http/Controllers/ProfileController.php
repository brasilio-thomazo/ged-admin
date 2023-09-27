<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function login(AuthRequest $request): Response
    {
        if (!auth('web')->attempt($request->except('remember'))) {
            throw ValidationException::withMessages(['password' => 'password incorrect']);
        }

        return response(auth('web')->user());
    }

    public function me(): Response
    {
        return response(auth('web')->user());
    }

    public function logout()
    {
        auth("web")->logout();
        return response([], 204);
    }
}
