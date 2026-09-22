<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar.',
            ], 422);
        }

        // Hapus OTP password reset sebelumnya
        OtpCode::where('email', $email)
            ->where('type', 'password_reset')
            ->delete();

        // Generate OTP 6 digit
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        // Simpan OTP
        OtpCode::create([
            'email' => $email,
            'type' => 'password_reset',
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Kirim email
        Mail::to($email)->send(
            new SendOtpMail(
                $otp,
                $user->name,
                'password_reset'
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP has been sent to your email.',
            'email' => $email,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau OTP tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar.',
            ], 422);
        }

        $otpRecord = OtpCode::where('email', $email)
            ->where('type', 'password_reset')
            ->where('code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $request->session()->put([
            'password_reset_user_id' => $user->id,
            'password_reset_verified' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'email' => $email,
        ]);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (
            !$request->session()->get('password_reset_verified') ||
            !$request->session()->get('password_reset_user_id')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset session is invalid or expired.',
            ], 422);
        }

        $userId = $request->session()->get('password_reset_user_id');

        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget([
                'password_reset_user_id',
                'password_reset_verified',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User account not found.',
            ], 422);
        }

        $user->update([
            'password' => $request->password,
        ]);

        OtpCode::where('email', $user->email)
            ->where('type', 'password_reset')
            ->delete();

        $request->session()->forget([
            'password_reset_user_id',
            'password_reset_verified',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'redirect' => '/writer/dashboard',
        ]);
    }
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar.',
            ], 422);
        }

        // Hapus OTP password reset sebelumnya
        OtpCode::where('email', $email)
            ->where('type', 'password_reset')
            ->delete();

        // Buat OTP baru
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        OtpCode::create([
            'email' => $email,
            'type' => 'password_reset',
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Kirim OTP baru
        Mail::to($email)->send(
            new SendOtpMail(
                $otp,
                $user->name,
                'password_reset'
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'A new password reset OTP has been sent to your email.',
            'email' => $email,
        ]);
    }
}