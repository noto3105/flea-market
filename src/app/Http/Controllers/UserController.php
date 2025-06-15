<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\SoldProduct;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        Auth::login($user);
        return redirect('/mypage/profile');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials=$request->only('email', 'password');
        if(Auth::attempt($credentials)){
            return redirect('/?tab=mylist');
        }
        return redirect('/login');
    }

    public function showMypage(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $tab = $request->query('tab');

        if ($tab === 'buy') {
            $products = SoldProduct::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('product')
            ->filter();
        } else {
            $products = Product::where('user_id', $user->id)->get();
        }

        return view('mylist', compact('products', 'user', 'profile'));
    }

    public function showAddress($item_id)
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return view('address', [
            'profile' => $profile,
            'item_id' => $item_id
        ]);
    }

    public function postAddress(ProfileRequest $request, $item_id)
    {
        $user = Auth::user();

        $profile = Profile::firstOrNew(['user_id' => $user->id]);
        $profile->postal_code = $request->postal_code;
        $profile->address = $request->address;
        $profile->save();

        return redirect("/purchase/{$item_id}");
    }

    public function showProfile()
    {
        $user = Auth::user();
        //プロフィールを取得、なければ新規作成
        $profile = Profile::firstOrNew(['user_id' => $user->id]);
        
        return view('profile', compact('user', 'profile'));
    }

    public function postProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        //入力内容を保存
        $profile->postcode = $request->input('postcode');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        //画像
        if ($request->hasFile('img_url')) {
            $path = $request->file('img_url')->store('profile', 'public');
            $profile->img_url = $path;
        }

        $profile->save();

        return redirect('/mypage');
    }
}
