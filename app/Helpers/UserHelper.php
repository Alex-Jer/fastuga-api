<?php

namespace App\Helpers;

use App\Models\User;
use Hash;
use Illuminate\Auth\Events\Registered;
use Storage;

class UserHelper
{
    public static function registerUser($request, $newUser)
    {
        if ($request->hasFile('photo')) {
            $newUser['photo_url'] = basename($request->file('photo')->store(StorageLocation::USER_PHOTOS));
            unset($newUser['photo']);
        }

        $newUser["password"] = Hash::make($newUser["password"]);

        $regUser = User::create($newUser);

        //event(new Registered($regUser));

        return $regUser;
    }

    public static function updateUser($request, $newUser, $user)
    {
        $deleteUserPhoto = false;
        if ($request->hasFile('photo')) {
            $newUser['photo_url'] = basename($request->file('photo')->store(StorageLocation::USER_PHOTOS));
            unset($newUser['photo']);

            $deleteUserPhoto = true;
        } else if ($request->has('remove_photo') && $request->remove_photo) {
            $newUser['photo_url'] = null;
            $deleteUserPhoto = true;
        }

        //Delete photo when true
        if (
            $deleteUserPhoto &&
            $user->photo_url
        )
            Storage::delete(StorageLocation::USER_PHOTOS . '/' . $user->photo_url);

        $user->update($newUser);
    }

    public static function getTypeDesignation($type)
    {
        $ut = strtoupper($type);
        switch ($ut) {
            case 'C':
                return 'Customer';
            case 'EC':
                return 'Chef';
            case 'ED':
                return 'Server';
            case 'EM':
                return 'Manager';
            default:
                return 'Unknown (' . $ut . ')';
        }
    }

    public static function createAccessToken($authUser)
    {
        return $authUser->createToken('authToken', $authUser->scopes())->accessToken;
    }
}
