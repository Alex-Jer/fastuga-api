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
            return response(['message' => 'You must provide a profile picture for a new user'], 422);

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

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response(['message' => 'User deleted']);
    }

    public function block(User $user)
    {
        if ($user->blocked)
            return response(['message' => 'That user is already blocked'], 400);

        $user->blocked = true;

        foreach ($user->tokens as $token)
            $token->revoke();

        $user->save();
        return response(['message' => 'User blocked']);
    }

    public function unblock(User $user)
    {
        if (!$user->blocked)
            return response(['message' => 'That user is not blocked'], 400);
        $user->blocked = false;
        $user->save();
        return response(['message' => 'User unblocked']);
    }

    public function isMyEmailVerified(Request $request)
    {
        if ($request->user()->email_verified_at)
            return response(['status' => true, 'message' => 'User\'s email is verified', 'email_verified_at' => $request->user()->email_verified_at]);
        return response(['status' => false, 'message' => 'User\'s email is not verified']);
    }

    public function verifyMyEmail(Request $request)
    {
        if ($request->user()->email_verified_at)
            return response(['message' => 'User\'s email is already verified'], 400);
    }
}
