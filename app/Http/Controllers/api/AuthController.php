<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
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

        if ($authUser->blocked) {
            return response(['message' => 'This account is blocked from using our services'], 403);
        }

        $accessToken = $authUser->createToken('authToken', $authUser->scopes())->accessToken;

        return response(['message' => 'Login was successful', 'user' => new UserResource($authUser), 'access_token' => $accessToken]);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['message' => 'Token revoked']);
    }

    /*
    public function scopes()
    {
        return Auth::user()->scopes();
    }

    public function testAllScopes(Request $request)
    {
        $typesArr = User::select("type")->distinct()->get();
        $types = [];
        foreach ($typesArr as $type) {
            $types[] = $type->type;
        }
        $str = [];
        foreach ($types as $type) {
            $str[] = User::where("type", $type)->first()->scopes();
        }
        return response($str);
    }
    */
    public function test(Request $request)
    {
        return response(["message" => "Test was successful"]);
    }
}
