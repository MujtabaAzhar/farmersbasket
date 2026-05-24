<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contacts;

class HomeController extends Controller
{
   
    public function index()
    {
        $slides = Slide::where('status', 1)->limit(3)->get();
        $categories = Category::orderBy('name')->get();
        $categories_2 = Category::orderBy('name')->limit(2)->get();
        $sproducts = Product::with('variants')->whereHas('variants', fn($q) => $q->whereNotNull('compare_price'))->inRandomOrder()->limit(8)->get();
        $fproducts = Product::with('variants')->where('featured', 1)->limit(8)->get();
        return view('index', compact('slides', 'categories', 'categories_2', 'sproducts', 'fproducts'));
    }

    public function contact()
    {
        return view('contact');
    }
    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{11}$/',
            'email' => 'required|email|max:255',
            'comment' => 'required|string',
        ]);

        $contact = new Contacts();
        $contact->name = $request->name;
        $contact->mobile = $request->phone;
        $contact->email = $request->email;
        $contact->message = $request->comment;
        $contact->save();

        AdminNotification::notify(
            'new_contact',
            'New Contact Message',
            $request->name . ' (' . $request->email . ') sent a message.',
            route('admin.contacts')
        );

        return redirect()->route('home.contact')->with('success', 'Your message has been sent successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%{$query}%")->limit(8)->get();
        return response()->json($results);
    }
     public function orderTracking()
    {
        return view('orderTracking');
    }
     public function about()
    {
        return view('about');
    }
}
