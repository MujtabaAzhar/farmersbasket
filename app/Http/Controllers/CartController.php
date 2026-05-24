<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use App\Models\AdminNotification;
use App\Services\NotificationService;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transection;
use App\Models\User;
use App\Models\GiftOrder;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index()
    {
         $shipping_fee = config('cart.shipping', 0);
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items', 'shipping_fee'));
    }

    public function add_to_cart(Request $request)
    {
        $options = [];
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::find($request->variant_id);
            if ($variant) {
                $options['variant_id']    = $variant->id;
                $options['variant_label'] = $variant->display_label;
                // Use variant price if no explicit price passed
                if (!$request->filled('price')) {
                    $request->merge(['price' => $variant->price]);
                }
            }
        }
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price, $options)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $product->qty + 1);
        return redirect()->back();
    }
    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $product->qty - 1);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }
    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code)){
            $subtotal = str_replace(',', '', Cart::instance('cart')->subtotal());
            $coupon = Coupon::where('code', $coupon_code)->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', $subtotal)->first();
            if(!$coupon){
                return redirect()->back()->with('error', 'Invalid coupon code.');
            } else {
                 Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculate_discount();
                return redirect()->back()->with('success', 'Coupon applied successfully!');
            }
        }
        else{
            return redirect()->back()->with('error', 'Please enter a coupon code.');
        }

    }

    public function calculate_discount()
    {
        $shipping_fee = config('cart.shipping', 0);
        $discount = 0;
        if(Session::has('coupon')){
            $subtotal = str_replace(',', '', Cart::instance('cart')->subtotal());
            $coupon = Session::get('coupon');
            if($coupon['type'] == 'fixed'){
                $discount = $coupon['value'];
            }
            elseif($coupon['type'] == 'percent'){
                $discount = ($subtotal * $coupon['value']) / 100;
            }
            $subtotal_after_discount = $subtotal - $discount;
            $tax_after_discount = ($subtotal_after_discount * config('cart.tax')) / 100;
            $total_after_discount = $subtotal_after_discount + $tax_after_discount + $shipping_fee;
            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotal_after_discount), 2, '.', ''),
                'tax' => number_format(floatval($tax_after_discount), 2, '.', ''),
                'shipping' => number_format(floatval($shipping_fee), 2, '.', ''),
                'total' => number_format(floatval($total_after_discount), 2, '.', ''),
            ]);
        }
    }

    public function remove_coupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }
    public function checkout()
    {
        $address = null;
        if(Auth::check())
        {
            $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();              
        }
        // Initialize checkout session with current cart totals
        $this->setAmountForCheckout();
        return view('checkout',compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $isGift = $request->gift === 'Yes';

        // Build conditional validation rules
        $rules = [
            'gift'     => 'required|in:Yes,No',
            'mode'     => 'required',
            'zip'      => 'required',
            'state'    => 'required',
            'locality' => 'required',
            'country'  => 'required',
            'landmark' => 'required',
        ];

        if ($isGift) {
            $rules['gift_sender_name']       = 'required|string|max:255';
            $rules['gift_sender_phone']      = 'required|string|max:20';
            $rules['gift_receiver_name']     = 'required|string|max:255';
            $rules['gift_receiver_phone']    = 'required|string|max:20';
            $rules['gift_receiver_address']  = 'required|string';
            if (!Auth::check()) {
                $rules['gift_sender_email'] = 'required|email';
            }
        } else {
            $rules['name']    = 'required';
            $rules['email']   = 'required|email';
            $rules['phone']   = 'required';
            $rules['city']    = 'required';
            $rules['address'] = 'required';
        }

        $request->validate($rules);

        // Resolve the user
        if (Auth::check()) {
            $user_id = Auth::id();
        } else {
            $lookup_email = $isGift ? $request->gift_sender_email : $request->email;
            $lookup_phone = $isGift ? $request->gift_sender_phone : $request->phone;
            $user = User::where('email', $lookup_email)->orWhere('mobile', $lookup_phone)->first();
            if (!$user) {
                $user = new User();
                $user->name   = $isGift ? $request->gift_sender_name : $request->name;
                $user->email  = $lookup_email;
                $user->mobile = $lookup_phone;
                $user->password = Hash::make($lookup_phone);
                $user->save();
            }
            Auth::login($user);
            $user_id = $user->id;
        }

        // For regular orders: persist/reuse default address
        if (!$isGift) {
            $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
            if (!$address) {
                $address = new Address();
                $address->user_id  = $user_id;
                $address->name     = $request->name;
                $address->phone    = $request->phone;
                $address->zip      = $request->zip;
                $address->state    = $request->state;
                $address->city     = $request->city;
                $address->address  = $request->address;
                $address->locality = $request->locality;
                $address->landmark = $request->landmark;
                $address->country  = $request->country;
                $address->isdefault = true;
                $address->save();
            }
        }

        $this->setAmountForCheckout();

        $order = new Order();
        $order->user_id  = $user_id;
        $order->subtotal = str_replace(',', '', Session::get('checkout')['subtotal']);
        $order->discount = str_replace(',', '', Session::get('checkout')['discount']);
        $order->tax      = str_replace(',', '', Session::get('checkout')['tax']);
        $order->total    = str_replace(',', '', Session::get('checkout')['total']);
        $order->locality = $request->locality;
        $order->landmark = $request->landmark;
        $order->zip      = $request->zip;
        $order->state    = $request->state;
        $order->country  = $request->country;

        if ($isGift) {
            // Deliver to receiver
            $order->name    = $request->gift_receiver_name;
            $order->phone   = $request->gift_receiver_phone;
            $order->city    = $request->gift_receiver_city ?? 'N/A';
            $order->address = $request->gift_receiver_address;
            $order->type    = 'gift';
        } else {
            $order->name    = $address->name;
            $order->phone   = $address->phone;
            $order->city    = $address->city;
            $order->address = $address->address;
        }

        $order->save();

        AdminNotification::notify(
            'new_order',
            'New Online Order ' . $order->order_number,
            ($order->name ?: 'Guest') . ' — Rs ' . number_format($order->total, 0),
            route('admin.order.details', $order->id)
        );

        NotificationService::orderPlaced($order);

        // Save order items and deduct stock
        foreach (Cart::instance('cart')->content() as $item) {
            $variantId    = $item->options->variant_id ?? null;
            $variantLabel = $item->options->variant_label ?? null;

            $orderitem                = new OrderItem();
            $orderitem->product_id    = $item->id;
            $orderitem->variant_id    = $variantId;
            $orderitem->variant_label = $variantLabel;
            $orderitem->order_id      = $order->id;
            $orderitem->price         = $item->price;
            $orderitem->quantity      = $item->qty;
            $orderitem->save();

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $before = $variant->stock_qty;
                    $variant->stock_qty = max(0, $before - $item->qty);
                    $variant->save();
                    $variant->product->syncStockStatus();
                    InventoryLog::record($item->id, 'order', $before, -$item->qty, $variantId, null, "Order #{$order->id}", $user_id);
                }
            }
        }

        // Save gift order details
        if ($isGift) {
            GiftOrder::create([
                'order_id'          => $order->id,
                'sender_name'       => $request->gift_sender_name,
                'sender_phone'      => $request->gift_sender_phone,
                'sender_email'      => Auth::user()->email ?? $request->gift_sender_email,
                'sender_address'    => $request->gift_sender_address,
                'receiver_name'     => $request->gift_receiver_name,
                'receiver_phone'    => $request->gift_receiver_phone,
                'receiver_city'     => $request->gift_receiver_city,
                'receiver_address'  => $request->gift_receiver_address,
                'gift_message'      => $request->gift_message,
            ]);
        }

        // Record transaction
        $transaction = new Transection();
        $transaction->user_id  = $user_id;
        $transaction->order_id = $order->id;
        $transaction->mode     = $request->mode;
        $transaction->status   = 'pending';
        $transaction->save();

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }
    public function setAmountForCheckout()
    { 
        if(!Cart::instance('cart')->count() > 0)
        {
            Session::forget('checkout');
            return;
        }    

        // Get shipping price from config
        $shipping_fee = config('cart.shipping', 0);

        if(Session::has('coupon'))
        {
            $discount = Session::get('discounts')['discount'];
            $subtotal = Session::get('discounts')['subtotal'];
            $tax = Session::get('discounts')['tax'];
            $total = Session::get('discounts')['total'] + $shipping_fee;

            Session::put('checkout',[
                'discount' => $discount,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping_fee,
                'total' => $total
            ]);
        }
        else
        {
            $subtotal = str_replace(',', '', Cart::instance('cart')->subtotal());
            $tax = str_replace(',', '', Cart::instance('cart')->tax());
            $total = str_replace(',', '', Cart::instance('cart')->total()) + $shipping_fee;

            Session::put('checkout',[
                'discount' => 0,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping_fee,
                'total' => $total
            ]);
        }
    }
        public function order_confirmation()
        {
            if(Session::has('order_id'))
            {
                $order = Order::find(Session::get('order_id'));
                return view('order-confirmation', compact('order'));
            }
            return redirect()->route('cart.index');
        }
}
    