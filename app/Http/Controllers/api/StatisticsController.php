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
        $ordersDelivered = Order::where('delivered_by', $user->id)->count();
        $cancelledOrdersDelivered = Order::where('delivered_by', $user->id)->where('status', 'C')->count();
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
            return ['product' => [/*'id' => $prod->id, */'name' => $prod->name, 'photo_url' => $prod->photo_url, 'type' => $prod->type], 'deleted' => ($orderItem->product ? false : true), 'quantity' => $orderItem->quantity];
        });

        /*$worstDishes = OrderItem::withOrder()->where('orders.status', '!=', 'C')->groupBy('order_items.product_id')->select('order_items.product_id', DB::raw('count(*) as quantity'))->orderBy('quantity', 'asc')->limit(10)->get()->map(function ($orderItem) {
            $prod = $orderItem->product;
            if ($prod == null)
                $prod = Product::withTrashed()->find($orderItem->product_id);
            return ['product' => ['id' => $prod->id, 'name' => $prod->name, 'photo_url' => $prod->photo_url, 'type' => $prod->type], 'deleted' => ($orderItem->product ? false : true), 'quantity' => $orderItem->quantity];
        });*/

        //Top 10 best customers
        $bestCustomers =  Order::where('customer_id', "!=", 'null')->join('customers', 'customers.id', 'orders.customer_id')->join('users', 'users.id', 'customers.user_id')->groupBy('orders.customer_id')->select('orders.customer_id', 'users.name', 'users.photo_url', 'users.deleted_at', 'customers.user_id', DB::raw('count(*) as quantity'))->orderBy('quantity', 'desc')->limit(10)->get()->map(function ($order) {
            return [
                'user' => [
                    /*'id' => $order->user_id,*/
                    'name' => $order->name,
                    'photo_url' => $order->photo_url
                ], 'deleted' => ($order->deleted_at ? false : true),
                'quantity' => $order->quantity
            ];
        });

        //Top 10 dias com mais orders
        $bestDays = Order::groupBy('date')->select('date', DB::raw('count(*) as quantity'))->orderBy('quantity', 'desc')->limit(10)->get();

        //Meses com mais orders
        $monthsByQuantity = Order::select(DB::raw('YEAR(DATE) as year'), DB::raw('MONTH(DATE) as month'), DB::raw('count(*) as quantity'))->groupBy(DB::raw('1, 2'))->orderBy('year', 'asc')->orderBy('month', 'asc')->get();

        //Top 10 dias mais profitable
        $bestProfitDays = Order::groupBy('date')->select('date', DB::raw('SUM(total_paid) as money_made'))->orderBy('money_made', 'desc')->limit(10)->get();

        //Meses mais profitable
        $monthsByProfit = Order::select(DB::raw('YEAR(DATE) as year'), DB::raw('MONTH(DATE) as month'), DB::raw('SUM(total_paid) as money_made'))->groupBy(DB::raw('1, 2'))->orderBy('year', 'asc')->orderBy('month', 'asc')->get();

        return response(['best_dishes' => $bestDishes, 'best_customers' => $bestCustomers, 'best_days' => ['num_orders' => $bestDays, 'profit' => $bestProfitDays], 'months_history' => ['num_orders' => $monthsByQuantity, 'profit' => $monthsByProfit]]);
    }
}
