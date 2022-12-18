<?php

namespace App\Http\Controllers\api;

use App\Helpers\OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderPostRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Date;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $cartJson = json_decode($newOrder["cart"]);
        $cart = [];
        $productArray = [];

        $totalPrice = 0;

        if (count($cartJson) == 0)
            return response(["message" => "Cart is empty"], 422);

        foreach ($cartJson as $product) {
            if (!array_key_exists("quantity", $product) || !array_key_exists("id", $product))
                continue;
            $cart[] = $product;

            try {
                $thisProd = Product::where("id", $product["id"])->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response(["message" => "Product not found", "product_id" => $product["id"]], 404);
            }

            $productArray[$product["id"]] = ["price" => $thisProd->price, "type" => $thisProd->type];
            $totalPrice += $thisProd->price * $product["quantity"];
        }

        if (count($cart) == 0)
            return response(["message" => "Cart is empty"], 422);

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
                    'notes' => $product["notes"] ?? null
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
