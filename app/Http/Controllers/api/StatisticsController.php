<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        switch ($user->type) {
            case 'C':
                return response(['message' => 'Only employees can view statistics'], 403);
            case 'EC':
                return $this->chefStats($user);
            case 'ED':
                return
                    $this->delivererStats($user);
            case 'EM':
                return $this->managerStats($user);
        }
    }

    public function chefStats(User $user)
    {
        $dishesPrepared = OrderItem::where('preparation_by', $user->id)->where('status', 'R')->count();
        $dishesOnCanceledOrders = OrderItem::withOrder()->where('order_items.preparation_by', $user->id)->where('order_items.status', 'R')->where('orders.status', 'C')->count();
        $dishesNotCancel = $dishesPrepared - $dishesOnCanceledOrders;
        return response([
            'num_dishes_prepared' =>
            [
                'all' => $dishesPrepared,
                'cancelled' => $dishesOnCanceledOrders,
                'not_cancelled' => $dishesNotCancel
            ]
        ]);
    }

    public function delivererStats(User $user)
    {
        $ordersDelivered = OrderItem::where('delivered_by', $user->id)->count();
        $cancelledOrdersDelivered = OrderItem::where('delivered_by', $user->id)->where('status', 'C')->count();
        $notCancelledOrdersDelivered = $ordersDelivered - $cancelledOrdersDelivered;
        return response([
            'num_orders_delivered' =>
            [
                'all' => $ordersDelivered,
                'cancelled' => $cancelledOrdersDelivered,
                'not_cancelled' => $notCancelledOrdersDelivered
            ]
        ]);
    }

    public function managerStats(User $user)
    {
        //Top 10 pratos mais vendidos

        $bestDishes = OrderItem::withOrder()->where('orders.status', '!=', 'C')->groupBy('order_items.product_id')->select('order_items.product_id', DB::raw('count(*) as quantity'))->orderBy('quantity', 'desc')->limit(10)->get()->map(function ($orderItem) {
            $prod = $orderItem->product;
            if ($prod == null)
                $prod = Product::withTrashed()->find($orderItem->product_id);
            return ['product' => new ProductResource($prod), 'deleted' => ($orderItem->product ? false : true), 'quantity' => $orderItem->quantity];
        });

        //Top 10 dias com mais orders
        $bestDays = Order::groupBy('date')->select('date', DB::raw('count(*) as quantity'))->orderBy('quantity', 'desc')->limit(10)->get();

        //Top 10 best customers
        return Order::where('customer_id', "!=", 'null')->join('customers', 'customers.id', 'orders.customer_id')->groupBy('orders.customer_id')->select('orders.customer_id', DB::raw('count(*) as quantity'))->orderBy('quantity', 'desc')->limit(10)->get()->map(function ($order) {
            $customer = Customer::withTrashed()->find($order->customer_id);
            return ['user' => new UserResource($customer->user()), 'deleted' => ($order->customer ? false : true), 'quantity' => $customer->quantity];
        });
        //Top 10 best chefs/servers
        //Top 10
        return response(['message' => 'Not implemented yet'], 501);
    }
}
