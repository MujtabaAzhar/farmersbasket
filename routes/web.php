<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\AuthAdmin;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
// POS routes — Cashiers and Supervisors (and Super Admin)
Route::middleware(['auth', 'auth.pos'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/products/search', [PosController::class, 'product_search'])->name('pos.products.search');
    Route::post('/pos/cart/add', [PosController::class, 'cart_add'])->name('pos.cart.add');
    Route::put('/pos/cart/update/{rowId}', [PosController::class, 'cart_update'])->name('pos.cart.update');
    Route::delete('/pos/cart/remove/{rowId}', [PosController::class, 'cart_remove'])->name('pos.cart.remove');
    Route::delete('/pos/cart/clear', [PosController::class, 'cart_clear'])->name('pos.cart.clear');
    Route::get('/pos/customer/search', [PosController::class, 'customer_search'])->name('pos.customer.search');
    Route::post('/pos/customer/create', [PosController::class, 'customer_create'])->name('pos.customer.create');
    Route::get('/pos/customer/lookup', [PosController::class, 'customer_lookup'])->name('pos.customer.lookup');
    Route::post('/pos/customer/address/save', [PosController::class, 'address_save'])->name('pos.customer.address.save');
    Route::post('/pos/hold', [PosController::class, 'hold_order'])->name('pos.hold');
    Route::get('/pos/held', [PosController::class, 'held_orders'])->name('pos.held');
    Route::post('/pos/held/{id}/resume', [PosController::class, 'resume_order'])->name('pos.resume');
    Route::get('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/order/place', [PosController::class, 'place_order'])->name('pos.order.place');
    Route::get('/pos/receipt/{id}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/sessions', [PosController::class, 'sessions'])->name('pos.sessions');
    Route::post('/pos/session/open', [PosController::class, 'session_open'])->name('pos.session.open');
    Route::post('/pos/session/close', [PosController::class, 'session_close'])->name('pos.session.close');
});

// POS Supervisor-only routes
Route::middleware(['auth', 'auth.supervisor'])->group(function () {
    Route::get('/pos/supervisor', [PosController::class, 'supervisor_dashboard'])->name('pos.supervisor');
});
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
Route::get('/api/product/{product_id}', [ShopController::class, 'get_product_data'])->name('api.product.data');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/remove-product/{product_id}', [WishlistController::class, 'remove_by_product_id'])->name('wishlist.remove.product');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.empty');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move_to_cart');
Route::post('/wishlist/move-to-cart-product/{product_id}', [WishlistController::class, 'move_to_cart_by_product_id'])->name('wishlist.move_to_cart.product');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-an-order',[CartController::class,'place_an_order'])->name('cart.place.an.order');
Route::get('/order-confirmation',[CartController::class,'order_confirmation'])->name('cart.order.confirmation');
Route::get('/contact-us', [HomeController::class, 'contact'])->name('home.contact');
Route::post('/contact-us', [HomeController::class, 'contact_store'])->name('home.contact.store');
Route::get('/search', [HomeController::class, 'search'])->name('home.search');
Route::post('/contact-us', [HomeController::class, 'contact_store'])->name('home.contact.store');
Route::get('/order-tracking', [App\Http\Controllers\ShipmentTrackingController::class, 'index'])->name('home.order.tracking');
Route::get('/about', [HomeController::class, 'about'])->name('home.about');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');

    // Reviews
    Route::post('/product/{product_id}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/review/{review_id}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

Route::middleware(['auth',AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Brands Start
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update/{id}', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brand/delete/{id}', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');
    // Brands End

    // Categories Start
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update/{id}', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/delete/{id}', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    // Category End

    // Products Start
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update/{id}', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/delete/{id}', [AdminController::class, 'product_delete'])->name('admin.product.delete');
    Route::get('/admin/product/quantity', [AdminController::class, 'product_quantity'])->name('admin.product.quantity');
    Route::post('/admin/product/quantity/update', [AdminController::class, 'product_quantity_update'])->name('admin.product.quantity.update');

    // Products End
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/edit/{id}', [AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/update/{id}', [AdminController::class, 'coupon_update'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/delete/{id}', [AdminController::class, 'coupon_delete'])->name('admin.coupon.delete');
    Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.apply_coupon');
    Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon'])->name('cart.remove_coupon');
    Route::get('/admin/orders', [AdminController::class, 'order'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order.details');
    Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');
    Route::post('/admin/orders/bulk-status', [AdminController::class, 'bulk_update_orders'])->name('admin.orders.bulk.status');
    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
    Route::get('/admin/slide/edit/{id}', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update/{id}', [AdminController::class, 'slide_update'])->name('admin.slide.update');
    Route::delete('/admin/slide/delete/{id}', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');
    Route::get('/admin/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::delete('/admin/contact/delete/{id}', [AdminController::class, 'contact_delete'])->name('admin.contact.delete');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    // Review moderation
    Route::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::put('/admin/review/{id}/approve', [AdminController::class, 'review_approve'])->name('admin.review.approve');
    Route::put('/admin/review/{id}/reject', [AdminController::class, 'review_reject'])->name('admin.review.reject');
    Route::delete('/admin/review/{id}/delete', [AdminController::class, 'review_delete'])->name('admin.review.delete');

    // Inventory
    Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::post('/admin/inventory/adjust', [AdminController::class, 'inventory_adjust'])->name('admin.inventory.adjust');

    // Warehouses
    Route::get('/admin/warehouses', [AdminController::class, 'warehouses'])->name('admin.warehouses');
    Route::get('/admin/warehouse/add', [AdminController::class, 'warehouse_add'])->name('admin.warehouse.add');
    Route::post('/admin/warehouse/store', [AdminController::class, 'warehouse_store'])->name('admin.warehouse.store');
    Route::get('/admin/warehouse/edit/{id}', [AdminController::class, 'warehouse_edit'])->name('admin.warehouse.edit');
    Route::put('/admin/warehouse/update/{id}', [AdminController::class, 'warehouse_update'])->name('admin.warehouse.update');
    Route::delete('/admin/warehouse/delete/{id}', [AdminController::class, 'warehouse_delete'])->name('admin.warehouse.delete');
    Route::get('/admin/warehouse/{id}/inventory', [AdminController::class, 'warehouse_inventory'])->name('admin.warehouse.inventory');
    Route::post('/admin/warehouse/{id}/inventory/adjust', [AdminController::class, 'warehouse_inventory_adjust'])->name('admin.warehouse.inventory.adjust');

    // Branches
    Route::get('/admin/branches', [AdminController::class, 'branches'])->name('admin.branches');
    Route::get('/admin/branch/add', [AdminController::class, 'branch_add'])->name('admin.branch.add');
    Route::post('/admin/branch/store', [AdminController::class, 'branch_store'])->name('admin.branch.store');
    Route::get('/admin/branch/edit/{id}', [AdminController::class, 'branch_edit'])->name('admin.branch.edit');
    Route::put('/admin/branch/update/{id}', [AdminController::class, 'branch_update'])->name('admin.branch.update');
    Route::delete('/admin/branch/delete/{id}', [AdminController::class, 'branch_delete'])->name('admin.branch.delete');

    // Cashiers
    Route::get('/admin/cashiers', [AdminController::class, 'cashiers'])->name('admin.cashiers');
    Route::post('/admin/cashier/store', [AdminController::class, 'cashier_store'])->name('admin.cashier.store');
    Route::put('/admin/cashier/update/{id}', [AdminController::class, 'cashier_update'])->name('admin.cashier.update');
    Route::put('/admin/cashier/revoke/{id}', [AdminController::class, 'cashier_revoke'])->name('admin.cashier.revoke');

    // Customers
    Route::get('/admin/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::get('/admin/customer/{id}', [AdminController::class, 'customer_detail'])->name('admin.customer.detail');

    // Notifications
    Route::get('/admin/notifications/fetch', [AdminController::class, 'notifications_fetch'])->name('admin.notifications.fetch');
    Route::get('/admin/notifications', [AdminController::class, 'notifications_page'])->name('admin.notifications.page');
    Route::post('/admin/notifications/read-all', [AdminController::class, 'notifications_read_all'])->name('admin.notifications.read.all');
    Route::post('/admin/notifications/{id}/read', [AdminController::class, 'notifications_mark_read'])->name('admin.notifications.read');

    // Settings
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings/profile', [AdminController::class, 'settings_profile'])->name('admin.settings.profile');
    Route::post('/admin/settings/password', [AdminController::class, 'settings_password'])->name('admin.settings.password');

    // POS Sessions & Login Activity
    Route::get('/admin/pos-sessions', [AdminController::class, 'pos_sessions'])->name('admin.pos.sessions');
    Route::get('/admin/login-activity', [AdminController::class, 'login_activity'])->name('admin.login.activity');

    // Stock Transfers
    Route::get('/admin/stock-transfers', [AdminController::class, 'stock_transfers'])->name('admin.stock.transfers');
    Route::get('/admin/stock-transfer/create', [AdminController::class, 'stock_transfer_create'])->name('admin.stock.transfer.create');
    Route::post('/admin/stock-transfer/store', [AdminController::class, 'stock_transfer_store'])->name('admin.stock.transfer.store');
    Route::put('/admin/stock-transfer/{id}/complete', [AdminController::class, 'stock_transfer_complete'])->name('admin.stock.transfer.complete');
    Route::put('/admin/stock-transfer/{id}/cancel', [AdminController::class, 'stock_transfer_cancel'])->name('admin.stock.transfer.cancel');

    // Shipments
    Route::get('/admin/shipments', [App\Http\Controllers\Admin\ShipmentController::class, 'index'])->name('admin.shipments.index');
    Route::get('/admin/shipments/create', [App\Http\Controllers\Admin\ShipmentController::class, 'create'])->name('admin.shipments.create');
    Route::post('/admin/shipments', [App\Http\Controllers\Admin\ShipmentController::class, 'store'])->name('admin.shipments.store');
    Route::get('/admin/shipments/{shipment}', [App\Http\Controllers\Admin\ShipmentController::class, 'show'])->name('admin.shipments.show');
    Route::put('/admin/shipments/{shipment}/status', [App\Http\Controllers\Admin\ShipmentController::class, 'updateStatus'])->name('admin.shipments.status');
    Route::post('/admin/shipments/{shipment}/refresh', [App\Http\Controllers\Admin\ShipmentController::class, 'refresh'])->name('admin.shipments.refresh');
    Route::delete('/admin/shipments/{shipment}', [App\Http\Controllers\Admin\ShipmentController::class, 'destroy'])->name('admin.shipments.destroy');

    // Courier Services
    Route::get('/admin/couriers', [App\Http\Controllers\Admin\CourierServiceController::class, 'index'])->name('admin.couriers.index');
    Route::put('/admin/couriers/{courier}', [App\Http\Controllers\Admin\CourierServiceController::class, 'update'])->name('admin.couriers.update');

    // Dispatch board
    Route::get('/admin/dispatch', [App\Http\Controllers\Admin\DispatchController::class, 'index'])->name('admin.dispatch.index');
    Route::post('/admin/dispatch/{shipment}/quick-update', [App\Http\Controllers\Admin\DispatchController::class, 'quickUpdate'])->name('admin.dispatch.quick');

    // Riders
    Route::get('/admin/riders', [App\Http\Controllers\Admin\RiderController::class, 'index'])->name('admin.riders.index');
    Route::post('/admin/riders', [App\Http\Controllers\Admin\RiderController::class, 'store'])->name('admin.riders.store');
    Route::put('/admin/riders/{rider}', [App\Http\Controllers\Admin\RiderController::class, 'update'])->name('admin.riders.update');
    Route::delete('/admin/riders/{rider}', [App\Http\Controllers\Admin\RiderController::class, 'destroy'])->name('admin.riders.destroy');
});