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
            $items = WishlistItem::with(['product.variants', 'product.category'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
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
            return redirect()->back()->with('success', 'Added to wishlist!');
        }

        // Guest: session-based
        Cart::instance('wishlist')
            ->add($request->id, $request->name, $request->quantity, $request->price)
            ->associate('App\Models\Product');
        return redirect()->back()->with('success', 'Added to wishlist!');
    }

    // Guest session-based remove (rowId)
    public function remove_item($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back()->with('success', 'Removed from wishlist.');
    }

    // Auth DB-based remove (product_id)
    public function remove_by_product_id($product_id)
    {
        if (Auth::check()) {
            WishlistItem::where('user_id', Auth::id())
                ->where('product_id', $product_id)
                ->delete();
        } else {
            $item = Cart::instance('wishlist')->content()->where('id', $product_id)->first();
            if ($item) {
                Cart::instance('wishlist')->remove($item->rowId);
            }
        }
        return redirect()->back()->with('success', 'Removed from wishlist.');
    }

    public function empty_wishlist()
    {
        if (Auth::check()) {
            WishlistItem::where('user_id', Auth::id())->delete();
        } else {
            Cart::instance('wishlist')->destroy();
        }
        return redirect()->back()->with('success', 'Wishlist cleared.');
    }

    // Move to cart by product_id (auth + guest)
    public function move_to_cart_by_product_id($product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);

        if ($product->stock_status === 'outofstock') {
            return redirect()->back()->with('error', '"' . $product->name . '" is currently out of stock and cannot be added to cart.');
        }

        // Redirect to product page so user can pick a variant
        return redirect()->route('shop.product.details', $product->slug)
            ->with('info', 'Please select a variant and quantity to add to your cart.');
    }

    // Legacy guest rowId move
    public function move_to_cart($rowId)
    {
        $item    = Cart::instance('wishlist')->get($rowId);
        $product = Product::find($item->id);

        if ($product && $product->stock_status === 'outofstock') {
            return redirect()->back()->with('error', '"' . $item->name . '" is currently out of stock.');
        }

        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)
            ->associate('App\Models\Product');
        return redirect()->back()->with('success', 'Moved to cart!');
    }

    // AJAX toggle for auth users
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Login required'], 401);
        }

        $product_id = $request->product_id;
        $existing   = WishlistItem::where('user_id', Auth::id())
                        ->where('product_id', $product_id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['wishlisted' => false]);
        }

        WishlistItem::create(['user_id' => Auth::id(), 'product_id' => $product_id]);
        return response()->json(['wishlisted' => true]);
    }
}
