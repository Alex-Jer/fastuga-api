<?php

namespace App\Http\Controllers\api;

use App\Helpers\OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderPostRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Auth;
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


        $usr = Auth::guard('api')->user();
        if ($usr) {
            if ($usr->type !== 'C')
                return response(['message' => 'Only customers can place orders'], 403);
            $newOrder["customer_id"] = $usr->customer->id;
        }

        return $this->processNewOrder($newOrder, $usr);
    }

    public function processNewOrder($newOrder, $usr)
    {
        $cartJson = json_decode($newOrder["cart"]);
        $cart = [];
        $productArray = [];

        $totalPrice = 0;

        if (count($cartJson) == 0)
            return response(["message" => "Cart is empty"], 422);
        foreach ($cartJson as $product) {
            if (!$product->quantity || !$product->id)
                continue;
            $cart[] = $product;

            try {
                $thisProd = Product::where("id", $product->id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response(["message" => "Product not found", "product_id" => $product->id], 404);
            }

            $productArray[$product->id] = ["price" => $thisProd->price, "type" => $thisProd->type];
            $totalPrice += $thisProd->price * $product->quantity;
        }

        if (count($cart) == 0)
            return response(["message" => "Cart is empty"], 422);

        $pointsUsed = $newOrder["points_used"] ?? 0;
        if ($usr && $pointsUsed > 0) {
            if ($pointsUsed % 10 != 0)
                return response(["message" => "Points must be used in batches of 10"], 422);

            $eur_per_10_pts = OrderHelper::EUR_PER_10_POINTS;

            if (($pointsUsed / 10) * $eur_per_10_pts > $totalPrice)
                return response(["message" => "Points used exceed total price"], 422);

            $cstmr = $usr->customer;

            if ($pointsUsed > 0) {
                if ($cstmr->points < $pointsUsed) {
                    return response(["message" => "Not enough points"], 403);
                }

                $newOrder['points_used_to_pay'] = $pointsUsed;
                $newOrder['total_paid_with_points'] = ($pointsUsed / 10) * $eur_per_10_pts;
            }

            $newOrder['points_gained'] = floor($totalPrice / 10);
        } else {
            $newOrder['points_used_to_pay'] = 0;
            $newOrder['total_paid_with_points'] = 0;
            $newOrder['points_gained'] = 0;
        }

        $ticketNumber = OrderHelper::nextTicketNumber();
        $newOrder['ticket_number'] = $ticketNumber;

        $newOrder['status'] = 'P';
        $newOrder['total_price'] = $totalPrice;
        $newOrder['total_paid'] = array_key_exists("total_paid_with_points", $newOrder) ? $totalPrice - $newOrder['total_paid_with_points'] : $totalPrice;

        $newOrder['date'] = Date::now();

        $payVal = OrderHelper::processPayment($newOrder["payment_type"], $newOrder["payment_reference"], $newOrder['total_paid']);

        if ($payVal['status'] == false) {
            return response(["message" => $payVal['message']], 402); //We are using 402 as a "payment failed" error code
        }

        if ($usr) {
            $cstmr->points -= $pointsUsed;
            $cstmr->save();
        }
        $regOrder = Order::create($newOrder);

        $count = 1;
        foreach ($cart as $product) {
            for ($i = 0; $i < $product->quantity; $i++) {
                OrderItem::create([
                    'order_local_number' => $count,
                    "order_id" => $regOrder->id,
                    "product_id" => $product->id,
                    "price" => $productArray[$product->id]["price"],
                    "status" => $productArray[$product->id]["type"] === "hot dish" ? 'W' : 'R',
                    'notes' => $product->notes ?? null
                ]);
                $count++;
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
        return new OrderResource($order);
    }

    public function cancel(Order $order)
    {
        if ($order->status === 'C')
            return response(['message' => 'This order was already cancelled'], 422);
        $res = OrderHelper::processRefund($order->payment_type, $order->payment_reference, $order->total_paid);

        if ($order->customer) {
            $order->customer->points += $order->points_used_to_pay;
            $order->customer->save();
        }

        $order->status = 'C';
        $order->save();

        if ($res['status'] == false)
            return response(['message' => 'Order was cancelled but refund failed: ' . $res['message']], 402);
        else
            return response(['message' => 'Order cancelled']);
    }

    public function isDishInvalid(Order $order, OrderItem $item)
    {
        if ($order->id !== $item->order->id)
            return response(['message' => 'This item does not belong to this order'], 400);
        if ($order->status !== 'P')
            return response(['message' => 'This order is no longer preparing'], 422);
        if ($item->product->type !== 'hot dish')
            return response(['message' => 'This item is not a hot dish'], 422);
        return false;
    }

    public function prepareDish(Request $request, Order $order, OrderItem $item)
    {
        $isInvalid = $this->isDishInvalid($order, $item);
        if ($isInvalid)
            return $isInvalid;

        if ($item->status !== 'W')
            return response(['message' => 'This dish is already ' . ($item->status === 'R' ? 'ready' : 'preparing')], 422);

        $item->status = 'P';
        $item->preparation_by = $request->user()->id;
        $item->save();

        return response(['message' => 'Set dish state to preparing']);
    }

    public function finishDish(Request $request, Order $order, OrderItem $item)
    {
        $isInvalid = $this->isDishInvalid($order, $item);
        if ($isInvalid)
            return $isInvalid;

        if ($item->status !== 'P')
            return response(['message' => 'This dish ' . ($item->status === 'W' ? 'hasn\'t been prepared yet' : 'is already marked as ready')], 422);

        $item->status = 'R';
        $item->preparation_by = $request->user()->id;
        $item->save();

        return response(['message' => 'Set dish state to ready']);
    }
}
