<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 10000;
        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_order = "DESC";
                break;
            default:
                $o_column = "id";
                $o_order = "DESC";
        }

        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'".$f_brands."' = ''");    
        })->where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'".$f_categories."' = ''");    
        })
        ->where(function ($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])->orWhereBetween('sale_price', [$min_price, $max_price]);
        })
            ->orderBy($o_column, $o_order)->paginate($size);
        return view('shop', compact('products' , 'size', 'order', 'brands', 'f_brands', 'categories' , 'f_categories', 'min_price', 'max_price'));
    }

       public function pos_index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 10000;
        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_order = "DESC";
                break;
            default:
                $o_column = "id";
                $o_order = "DESC";
        }

        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'".$f_brands."' = ''");    
        })->where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'".$f_categories."' = ''");    
        })
        ->where(function ($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])->orWhereBetween('sale_price', [$min_price, $max_price]);
        })
            ->orderBy($o_column, $o_order)->paginate($size);
        return view('pos', compact('products' , 'size', 'order', 'brands', 'f_brands', 'categories' , 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug){
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug','<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'rproducts'));
    }

    public function get_product_data($product_id)
    {
        $product = Product::with('sizes')->find($product_id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'image' => $product->image,
            'images' => $product->images,
            'regular_price' => $product->regular_price,
            'sale_price' => $product->sale_price,
            'slug' => $product->slug,
            'sizes' => $product->sizes->map(function ($size) {
                return [
                    'id' => $size->id,
                    'size_label' => $size->size_label,
                    'size_value' => $size->size_value,
                    'unit' => $size->unit,
                    'quantity' => $size->quantity,
                    'regular_price' => $size->regular_price,
                    'sale_price' => $size->sale_price,
                ];
            })
        ]);
    }

}
