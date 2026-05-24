<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminNotification;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
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
        $order = Order::with('histories')->where('user_id', Auth::user()->id)->where('id', $order_id)->first();
    if($order){
     $orderItems = OrderItem::with('product')->where('order_id', $order->id)->orderBy('id')->paginate(12);
        $transection = Transection::where('order_id', $order->id)->first();
        return view('user.order-details',compact('order','orderItems','transection'));
    }
    else{
        return redirect()->route('user.orders')->with('error', 'Order not found');
    }
   
    }

    public function order_cancel(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ]);

        $order = Order::findOrFail($request->order_id);

        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status !== 'ordered') {
            return back()->with('error', 'Only pending orders can be canceled.');
        }

        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();

        AdminNotification::notify(
            'order_canceled',
            'Order ' . $order->order_number . ' Canceled',
            ($order->name ?: Auth::user()->name) . ' canceled their order — Rs ' . number_format($order->total, 0),
            route('admin.order.details', $order->id)
        );

        // Restore stock for each item
        foreach ($order->orderItems as $item) {
            if ($item->variant_id) {
                $variant = ProductVariant::find($item->variant_id);
                if ($variant) {
                    $before = $variant->stock_qty;
                    $variant->stock_qty = $before + $item->quantity;
                    $variant->save();
                    $variant->product->syncStockStatus();
                    InventoryLog::record($item->product_id, 'cancel', $before, $item->quantity, $item->variant_id, null, "Order #{$order->id} cancelled", Auth::id());
                }
            } else {
                $product = Product::find($item->product_id);
                if ($product) {
                    InventoryLog::record($product->id, 'cancel', 0, $item->quantity, null, null, "Order #{$order->id} cancelled", Auth::id());
                }
            }
        }

        return back()->with('success', 'Order canceled successfully');
    }
}
