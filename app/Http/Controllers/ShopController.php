<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\WishlistItem;
use App\Models\OrderItem;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        [
            'products' => $products, 'size' => $size, 'order' => $order,
            'brands' => $brands, 'f_brands' => $f_brands,
            'categories' => $categories, 'f_categories' => $f_categories,
            'min_price' => $min_price, 'max_price' => $max_price,
            'wishlisted_ids' => $wishlisted_ids,
        ] = $this->filteredProducts($request);

        return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price', 'wishlisted_ids'));
    }

    public function pos_index(Request $request)
    {
        [
            'products' => $products, 'size' => $size, 'order' => $order,
            'brands' => $brands, 'f_brands' => $f_brands,
            'categories' => $categories, 'f_categories' => $f_categories,
            'min_price' => $min_price, 'max_price' => $max_price,
            'wishlisted_ids' => $wishlisted_ids,
        ] = $this->filteredProducts($request);

        return view('pos', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price', 'wishlisted_ids'));
    }

    public function product_details($product_slug)
    {
        $product = Product::with(['variants', 'approvedReviews.user'])->where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)->limit(8)->get();

        $wishlisted = false;
        $can_review = false;

        if (Auth::check()) {
            $wishlisted = WishlistItem::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();

            $can_review = OrderItem::whereHas('order', function ($q) {
                    $q->where('user_id', Auth::id())->where('status', '!=', 'canceled');
                })
                ->where('product_id', $product->id)
                ->whereDoesntHave('review')
                ->exists();
        }

        return view('details', compact('product', 'rproducts', 'wishlisted', 'can_review'));
    }

    private function filteredProducts(Request $request): array
    {
        $size        = $request->query('size', 12);
        $order       = $request->query('order', -1);
        $f_brands    = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price   = $request->query('min', 1);
        $max_price   = $request->query('max', 10000);

        $brands     = Brand::withCount('products')->orderBy('name', 'ASC')->get();
        $categories = Category::withCount('products')->orderBy('name', 'ASC')->get();

        if (Auth::check()) {
            $wishlisted_ids = WishlistItem::where('user_id', Auth::id())->pluck('product_id')->toArray();
        } else {
            $wishlisted_ids = Cart::instance('wishlist')->content()->pluck('id')->toArray();
        }

        $query = Product::with(['variants'])
            ->when($f_brands, fn($q) => $q->whereIn('brand_id', explode(',', $f_brands)))
            ->when($f_categories, fn($q) => $q->whereIn('category_id', explode(',', $f_categories)))
            ->whereHas('variants', fn($q) => $q->whereBetween('price', [$min_price, $max_price]));

        // Sort by min variant price for price sorts, otherwise by product column
        if ($order == 3) {
            $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC');
        } elseif ($order == 4) {
            $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC');
        } elseif ($order == 1) {
            $query->orderBy('created_at', 'DESC');
        } elseif ($order == 2) {
            $query->orderBy('created_at', 'ASC');
        } else {
            $query->orderBy('id', 'DESC');
        }

        $products = $query->paginate($size);

        return compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price', 'wishlisted_ids');
    }

    public function get_product_data($product_id)
    {
        $product = Product::with('variants')->find($product_id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id'                => $product->id,
            'name'              => $product->name,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'image'             => $product->image,
            'images'            => $product->images,
            'slug'              => $product->slug,
            'variants'          => $product->variants->where('is_active', true)->map(fn($v) => [
                'id'            => $v->id,
                'variant_name'  => $v->variant_name,
                'display_label' => $v->display_label,
                'weight'        => $v->weight,
                'unit'          => $v->unit,
                'sku'           => $v->sku,
                'barcode'       => $v->barcode,
                'price'         => $v->price,
                'compare_price' => $v->compare_price,
                'stock_qty'     => $v->stock_qty,
                'is_in_stock'   => $v->isInStock(),
            ])->values(),
        ]);
    }

}
