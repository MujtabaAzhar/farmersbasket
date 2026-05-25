<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use App\Models\Contacts;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AdminNotification;
use App\Services\NotificationService;
use App\Models\Branch;
use App\Models\InventoryLog;
use App\Models\LoginActivityLog;
use App\Models\OrderHistory;
use App\Models\PosSession;
use App\Models\Review;
use App\Models\CourierService;
use App\Models\Rider;
use App\Models\Shipment;
use App\Models\Slide;
use App\Models\StockTransfer;
use App\Models\Transection;
use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        $orders = Order::orderBy('created_at', 'DESC')->limit(10)->get();
        $dashboardDatas = DB::select("Select sum(total) AS TotalAmount,
        sum(if(status = 'ordered',total,0)) AS TotalOrderedAmount,
        sum(if(status = 'delivered',total,0)) AS TotalDeliveredAmount,
        sum(if(status = 'canceled',total,0)) AS TotalCanceledAmount,
        Count(*) As Total,
        sum(if(status = 'ordered',total,0)) AS TotalOrdered,
        sum(if(status = 'delivered',total,0)) AS TotalDelivered,
        sum(if(status = 'canceled',total,0)) AS TotalCanceled
        From Orders");

        $monthlyDatas = DB::select("SELECT M.id As MonthNo, m.name As MonthName,
	IFNULL(D.TotalAmount,0) As TotalAmount,
	IFNULL(D.TotalOrderedAmount,0) As TotalOrderedAmount,
	IFNULL(D.TotalDeliveredAmount,0) As TotalDeliveredAmount,
	IFNULL(D.TotalCanceledAmount,0) As TotalCanceledAmount FROM month_names M
	LEFT JOIN (Select DATE_FORMAT(created_at,'%b') As MonthName,
	MONTH(created_at) As MonthNo,
	sum(total) As TotalAmount,
	sum(if(status='ordered',total, 0)) As TotalOrderedAmount,
	sum(if(status='delivered',total,0)) As TotalDeliveredAmount,
	sum(if(status='canceled',total,0)) As TotalCanceledAmount
	From Orders WHERE YEAR(created_at)=YEAR(NOW()) GROUP BY YEAR(created_at), MONTH(created_at) , DATE_FORMAT(created_at, '%b')
	Order By MONTH(created_at)) D On D.MonthNo=M.id");

    $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
    $orderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
    $deliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
    $canceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());

    $totalAmount = collect($monthlyDatas)->sum('TotalAmount');
    $totalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
    $totalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
    $totalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');


        return view('admin.index', compact('orders', 'dashboardDatas', 'AmountM', 'orderedAmountM', 'deliveredAmountM', 'canceledAmountM', 'totalAmount', 'totalOrderedAmount', 'totalDeliveredAmount', 'totalCanceledAmount'));
    }

    // Brands Start

    // Brands List

    public function brands(){
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    // Add Brand

    public function add_brand(Request $request){
    return view('admin.brand-add');
    }

    // Store Brand

    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand added successfully.');
    }

    // Edit Brand

    public function brand_edit($id)
    {
    $brand = Brand::find($id);
    return view('admin.brand-edit',compact('brand'));
    }

    // Update Brand

    public function brand_update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $brand = Brand::find($id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands/'.$brand->image))){
                File::delete(public_path('uploads/brands/'.$brand->image));
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand updated successfully.');
    }

    // Generate Brand Thumbnails Image

    public function GenerateBrandThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,'top');
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    //Delete Brand

    public function brand_delete($id){
        $brand = Brand::find($id);
        if(File::exists(public_path('uploads/brands/'.$brand->image))){
            File::delete(public_path('uploads/brands/'.$brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand deleted successfully.');
    }

    // Brands End


    // Categories Start

    // Categories List

    public function categories(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    // Add Category    
    public function category_add(){
        return view('admin.category-add');
    }

    // Store Category
    public function category_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category added successfully.');
    }

    // Generate Category Thumbnails Image
    public function GenerateCategoryThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124,124,'top');
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    // Edit Category
    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit',compact('category'));
    }

    // Update Category
    public function category_update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories/'.$category->image))){
                File::delete(public_path('uploads/categories/'.$category->image));
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category updated successfully.');
    }

    // Delete Category
    public function category_delete($id){
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories/'.$category->image))){
            File::delete(public_path('uploads/categories/'.$category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category deleted successfully.');
    }

    // Categories End

    // Products Start
    public function products(){
        $products = Product::with('variants')->orderBy('created_at','DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }


    public function product_add(){
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories','brands'));
    }


    public function product_store(Request $request){
        $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'required|unique:products,slug',
            'short_description' => 'required',
            'description'       => 'required',
            'featured'          => 'required',
            'image'             => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category_id'       => 'required',
            'brand_id'          => 'required',
            'variants'          => 'required|array|min:1',
            'variants.*.variant_name' => 'required|string|max:100',
            'variants.*.price'        => 'required|numeric|min:0',
            'variants.*.stock_qty'    => 'required|integer|min:0',
            'variants.*.unit'         => 'required|string|max:20',
        ]);

        $product = new Product();
        $product->name              = $request->name;
        $product->slug              = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description       = $request->description;
        $product->featured          = $request->featured;
        $product->category_id       = $request->category_id;
        $product->brand_id          = $request->brand_id;
        $product->stock_status      = 'instock';

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $counter = 1;
        if($request->hasFile('images')){
            foreach ($request->file('images') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if(in_array($ext, ['jpeg','jpg','png'])){
                    $gfileName = $current_timestamp.'-'.$counter.'.'.$ext;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    $gallery_arr[] = $gfileName;
                    $counter++;
                }
            }
        }
        $product->images = implode(',', $gallery_arr);
        $product->save();

        // Save variants
        foreach ($request->variants as $v) {
            if (empty($v['variant_name'])) continue;
            ProductVariant::create([
                'product_id'       => $product->id,
                'variant_name'     => $v['variant_name'],
                'weight'           => $v['weight'] ?? null,
                'unit'             => $v['unit'],
                'sku'              => !empty($v['sku']) ? $v['sku'] : null,
                'barcode'          => $v['barcode'] ?? null,
                'price'            => $v['price'],
                'compare_price'    => !empty($v['compare_price']) ? $v['compare_price'] : null,
                'cost_price'       => !empty($v['cost_price']) ? $v['cost_price'] : null,
                'stock_qty'        => $v['stock_qty'],
                'low_stock_alert'  => $v['low_stock_alert'] ?? 5,
                'is_active'        => true,
            ]);
        }

        $product->syncStockStatus();
        
        return redirect()->route('admin.products')->with('status', 'Product added successfully.');
    
    }

    public function GenerateProductThumbnailsImage($image, $imageName){

        $destinationPathThumbnail = public_path('uploads/products/thumbnails');    
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(252,152,'top');
        $img->resize(252,152,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104,104,function($constraint){
        $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function product_edit($id){
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product','categories','brands'));
    }

    public function product_update(Request $request, $id){
        $request->validate([
            'name'              => 'required',
            'slug'              => 'required|unique:products,slug,'.$id,
            'short_description' => 'required',
            'description'       => 'required',
            'featured'          => 'required',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id'       => 'required',
            'brand_id'          => 'required',
            'variants'          => 'required|array|min:1',
            'variants.*.variant_name' => 'required|string|max:100',
            'variants.*.price'        => 'required|numeric|min:0',
            'variants.*.stock_qty'    => 'required|integer|min:0',
            'variants.*.unit'         => 'required|string|max:20',
        ]);

        $product = Product::findOrFail($id);
        $product->name              = $request->name;
        $product->slug              = $request->slug;
        $product->short_description = $request->short_description;
        $product->description       = $request->description;
        $product->featured          = $request->featured;
        $product->category_id       = $request->category_id;
        $product->brand_id          = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if (File::exists(public_path('uploads/products').'/'.$product->image)) {
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)) {
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $old_gallery_arr = $product->images ? explode(',', $product->images) : [];

        if ($request->has('current_images') && !empty($request->current_images)) {
            $current_images = $request->current_images;
            // If it's a JSON string, decode it
            if(is_string($current_images) && (strpos($current_images, '[') === 0 || strpos($current_images, '{') === 0)) {
                $gallery_arr = json_decode($current_images, true);
                if(!is_array($gallery_arr)) {
                    $gallery_arr = [];
                }
            } elseif(is_array($current_images)) {
                $gallery_arr = $current_images;
            } else {
                $gallery_arr = [];
            }
        } else {
            // If no current_images provided, keep existing images
            $gallery_arr = $old_gallery_arr;
        }

        $removed_images = array_diff($old_gallery_arr, $gallery_arr);
        foreach ($removed_images as $rm_img) {
            if (File::exists(public_path('uploads/products').'/'.$rm_img)) {
                File::delete(public_path('uploads/products').'/'.$rm_img);
            }
            if (File::exists(public_path('uploads/products/thumbnails').'/'.$rm_img)) {
                File::delete(public_path('uploads/products/thumbnails').'/'.$rm_img);
            }
        }

        $counter = 1;

        if($request->hasFile('images'))
        {
            $allowedfileExtensions = ['jpeg', 'jpg', 'png'];
            $files = $request->file('images');
            foreach ($files as $file) 
            {
                $gextension = strtolower($file->getClientOriginalExtension());
                $gcheck = in_array($gextension, $allowedfileExtensions);
                if($gcheck)
                {
                    $gfileName = $current_timestamp.'-'.$counter.'.'.$gextension;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
        }
        
        $gallery_images = implode(",",$gallery_arr);
        $product->images = $gallery_images;

        $product->save();

        // Sync variants: keep existing by ID, delete removed, update/create
        $submittedIds = [];
        foreach ($request->variants as $v) {
            if (!empty($v['id'])) {
                $variant = ProductVariant::where('product_id', $product->id)->find($v['id']);
                if ($variant) {
                    $variant->update([
                        'variant_name'    => $v['variant_name'],
                        'weight'          => $v['weight'] ?? null,
                        'unit'            => $v['unit'],
                        'sku'             => !empty($v['sku']) ? $v['sku'] : null,
                        'barcode'         => $v['barcode'] ?? null,
                        'price'           => $v['price'],
                        'compare_price'   => !empty($v['compare_price']) ? $v['compare_price'] : null,
                        'cost_price'      => !empty($v['cost_price']) ? $v['cost_price'] : null,
                        'stock_qty'       => $v['stock_qty'],
                        'low_stock_alert' => $v['low_stock_alert'] ?? 5,
                        'is_active'       => true,
                    ]);
                    $submittedIds[] = $variant->id;
                    continue;
                }
            }
            $newVariant = ProductVariant::create([
                'product_id'      => $product->id,
                'variant_name'    => $v['variant_name'],
                'weight'          => $v['weight'] ?? null,
                'unit'            => $v['unit'],
                'sku'             => !empty($v['sku']) ? $v['sku'] : null,
                'barcode'         => $v['barcode'] ?? null,
                'price'           => $v['price'],
                'compare_price'   => !empty($v['compare_price']) ? $v['compare_price'] : null,
                'cost_price'      => !empty($v['cost_price']) ? $v['cost_price'] : null,
                'stock_qty'       => $v['stock_qty'],
                'low_stock_alert' => $v['low_stock_alert'] ?? 5,
                'is_active'       => true,
            ]);
            $submittedIds[] = $newVariant->id;
        }

        // Delete variants that were removed in the form
        ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', $submittedIds)
            ->delete();

        $product->syncStockStatus();

        return redirect()->route('admin.products')->with('status', 'Product updated successfully.');
    }

    public function product_delete($id){
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products/'.$product->image))){
            File::delete(public_path('uploads/products/'.$product->image));
        }
        if(File::exists(public_path('uploads/products/thumbnails/'.$product->image))){
            File::delete(public_path('uploads/products/thumbnails/'.$product->image));
        }
        $gallery_images = explode(",",$product->images);
        foreach($gallery_images as $img){
            if(File::exists(public_path('uploads/products/'.$img))){
                File::delete(public_path('uploads/products/'.$img));
            }
            if(File::exists(public_path('uploads/products/thumbnails/'.$img))){
                File::delete(public_path('uploads/products/thumbnails/'.$img));
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product deleted successfully.');
    }

    public function product_quantity(){
        $products = Product::with('variants')->orderBy('name')->paginate(15);
        return view('admin.product-quantity', compact('products'));
    }

    public function product_quantity_update(Request $request){
        $request->validate([
            'quantities' => 'required|array'
        ]);

        $products_to_update = [];

        foreach ($request->quantities as $variant_id => $quantity) {
            $variant = ProductVariant::find($variant_id);
            if ($variant) {
                $variant->update(['stock_qty' => max(0, (int) $quantity)]);
                $products_to_update[$variant->product_id] = true;
            }
        }

        foreach (array_keys($products_to_update) as $product_id) {
            $product = Product::find($product_id);
            if ($product) $product->syncStockStatus();
        }

        return back()->with('status', 'Quantities updated successfully.');
    }
    // Products End

    public function coupons(){
        $coupons = Coupon::orderBy('expiry_date','DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function coupon_add(){
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request){
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = strtoupper($request->code);
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon added successfully.');
    }

    public function coupon_edit($id){
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request, $id){
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$id,
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($id);
        $coupon->code = strtoupper($request->code);
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon updated successfully.');
    }
    public function coupon_delete($id){
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon deleted successfully.');
    }
    public function order(Request $request){
        $query = Order::with(['posPayment', 'transaction'])->orderBy('created_at','DESC');

        // Order ID / number (supports "FB-1001" or raw "1001")
        if ($request->filled('order_id')) {
            $raw = trim($request->order_id);
            if (preg_match('/^FB-(\d+)$/i', $raw, $m)) {
                $query->where('id', (int)$m[1] - 1000);
            } elseif (is_numeric($raw)) {
                $query->where('id', (int)$raw);
            }
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%'.$request->phone.'%');
        }

        if ($request->filled('payment_method')) {
            $value     = $request->payment_method;
            $platforms = ['JazzCash','EasyPaisa','Meezan Bank','HBL Bank','Alfalah Bank'];

            if (in_array($value, $platforms)) {
                $query->whereHas('posPayment', fn($pq) => $pq->where('online_platform', $value));
            } else {
                $query->where(function ($q) use ($value) {
                    $q->whereHas('posPayment', fn($pq) => $pq->where('method', $value))
                      ->orWhereHas('transaction', fn($tq) => $tq->where('mode', $value));
                });
            }
        }

        if ($request->filled('courier')) {
            $query->where('courier_name', 'like', '%'.$request->courier.'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id){
        $order = Order::with([
            'histories.creator',
            'cashier',
            'branch',
            'posPayment',
            'transaction',
            'giftOrder',
        ])->findOrFail($order_id);

        $orderItems = OrderItem::with(['product.category', 'product.brand', 'variant'])
            ->where('order_id', $order_id)
            ->orderBy('id')
            ->get();

        $couriers = CourierService::where('is_active', true)
            ->orderByRaw("FIELD(code,'internal','leopards','tcs','mnp')")
            ->get();

        $riders = Rider::where('is_active', true)->with('branch')->orderBy('name')->get();

        $existingShipment = Shipment::where('order_id', $order_id)
            ->whereNotIn('status', ['canceled', 'returned'])
            ->first();

        return view('admin.order-details', compact('order', 'orderItems', 'couriers', 'riders', 'existingShipment'));
    }

    public function order_track($order_id)
    {
        $order = Order::with([
            'histories.creator',
            'orderItems.product',
            'posPayment',
            'transaction',
            'giftOrder',
            'cashier',
            'branch',
        ])->findOrFail($order_id);

        $pm      = $order->posPayment?->online_platform
                ?? $order->posPayment?->method
                ?? $order->transaction?->mode
                ?? null;
        $pmLabel = $pm ? ucwords(str_replace('_', ' ', $pm)) : null;

        return view('admin.order-tracker', compact('order', 'pm', 'pmLabel'));
    }

    public function bulk_update_orders(Request $request)
    {
        $request->validate([
            'order_ids'    => ['required', 'array', 'min:1'],
            'order_ids.*'  => ['integer', 'exists:orders,id'],
            'order_status' => ['required', 'in:ordered,confirmed,packed,shipped,delivered,canceled,returned'],
        ]);

        $status = $request->order_status;
        $now    = Carbon::now();

        foreach ($request->order_ids as $id) {
            $order = Order::find($id);
            if (!$order) continue;

            $order->status = $status;

            if ($status === 'delivered') {
                $order->delivered_date = $now;
                $order->payment_status = 'paid';
            } elseif ($status === 'canceled') {
                $order->canceled_date = $now;
            } elseif ($status === 'returned') {
                $order->payment_status = 'refunded';
            }

            $order->save();

            OrderHistory::create([
                'order_id'      => $order->id,
                'status'        => $status,
                'note'          => 'Bulk status update by admin',
                'created_by'    => Auth::id(),
                'is_admin_note' => true,
            ]);
        }

        $count = count($request->order_ids);
        return back()->with('status', "Updated {$count} order(s) to \"" . ucfirst($status) . '".');
    }

    public function update_order_status(Request $request){
        $request->validate([
            'order_id'                => ['required', 'integer', 'exists:orders,id'],
            'order_status'            => ['required', 'in:ordered,confirmed,packed,shipped,delivered,canceled,returned'],
            'tracking_number'         => ['nullable', 'string', 'max:100'],
            'courier_name'            => ['nullable', 'string', 'max:100'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'admin_note'              => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = $request->order_status;

        if ($request->filled('tracking_number')) {
            $order->tracking_number = $request->tracking_number;
        }
        if ($request->filled('courier_name')) {
            $order->courier_name = $request->courier_name;
        }
        if ($request->filled('estimated_delivery_date')) {
            $order->estimated_delivery_date = $request->estimated_delivery_date;
        }

        if ($request->order_status === 'delivered') {
            $order->delivered_date = Carbon::now();
            $order->payment_status  = 'paid';
        } elseif (in_array($request->order_status, ['canceled', 'returned'])) {
            if ($request->order_status === 'canceled') {
                $order->canceled_date = Carbon::now();
            } else {
                $order->payment_status = 'refunded';
            }
            // Restore stock when admin cancels or accepts a return
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $before = $variant->stock_qty;
                        $variant->stock_qty = $before + $item->quantity;
                        $variant->save();
                        $variant->product->syncStockStatus();
                        InventoryLog::record($item->product_id, 'cancel', $before, $item->quantity, $item->variant_id, null, "Order #{$order->id} {$request->order_status} by admin", Auth::id());
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        InventoryLog::record($product->id, 'cancel', 0, $item->quantity, null, null, "Order #{$order->id} {$request->order_status} by admin", Auth::id());
                    }
                }
            }
        }

        $order->save();

        // Notify customer of status change (skip 'ordered' — that's already sent on placement)
        if (!in_array($request->order_status, ['ordered'])) {
            NotificationService::orderStatusUpdated($order);
        }

        if ($request->order_status === 'delivered') {
            $transection = Transection::where('order_id', $request->order_id)->first();
            if ($transection) {
                $transection->status = 'approved';
                $transection->save();
            }
        }

        OrderHistory::create([
            'order_id'      => $order->id,
            'status'        => $request->order_status,
            'note'          => $request->admin_note ?: null,
            'created_by'    => Auth::id(),
            'is_admin_note' => $request->filled('admin_note'),
        ]);

        return back()->with('status', 'Order status updated successfully.');
    }

    public function slides(){
        $slides = Slide::orderBy('id','DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add(){
        return view('admin.slide-add');
    }

    public function slide_store(Request $request){
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide added successfully.');
    }
    public function GenerateSlideThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400,690,'top');
        $img->resize(400,690,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function slide_edit($id){
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request, $id){
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $slide = Slide::find($id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide updated successfully.');
    }

    public function slide_delete($id){
        $slide = Slide::find($id);
        if(File::exists(public_path('uploads/slides/'.$slide->image))){
            File::delete(public_path('uploads/slides/'.$slide->image));
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('status', 'Slide deleted successfully.');
    }

    public function contacts(){
        $contacts = Contacts::orderBy('created_at','DESC')->paginate(12);
        return view('admin.contacts', compact('contacts'));
    }

    public function contact_delete($id){
        $contact = Contacts::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with('status', 'Message deleted successfully.');
    }

    public function search(Request $request){
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%{$query}%")->limit(8)->get();
        return response()->json($results);
    }

    public function reviews()
    {
        $reviews = Review::with(['product', 'user'])
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        return view('admin.reviews', compact('reviews'));
    }

    public function review_approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();
        return back()->with('status', 'Review approved.');
    }

    public function review_reject($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->save();
        return back()->with('status', 'Review rejected.');
    }

    public function review_delete($id)
    {
        Review::findOrFail($id)->delete();
        return back()->with('status', 'Review deleted.');
    }

    // -------------------------------------------------------------------------
    // Inventory Management
    // -------------------------------------------------------------------------

    const LOW_STOCK_THRESHOLD = 10;

    public function inventory(Request $request)
    {
        $query = ProductVariant::with('product')
            ->whereHas('product')
            ->orderBy('stock_qty', 'asc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('variant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%')
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->filter === 'low') {
            $query->whereRaw('stock_qty > 0 AND stock_qty <= low_stock_alert');
        } elseif ($request->filter === 'out') {
            $query->where('stock_qty', '<=', 0);
        }

        $variants        = $query->paginate(20)->withQueryString();
        $total_variants  = ProductVariant::count();
        $low_stock_count = ProductVariant::whereRaw('stock_qty > 0 AND stock_qty <= low_stock_alert')->count();
        $out_stock_count = ProductVariant::where('stock_qty', '<=', 0)->count();
        $recent_logs     = InventoryLog::with(['product', 'variant', 'creator'])->latest()->limit(15)->get();

        return view('admin.inventory', compact(
            'variants', 'total_variants', 'low_stock_count', 'out_stock_count', 'recent_logs'
        ));
    }

    public function inventory_adjust(Request $request)
    {
        $request->validate([
            'variant_id'      => ['required', 'exists:product_variants,id'],
            'adjustment_type' => ['required', 'in:increase,decrease,set'],
            'quantity'        => ['required', 'integer', 'min:0'],
            'note'            => ['nullable', 'string', 'max:255'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        $before  = $variant->stock_qty;

        if ($request->adjustment_type === 'increase') {
            $change = $request->quantity;
            $after  = $before + $change;
        } elseif ($request->adjustment_type === 'decrease') {
            $change = -min($request->quantity, $before);
            $after  = $before + $change;
        } else {
            $after  = $request->quantity;
            $change = $after - $before;
        }

        $variant->stock_qty = $after;
        $variant->save();
        $variant->product->syncStockStatus();

        InventoryLog::record($variant->product_id, 'adjustment', $before, $change, $variant->id, null, $request->note ?: 'Manual adjustment', Auth::id());

        return back()->with('status', "Stock updated for \"{$variant->product->name} — {$variant->variant_name}\".");
    }

    // -------------------------------------------------------------------------
    // Warehouses
    // -------------------------------------------------------------------------

    public function warehouses()
    {
        $warehouses = Warehouse::withCount('inventories')->orderBy('name')->paginate(15);
        return view('admin.warehouses', compact('warehouses'));
    }

    public function warehouse_add()
    {
        return view('admin.warehouse-add');
    }

    public function warehouse_store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'code'          => ['required', 'string', 'max:20', 'unique:warehouses,code'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'manager_name'  => ['nullable', 'string', 'max:100'],
            'manager_phone' => ['nullable', 'string', 'max:20'],
        ]);

        Warehouse::create(array_merge(
            $request->only(['name', 'code', 'address', 'city', 'manager_name', 'manager_phone']),
            ['is_active' => true]
        ));

        return redirect()->route('admin.warehouses')->with('status', 'Warehouse created successfully.');
    }

    public function warehouse_edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('admin.warehouse-edit', compact('warehouse'));
    }

    public function warehouse_update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'code'          => ['required', 'string', 'max:20', "unique:warehouses,code,{$id}"],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'manager_name'  => ['nullable', 'string', 'max:100'],
            'manager_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $warehouse->update(array_merge(
            $request->only(['name', 'code', 'address', 'city', 'manager_name', 'manager_phone']),
            ['is_active' => $request->boolean('is_active', true)]
        ));

        return redirect()->route('admin.warehouses')->with('status', 'Warehouse updated.');
    }

    public function warehouse_delete($id)
    {
        Warehouse::findOrFail($id)->delete();
        return back()->with('status', 'Warehouse deleted.');
    }

    public function warehouse_inventory($id)
    {
        $warehouse   = Warehouse::findOrFail($id);
        $inventories = WarehouseInventory::with(['product', 'variant'])
            ->where('warehouse_id', $id)
            ->orderBy('quantity', 'asc')
            ->paginate(25);
        $products = Product::with('variants')->orderBy('name')->get(['id', 'name', 'sku']);
        return view('admin.warehouse-inventory', compact('warehouse', 'inventories', 'products'));
    }

    public function warehouse_inventory_adjust(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $request->validate([
            'product_id'      => ['required', 'exists:products,id'],
            'variant_id'      => ['nullable', 'exists:product_variants,id'],
            'adjustment_type' => ['required', 'in:increase,decrease,set'],
            'quantity'        => ['required', 'integer', 'min:0'],
            'note'            => ['nullable', 'string', 'max:255'],
        ]);

        $inv = WarehouseInventory::firstOrNew([
            'warehouse_id' => $warehouse->id,
            'product_id'   => $request->product_id,
            'variant_id'   => $request->variant_id,
        ]);

        $before = $inv->quantity ?? 0;

        if ($request->adjustment_type === 'increase') {
            $inv->quantity = $before + $request->quantity;
        } elseif ($request->adjustment_type === 'decrease') {
            $inv->quantity = max(0, $before - $request->quantity);
        } else {
            $inv->quantity = $request->quantity;
        }

        $inv->save();

        InventoryLog::record($request->product_id, 'adjustment', $before, $inv->quantity - $before, $request->variant_id, $warehouse->id, $request->note ?: "Warehouse stock adjustment", Auth::id());

        return back()->with('status', 'Warehouse stock updated.');
    }

    // -------------------------------------------------------------------------
    // Stock Transfers
    // -------------------------------------------------------------------------

    public function stock_transfers()
    {
        $transfers = StockTransfer::with(['product', 'fromWarehouse', 'toWarehouse', 'creator'])
            ->latest()
            ->paginate(20);
        return view('admin.stock-transfers', compact('transfers'));
    }

    public function stock_transfer_create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products   = Product::with('variants')->orderBy('name')->get(['id', 'name', 'sku']);
        return view('admin.stock-transfer-create', compact('warehouses', 'products'));
    }

    public function stock_transfer_store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'to_warehouse_id'   => ['nullable', 'exists:warehouses,id'],
            'product_id'        => ['required', 'exists:products,id'],
            'variant_id'        => ['nullable', 'exists:product_variants,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
            'note'              => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->from_warehouse_id == $request->to_warehouse_id) {
            return back()->withErrors(['to_warehouse_id' => 'Source and destination must be different.'])->withInput();
        }

        if (!$request->from_warehouse_id && !$request->to_warehouse_id) {
            return back()->withErrors(['to_warehouse_id' => 'At least one warehouse must be specified.'])->withInput();
        }

        StockTransfer::create([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id'   => $request->to_warehouse_id,
            'product_id'        => $request->product_id,
            'variant_id'        => $request->variant_id,
            'quantity'          => $request->quantity,
            'status'            => 'pending',
            'note'              => $request->note,
            'created_by'        => Auth::id(),
        ]);

        return redirect()->route('admin.stock.transfers')->with('status', 'Transfer request created.');
    }

    public function stock_transfer_complete($id)
    {
        $transfer = StockTransfer::with(['product', 'variant'])->findOrFail($id);
        abort_if($transfer->status !== 'pending', 422, 'Only pending transfers can be completed.');

        $product   = $transfer->product;
        $variant   = $transfer->variant;
        $qty       = $transfer->quantity;
        $variantId = $transfer->variant_id;

        // Deduct from source (warehouse inventory or main variant stock)
        if ($transfer->from_warehouse_id) {
            $srcInv = WarehouseInventory::firstOrNew([
                'warehouse_id' => $transfer->from_warehouse_id,
                'product_id'   => $product->id,
                'variant_id'   => $variantId,
            ]);
            $before = $srcInv->quantity ?? 0;
            $srcInv->quantity = max(0, $before - $qty);
            $srcInv->save();
            InventoryLog::record($product->id, 'transfer_out', $before, -$qty, $variantId, $transfer->from_warehouse_id, "Transfer #{$id}", Auth::id());
        } elseif ($variant) {
            $before = $variant->stock_qty;
            $variant->stock_qty = max(0, $before - $qty);
            $variant->save();
            $product->syncStockStatus();
            InventoryLog::record($product->id, 'transfer_out', $before, -$qty, $variantId, null, "Transfer #{$id} to warehouse #{$transfer->to_warehouse_id}", Auth::id());
        }

        // Add to destination (warehouse inventory or main variant stock)
        if ($transfer->to_warehouse_id) {
            $dstInv = WarehouseInventory::firstOrNew([
                'warehouse_id' => $transfer->to_warehouse_id,
                'product_id'   => $product->id,
                'variant_id'   => $variantId,
            ]);
            $before = $dstInv->quantity ?? 0;
            $dstInv->quantity = $before + $qty;
            $dstInv->save();
            InventoryLog::record($product->id, 'transfer_in', $before, $qty, $variantId, $transfer->to_warehouse_id, "Transfer #{$id}", Auth::id());
        } elseif ($variant) {
            $before = $variant->stock_qty;
            $variant->stock_qty = $before + $qty;
            $variant->save();
            $product->syncStockStatus();
            InventoryLog::record($product->id, 'transfer_in', $before, $qty, $variantId, null, "Transfer #{$id} from warehouse #{$transfer->from_warehouse_id}", Auth::id());
        }

        $transfer->status       = 'completed';
        $transfer->completed_at = Carbon::now();
        $transfer->save();

        return back()->with('status', 'Transfer completed successfully.');
    }

    public function stock_transfer_cancel($id)
    {
        $transfer = StockTransfer::findOrFail($id);
        abort_if($transfer->status !== 'pending', 422, 'Only pending transfers can be cancelled.');
        $transfer->status = 'cancelled';
        $transfer->save();
        return back()->with('status', 'Transfer cancelled.');
    }

    // -------------------------------------------------------------------------
    // Branch Management
    // -------------------------------------------------------------------------

    public function branches()
    {
        $branches = Branch::withCount('staff')->orderBy('name')->paginate(15);
        return view('admin.branches', compact('branches'));
    }

    public function branch_add()
    {
        $supervisors = User::where('pos_role', 'pos_supervisor')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.branch-add', compact('supervisors'));
    }

    public function branch_store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'code'       => ['required', 'string', 'max:20', 'unique:branches,code'],
            'address'    => ['nullable', 'string', 'max:255'],
            'city'       => ['nullable', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        Branch::create($request->only(['name', 'code', 'address', 'city', 'phone', 'manager_id']) + ['is_active' => true]);
        return redirect()->route('admin.branches')->with('status', 'Branch created.');
    }

    public function branch_edit($id)
    {
        $branch      = Branch::findOrFail($id);
        $supervisors = User::where('pos_role', 'pos_supervisor')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.branch-edit', compact('branch', 'supervisors'));
    }

    public function branch_update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'code'       => ['required', 'string', 'max:20', "unique:branches,code,{$id}"],
            'address'    => ['nullable', 'string', 'max:255'],
            'city'       => ['nullable', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        $branch->update(array_merge(
            $request->only(['name', 'code', 'address', 'city', 'phone', 'manager_id']),
            ['is_active' => $request->boolean('is_active', true)]
        ));
        return redirect()->route('admin.branches')->with('status', 'Branch updated.');
    }

    public function branch_delete($id)
    {
        Branch::findOrFail($id)->delete();
        return back()->with('status', 'Branch deleted.');
    }

    // -------------------------------------------------------------------------
    // Cashier Management
    // -------------------------------------------------------------------------

    public function cashiers()
    {
        $cashiers = User::whereIn('pos_role', ['pos_supervisor', 'cashier'])
            ->with('branch')
            ->orderBy('name')
            ->paginate(20);
        $branches = Branch::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        return view('admin.cashiers', compact('cashiers', 'branches'));
    }

    public function cashier_store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'mobile'    => ['required', 'string', 'max:20'],
            'password'  => ['required', 'string', 'min:6'],
            'pos_role'  => ['required', 'in:pos_supervisor,cashier'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'password' => bcrypt($request->password),
        ]);
        $user->pos_role  = $request->pos_role;
        $user->branch_id = $request->branch_id;
        $user->save();

        return redirect()->route('admin.cashiers')->with('status', "User \"{$user->name}\" created as {$user->roleBadge()}.");
    }

    public function cashier_update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'pos_role'  => ['required', 'in:pos_supervisor,cashier'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $user->pos_role  = $request->pos_role;
        $user->branch_id = $request->branch_id;
        $user->save();

        return back()->with('status', "{$user->name} updated.");
    }

    public function cashier_revoke($id)
    {
        $user = User::findOrFail($id);
        $user->pos_role  = null;
        $user->branch_id = null;
        $user->save();
        return back()->with('status', "POS access revoked for {$user->name}.");
    }

    // -------------------------------------------------------------------------
    // POS Sessions (admin view)
    // -------------------------------------------------------------------------

    public function pos_sessions()
    {
        $sessions    = PosSession::with(['cashier', 'branch'])->latest()->paginate(20);
        $openCount   = PosSession::where('status', 'open')->count();
        $todayCount  = PosSession::whereDate('opened_at', today())->count();
        $todaySales  = Order::where('source', 'pos')->whereDate('created_at', today())->where('is_hold', false)->sum('total');

        return view('admin.pos-sessions', compact('sessions', 'openCount', 'todayCount', 'todaySales'));
    }

    // -------------------------------------------------------------------------
    // Notifications
    // -------------------------------------------------------------------------

    public function notifications_fetch()
    {
        $notifications = AdminNotification::latest()->limit(15)->get()->map(function ($n) {
            return [
                'id'       => $n->id,
                'type'     => $n->type,
                'title'    => $n->title,
                'message'  => $n->message,
                'url'      => $n->url,
                'is_read'  => $n->is_read,
                'time_ago' => $n->time_ago,
            ];
        });

        return response()->json([
            'unread_count'  => AdminNotification::where('is_read', false)->count(),
            'notifications' => $notifications,
        ]);
    }

    public function notifications_page()
    {
        $notifications = AdminNotification::latest()->paginate(30);
        return view('admin.notifications', compact('notifications'));
    }

    public function notifications_read_all()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function notifications_mark_read($id)
    {
        AdminNotification::where('id', $id)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------

    public function settings()
    {
        return view('admin.settings', ['admin' => Auth::user()]);
    }

    public function settings_profile(Request $request)
    {
        $admin = Auth::user();
        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', 'max:191', 'unique:users,email,'.$admin->id],
            'mobile' => ['nullable', 'string', 'max:20', 'unique:users,mobile,'.$admin->id],
        ]);

        $admin->name   = $request->name;
        $admin->email  = $request->email;
        $admin->mobile = $request->mobile;
        $admin->save();

        return back()->with('profile_success', 'Profile updated successfully.');
    }

    public function settings_password(Request $request)
    {
        $request->validate([
            'current_password'      => ['required'],
            'new_password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!\Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        Auth::user()->update(['password' => \Hash::make($request->new_password)]);

        return back()->with('password_success', 'Password changed successfully.');
    }

    // -------------------------------------------------------------------------
    // Customers
    // -------------------------------------------------------------------------

    public function customers(Request $request)
    {
        $query = User::where('utype', 'USR')
            ->whereNull('pos_role')
            ->withCount('orders as order_count')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', 'delivered');
            }], 'total')
            ->with(['orders' => function ($q) {
                $q->latest()->limit(1);
            }]);

        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('mobile', 'like', $s);
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('admin.customers', compact('customers'));
    }

    public function customer_detail($id)
    {
        $customer  = User::where('utype', 'USR')->whereNull('pos_role')->findOrFail($id);
        $orders    = Order::where('user_id', $id)->latest()->get();
        $addresses = $customer->customerAddresses;

        $totalSpent = $orders->where('status', 'delivered')->sum('total');
        $orderCount = $orders->count();

        return view('admin.customer-detail', compact('customer', 'orders', 'addresses', 'totalSpent', 'orderCount'));
    }

    // -------------------------------------------------------------------------
    // Login Activity
    // -------------------------------------------------------------------------

    public function login_activity(Request $request)
    {
        $query = LoginActivityLog::with('user')->latest('created_at');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.login-activity', compact('logs'));
    }

}