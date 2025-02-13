<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str;
use App\Services\GmailService;
use App\Models\User;
use Illuminate\Support\Facades\Mail; 
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{

    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
    
        $token = Str::random(60);
    
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );
    
        $resetLink = url('/reset-password/' . $token);
    
        try {
            Mail::to($request->email)->send(new ResetPasswordMail($resetLink));
            return back()->with('status', 'ลิงก์รีเซ็ตรหัสผ่านถูกส่งเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'ส่งอีเมลไม่สำเร็จ: ' . $e->getMessage()]);
        }
    }
    

    public function showResetPasswordForm($token)
    {
        return view('reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'ลิงก์รีเซ็ตรหัสผ่านหมดอายุ']);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password); 
            $user->save();

            DB::table('password_resets')->where('email', $request->email)->delete();

            return redirect()->route('custom.login.form')->with('success', 'รหัสผ่านของคุณถูกรีเซ็ตเรียบร้อยแล้ว!');
        }

        return back()->withErrors(['email' => 'User not found.']);
    }
}
