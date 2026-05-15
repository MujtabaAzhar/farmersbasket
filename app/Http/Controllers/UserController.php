<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transection;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(){
        return view('user.index');
    }
    public function orders(){
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }
    public function order_details($order_id)
    {
        $order = Order::where('user_id',Auth::user()->id)->where('id', $order_id)->first();
    if($order){
     $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
        $transection = Transection::where('order_id', $order->id)->first();
        return view('user.order-details',compact('order','orderItems','transection'));
    }
    else{
        return redirect()->route('user.orders')->with('error', 'Order not found');
    }
   
    }

    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('success', 'Order canceled successfully');
    }
}
