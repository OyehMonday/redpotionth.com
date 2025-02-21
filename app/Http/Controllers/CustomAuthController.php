<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerifyEmail;
use App\Models\Order;

class CustomAuthController extends Controller
{
    public function redirectToGoogle()
    {
        if (!session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }
    
        return Socialite::driver('google')->redirect();
    }
    
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            $user = User::where('email', $googleUser->getEmail())->first();
    
            if (!$user) {
                $user = User::create([
                    'username' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()), 
                    'google_id' => $googleUser->getId(), 
                ]);
            }
    
            Session::put('user', $user);
    
            $redirectTo = session()->pull('url.intended', route('home')); 
            return redirect()->to($redirectTo);
    
        } catch (\Exception $e) {
            return redirect()->route('custom.login.form')->with('error', 'Failed to authenticate with Google.');
        }
    }
    
    public function showSignUpForm()
    {
        if (Session::has('user')) {
            return redirect()->route('landing.page'); 
        }        
        return view('signup');
    }
    
    public function signUp(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|min:6|max:50',
        ]);
    
        $verificationToken = Str::random(64);
    
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => $verificationToken,
        ]);

        try {
            Mail::to($user->email)->send(new VerifyEmail($user));
        } catch (\Exception $e) {
            return redirect()->route('custom.login.form')->with('error', 'ไม่สามารถส่งอีเมลยืนยันได้ โปรดลองอีกครั้ง');
        }
    
        return redirect()->route('custom.login.form')
            ->with('signupsuccess', "สมัครสมาชิกสำเร็จ! \nกรุณาตรวจสอบอีเมลของคุณเพื่อยืนยันบัญชี");
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();
    
        if (!$user) {
            $alreadyVerified = User::where('email_verified_at', '!=', null)->where('verification_token', null)->first();
            
            if ($alreadyVerified) {
                return redirect()->route('custom.login.form')->with('success', 'บัญชีของคุณได้รับการยืนยันแล้ว! กรุณาเข้าสู่ระบบ');
            }
    
            return redirect()->route('custom.login.form')->with('error', 'ลิงก์ยืนยันไม่ถูกต้องหรือหมดอายุ');
        }
    
        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);
    
        return redirect()->route('custom.login.form')->with('success', 'บัญชีของคุณได้รับการยืนยันแล้ว! กรุณาเข้าสู่ระบบ');
    }
     
        
    public function showLoginForm()
    {
        if (Session::has('user')) {
            return redirect()->route('landing.page'); 
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return redirect()->back()->with('error', 'บัญชีนี้ไม่มีอยู่ในระบบ');
        }
    
        if (!$user->email_verified_at) {
            return redirect()->back()->with('error', 'กรุณายืนยันอีเมลของคุณก่อนเข้าสู่ระบบ');
        }
    
        if ($user && Hash::check($request->password, $user->password)) { 
            Session::put('user', $user);
    
            $redirectTo = session()->pull('url.intended', route('dashboard'));
            return redirect()->to($redirectTo);
        }
    
        return redirect()->back()->with('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
    }

    public function dashboard()
    {
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบ');
        }
    
        $sessionUser = Session::get('user');
        
        $user = \App\Models\User::find($sessionUser->id);
    
        $orders = Order::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', '>', 1) 
                      ->where(function ($query) {
                          $query->where('status', '!=', 2) 
                                ->orWhere('created_at', '>=', now()->subHours(24)); 
                      });
            })
            ->latest()
            ->get();
    
        return view('dashboard', compact('orders', 'user')); 
    }

    // public function dashboard()
    // {
    //     if (!Session::has('user')) {
    //         return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบ');
    //     }
    
    //     $user = Session::get('user');
    //     $orders = Order::where('user_id', $user->id)
    //     ->where(function ($query) {
    //         $query->where('status', '>', 1) 
    //               ->where(function ($query) {
    //                   $query->where('status', '!=', 2) 
    //                         ->orWhere('created_at', '>=', now()->subHours(24)); 
    //               });
    //     })
    //     ->latest()
    //     ->get();
    
    //     return view('dashboard', compact('orders'));
    // }    

    public function logout()
    {
        Session::forget('user');
        return redirect()->route('home');
    }
}
