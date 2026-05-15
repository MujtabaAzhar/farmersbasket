<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contacts;

class HomeController extends Controller
{
   
    public function index()
    {
        $slides = Slide::where('status', 1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $categories_2 = Category::orderBy('name')->get()->take(2);
        $sproducts = Product::whereNotNull('sale_price')->where('sale_price', '<>','')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured', 1)->get()->take(8);
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
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'email' => 'required|email|max:255',
            'comment' => 'required|string',
        ]);

        $contact = new Contacts();
        $contact->name = $request->name;
        $contact->mobile = $request->phone;
        $contact->email = $request->email;
        $contact->message = $request->comment;
        $contact->save();

        return redirect()->route('home.contact')->with('success', 'Your message has been sent successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%{$query}%")->get()->take(8);
        return response()->json($results);
    }
}
