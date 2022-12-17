<?php

namespace App\Http\Controllers\api;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CustomerPostRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allCostumers()
    {
        //To also return user information
        return UserResource::collection(User::where('type', 'C')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerPostRequest $request)
    {
        $newCustomer = $request->validated();

        $newUser = [
            "name" => $newCustomer["name"],
            "email" => $newCustomer["email"],
            "password" => $newCustomer["password"],
            "type" => $newCustomer["type"]
        ];

        //Unset all user fields from newCustomer
        foreach ($newUser as $key => $value)
            unset($newCustomer[$key]);

        $regUser = UserHelper::registerUser($request, $newUser);

        $newCustomer["user_id"] = $regUser->id;

        $regCustomer = Customer::create($newCustomer);

        return response(["message" => "Customer user created", "user" => new UserResource($regCustomer->user)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function updateCustomer(Request $request, Customer $customer)
    {
        $newCustomer = $request->validated();

        $newUser = [
            "name" => $newCustomer["name"],
            "photo" => $newCustomer["photo"],
            "remove_photo" => $newCustomer["remove_photo"]
        ];

        //Unset all user fields from newCustomer
        foreach ($newUser as $key => $value)
            unset($newCustomer[$key]);

        UserHelper::updateUser($request, $newUser, $customer->user);

        $customer->update($newCustomer);

        return response(['message' => 'User updated']);
    }
}
