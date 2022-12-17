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

        //Delete previous photo
        if (
            $deleteUserPhoto &&
            $user->photo_url
        )
            Storage::delete(StorageLocation::USER_PHOTOS . '/' . $user->photo_url);

        $user->update($newUser);
    }
}
