<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Like;
use App\Models\Evaluation;
use App\Models\Condition;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\SoldProduct;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $page = $request->query('page');
        $keyword = $request->query('keyword');

        if (!empty($keyword)) {
        $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($page === 'mylist' && Auth::check()) {
        $likedProductIds = Like::where('user_id', Auth::id())->pluck('product_id');
            $query->whereIn('id', $likedProductIds);
            $query->where('user_id', '!=', Auth::id());
        }

        $products = $query->get();

        $soldProductIds = DB::table('sold_products')->pluck('product_id')->toArray();

        return view('index', compact('products', 'page', 'soldProductIds', 'keyword'));
    }

    public function showDetail($id)
    {
        $product = Product::find($id);
        $condition = $product->condition;
        $categories = $product->categories;
        $likesCount = $product->likes->count();
        $evaluations = $product->evaluations;
        $comments = $product->evaluations;
        $commentsCount = $comments->count();

        $isLiked = false;
        if (Auth::check()) {
            $isLiked = $product->likes->contains('user_id', Auth::id());
        }

        return view('detail', compact('product', 'condition', 'categories', 'likesCount', 'comments', 'commentsCount', 'isLiked'));
    }

    public function like($id)
    {
        $product = Product::findOrFail($id);

        $like = Like::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if (!$like) {
            Like::firstOrCreate(
                ['user_id' => Auth::id(), 'product_id' => $product->id],
                ['user_id' => Auth::id(), 'product_id' => $product->id]
            );
        }

        return back();
    }


    public function unlike($id)
    {
        Like::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->delete();
        return back();
    }

    public function comment(CommentRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->evaluations()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);
        return redirect("/products/{$product->id}");
    }

    public function handleBuyAccess($product_id)
    {
        if (!Auth::check()) {
            session(['buy_product_id' => $product_id]);
            return redirect('/login');
        }

        $product = Product::find($product_id);
        $user = Auth::user()->load('profile');

        return view('buy', compact('product', 'user'));
    }

    public function showPurchase(Request $request)
    {
        $user = Auth::user();
        $product = Product::find($request->product_id);
    
        return view('buy', compact('product', 'user'));
    }

    public function confirmPurchase(Request $request)
    {
        $user = Auth::user();
        $product = Product::findOrFail($request->input('product_id'));

        SoldProduct::create([
            'user_id'=>$user->id,
            'product_id'=>$product->id,
            'postcode'=>$user->profile->postcode,
            'address'=>$user->profile->address,                'building'=>optional($user->profile)->building,
        ]);

        return redirect('/');
    }

    public function showSell()
    {
        $user = Auth::user();

        $categories = Category::all();
        $conditions = Condition::all();

        return view('/sell', compact('user', 'categories', 'conditions'));
    }

    public function postSell(Request $request)
    {
        $user = Auth::user();

        $product = new Product();
        $product->user_id = $user->id;
        $product->name = $request->input('name');
        $product->brand_name = $request->input('brand_name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        } else {
            $product->image = 'products/default.png';
        }

        $product->condition_id = $request->input('condition_id');
        $product->save();

        foreach ($request->input('categories', []) as $category_id) {
            $productCategory = new ProductCategory();
            $productCategory->product_id = $product->id;
            $productCategory->category_id = $category_id;
            $productCategory->save();
        }

        return redirect('/');
    }
}
