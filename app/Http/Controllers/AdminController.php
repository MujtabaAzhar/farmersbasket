<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Contacts;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Slide;
use App\Models\Transection;
use Carbon\Carbon;

use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        $orders = Order::orderBy('created_at','DESC')->get()->take(10);
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

    $products = Product::orderBy('created_at','DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }


    public function product_add(){
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories','brands'));
    }


    public function product_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        
        // Auto-generate SKU: PROD-{timestamp}-{random}
        $product->SKU = 'PROD-' . time() . '-' . strtoupper(Str::random(4));
        
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = 0; // Will be calculated from sizes
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }
        
        
        $gallery_arr = array();
        $gallery_images = "";
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

        // Handle product sizes
        if($request->has('size_values') && is_array($request->size_values)){
            $size_values = $request->size_values;
            $size_quantities = $request->size_quantities ?? [];
            $size_regular_prices = $request->size_regular_prices ?? [];
            $size_sale_prices = $request->size_sale_prices ?? [];
            $unit = $request->unit ?? 'KG';
            
            $total_quantity = 0;
            foreach($size_values as $key => $size_value){
                if(!empty($size_value)){
                    $quantity = isset($size_quantities[$key]) ? $size_quantities[$key] : 0;
                    $regular_price = isset($size_regular_prices[$key]) ? $size_regular_prices[$key] : $product->regular_price;
                    $sale_price = isset($size_sale_prices[$key]) ? $size_sale_prices[$key] : $product->sale_price;
                    
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_label' => $size_value . ' ' . $unit,
                        'size_value' => $size_value,
                        'unit' => $unit,
                        'quantity' => $quantity,
                        'regular_price' => $regular_price,
                        'sale_price' => $sale_price
                    ]);
                    $total_quantity += $quantity;
                }
            }
            
            // Update product total quantity
            $product->quantity = $total_quantity;
            $product->save();
        }
        
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
        $product = Product::find($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product','categories','brands'));
    }

    public function product_update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

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

        // Update size variants if provided
        if($request->has('size_values') && is_array($request->size_values)){
            // Delete existing sizes
            ProductSize::where('product_id', $product->id)->delete();

            $unit = $request->unit ?? 'KG';
            $total_quantity = 0;

            foreach($request->size_values as $index => $size_value){
                if($size_value && isset($request->size_quantities[$index])){
                    $quantity = intval($request->size_quantities[$index]);
                    $regular_price = isset($request->size_regular_prices[$index]) ? $request->size_regular_prices[$index] : $product->regular_price;
                    $sale_price = isset($request->size_sale_prices[$index]) ? $request->size_sale_prices[$index] : $product->sale_price;
                    
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_label' => $size_value . ' ' . $unit,
                        'size_value' => $size_value,
                        'unit' => $unit,
                        'quantity' => $quantity,
                        'regular_price' => $regular_price,
                        'sale_price' => $sale_price
                    ]);
                    $total_quantity += $quantity;
                }
            }

            // Update product quantity to sum of all sizes
            $product->quantity = $total_quantity;
            $product->save();
        }

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
        $products = Product::with('sizes')->orderBy('name')->paginate(15);
        return view('admin.product-quantity', compact('products'));
    }

    public function product_quantity_update(Request $request){
        $request->validate([
            'quantities' => 'required|array'
        ]);

        $quantities = $request->quantities;
        $products_to_update = [];

        foreach($quantities as $size_id => $quantity){
            $size = ProductSize::find($size_id);
            if($size) {
                $size->update(['quantity' => $quantity]);
                $products_to_update[$size->product_id] = true;
            }
        }

        // Recalculate total quantity for each affected product
        foreach(array_keys($products_to_update) as $product_id) {
            $total_qty = ProductSize::where('product_id', $product_id)->sum('quantity');
            Product::where('id', $product_id)->update(['quantity' => $total_qty]);
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
    public function order(){
        $orders = Order::orderBy('created_at','DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id){
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transection = Transection::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transection'));
    }

    public function update_order_status(Request $request){
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;

        if($request->order_status == 'delivered'){
            $order->delivered_date = Carbon::now();
        }
        else if($request->order_status == 'canceled'){
            $order->canceled_date = Carbon::now();
        }

        $order->save();
        if($request->order_status == 'delivered'){
            $transection = Transection::where('order_id', $request->order_id)->first();
            $transection->status = 'approved';
            $transection->save();
        }

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
        $results = Product::where('name', 'LIKE', "%{$query}%")->get()->take(8);
        return response()->json($results);
    }

}