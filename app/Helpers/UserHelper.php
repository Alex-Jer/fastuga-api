<?php

namespace App\Helpers;

use App\Models\User;
use Hash;
use Illuminate\Auth\Events\Registered;

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

        event(new Registered($regUser));

        return $regUser;
    }
}
