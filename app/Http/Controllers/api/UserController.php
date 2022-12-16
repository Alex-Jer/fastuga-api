<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserPostRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allUsers()
    {
        return UserResource::collection(User::all());
    }

    public function showMe(Request $request)
    {
        //return new UserResource($request->user());
    }

    public function updateMe(Request $request)
    {
        //return new UserResource($request->user());
    }

    public function store(UserPostRequest $request)
    {
        $newUser = $request->validated();

        if (!$request->hasFile('photo'))
            return response(['message' => 'You must provide a profile picture for a new user'], 400);

        $newUser['photo_url'] = basename($request->file('photo')->store('public/fotos'));
        unset($newUser['photo']);

        return response(["message" => "User created", "user" => User::create($newUser)]);
    }
}
