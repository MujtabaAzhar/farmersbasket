<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\OrderItem;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(Request $request, $product_id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        Product::findOrFail($product_id);

        // Only verified buyers (with a non-canceled order) may review
        $orderItem = OrderItem::whereHas('order', function ($q) use ($product_id) {
                $q->where('user_id', Auth::id())->where('status', '!=', 'canceled');
            })
            ->where('product_id', $product_id)
            ->whereDoesntHave('review')
            ->first();

        if (!$orderItem) {
            return back()->with('error', 'You can only review products you have purchased, and only once per purchase.');
        }

        Review::create([
            'product_id'    => $product_id,
            'user_id'       => Auth::id(),
            'order_item_id' => $orderItem->id,
            'rating'        => $request->rating,
            'title'         => $request->title,
            'comment'       => $request->comment,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Review submitted. It will appear after moderation.');
    }

    public function destroy($review_id)
    {
        $review = Review::findOrFail($review_id);
        abort_if($review->user_id !== Auth::id(), 403);
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
