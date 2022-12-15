<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($login)) {
            return response(['message' => 'Invalid login credentials'], 401);
        }

        $authUser = Auth::user();
        $accessToken = $authUser->createToken('authToken', $authUser->scopes())->accessToken;

        return response(['message' => 'Login was successfull', 'user' => $authUser, 'access_token' => $accessToken]);
    }

    // public function scopes()
    // {
    //     return Auth::user()->scopes();
    // }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['message' => 'Token revoked']);
    }
}
