<?php

declare(strict_types=1);

namespace Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\Response | array
    {
        $credentials = $request->validate(
            [
                'email'    => ['required', 'email'],
                'password' => ['required'],
            ]
        );

        if (Auth::attempt($credentials, (bool) $request->input('remember'))) {
            $request->session()->regenerate();
            return ['id' => Auth::user()->id];
        } else {
            return Response::make(['message' => __('auth.failed')], 401);
        }
    }

    public function getCurrentAuth(): array
    {
        $user = Auth::user();
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
