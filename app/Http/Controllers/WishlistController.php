<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\WishlistItem;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $items = WishlistItem::with('product')->where('user_id', Auth::id())->get();
        } else {
            $items = Cart::instance('wishlist')->content();
        }
        return view('wishlist', compact('items'));
    }

    public function add_to_wishlist(Request $request)
    {
        if (Auth::check()) {
            WishlistItem::firstOrCreate([
                'user_id'    => Auth::id(),
                'product_id' => $request->id,
            ]);
        } else {
            Cart::instance('wishlist')
                ->add($request->id, $request->name, $request->quantity, $request->price)
                ->associate('App\Models\Product');
        }
        return redirect()->back();
    }

    // Used by guest session-based remove (rowId from cart)
    public function remove_item($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    // Used by auth DB-based remove (product_id)
    public function remove_by_product_id($product_id)
    {
        if (Auth::check()) {
            WishlistItem::where('user_id', Auth::id())->where('product_id', $product_id)->delete();
        } else {
            $item = Cart::instance('wishlist')->content()->where('id', $product_id)->first();
            if ($item) {
                Cart::instance('wishlist')->remove($item->rowId);
            }
        }
        return redirect()->back();
    }

    public function empty_wishlist()
    {
        if (Auth::check()) {
            WishlistItem::where('user_id', Auth::id())->delete();
        } else {
            Cart::instance('wishlist')->destroy();
        }
        return redirect()->back();
    }

    // Guest: move session item to cart. Auth: move DB item to cart.
    public function move_to_cart_by_product_id($product_id)
    {
        $product = Product::findOrFail($product_id);

        // Redirect to product details so the user can select a variant
        return redirect()->route('shop.product.details', $product->slug)
            ->with('info', 'Please select a variant to add to your cart.');

        if (Auth::check()) {
            WishlistItem::where('user_id', Auth::id())->where('product_id', $product_id)->delete();
        } else {
            $item = Cart::instance('wishlist')->content()->where('id', $product_id)->first();
            if ($item) {
                Cart::instance('wishlist')->remove($item->rowId);
            }
        }

        return redirect()->back();
    }

    // Legacy: move session item by rowId (kept for existing guest flow)
    public function move_to_cart($rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    // AJAX toggle for auth users
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Login required'], 401);
        }

        $product_id = $request->product_id;
        $existing = WishlistItem::where('user_id', Auth::id())->where('product_id', $product_id)->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            WishlistItem::create(['user_id' => Auth::id(), 'product_id' => $product_id]);
            $wishlisted = true;
        }

        return response()->json(['wishlisted' => $wishlisted]);
    }
}
