<?php

namespace App\Http\Controllers\api;

use App\Helpers\StorageLocation;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserPostRequest;
use App\Http\Requests\User\UserPutRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class UserController extends Controller
{
    public const storage_loc = StorageLocation::USER_PHOTOS;
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
        return new UserResource($request->user());
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

        $newUser['photo_url'] = basename($request->file('photo')->store($this->storage_loc));
        unset($newUser['photo']);

        return response(["message" => "User created", "user" => User::create($newUser)]);
    }

    public function update(UserPutRequest $request, User $user)
    {
        $newUser = $request->validated();

        if ($request->hasFile('photo')) {
            $newUser['photo_url'] = basename($request->file('photo')->store($this->storage_loc));
            unset($newUser['photo']);

            //Delete previous photo
            $newUser->photo_url ? Storage::delete($this->storage_loc . '/' . $newUser->photo_url) : null;
        }

        $user->update($newUser);
        return response(['message' => 'User updated']);
    }
}
