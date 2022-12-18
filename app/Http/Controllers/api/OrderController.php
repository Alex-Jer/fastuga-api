<?php

namespace App\Http\Controllers\api;

use App\Helpers\OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\OrderPostRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Date;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderPostRequest $request)
    {
        $newOrder = $request->validated();

        $cart = json_decode($newOrder["cart"]);
        $productArray = [];

        $totalPrice = 0;

        foreach ($cart as $product) {
            $thisProd = Product::where("id", $product["id"])->firstOrFail(); //TODO: test this
            $productArray[$product["id"]] = ["price" => $thisProd->price, "type" => $thisProd->type];
            $totalPrice += $thisProd->price * $product["quantity"];
        }

        if ($request->user() && $request->user()->customer) {
            $pointsUsed = $newOrder["points_used"] ?? 0;
            $pointsUsed = floor($pointsUsed / 10) * 10; //floor to nearest 10 in case someone tries to spend points not in 10 points blocks
            $cstmr = $request->user()->customer;

            if ($pointsUsed > 0) {
                if ($cstmr->points < $pointsUsed) {
                    return response(["message" => "Not enough points"], 403);
                }
                $cstmr->points -= $pointsUsed;
                $cstmr->save();

                $newOrder['points_used_to_pay'] = $pointsUsed;
                $newOrder['total_paid_with_points'] = $pointsUsed * 5.0;
            }

            $newOrder['points_gained'] = floor($totalPrice / 10);
        }

        $newOrder['ticket_number'] = OrderHelper::nextTicketNumber();

        $newOrder['status'] = 'P';
        $newOrder['total_price'] = $totalPrice;
        $newOrder['total_paid'] = $newOrder['total_paid_with_points'] ? $totalPrice - $newOrder['total_paid_with_points'] : $totalPrice;

        $newOrder['date'] = Date::now();

        $payVal = OrderHelper::processPayment($newOrder["payment_type"], $newOrder["payment_reference"], $newOrder['total_paid']);

        if ($payVal['status'] == false) {
            return response(["message" => $payVal['message']], 402); //We are using 402 as a "payment failed" error code
        }

        $regOrder = Order::create($newOrder);

        foreach ($cart as $product) {
            for ($i = 0; $i < $product["quantity"]; $i++) {
                OrderItem::create([
                    "order_id" => $regOrder->id,
                    "product_id" => $product["id"],
                    "price" => $productArray[$product["id"]]["price"],
                    "status" => $productArray[$product["id"]]["type"] === "hot dish" ? 'W' : 'R',
                    'notes' => $product["notes"]
                ]);
            }
        }

        return response(["message" => "Order placed", "order" => new OrderResource($regOrder)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
