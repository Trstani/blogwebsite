<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Handle user registration - Generate and send OTP
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create user with unverified status
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'slug' => Str::slug($request->name) . '-' . Str::lower(Str::random(6)),
            'email_verified_at' => null,
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        // Save OTP to otp_codes table
        OtpCode::create([
            'email' => $user->email,
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Measure how long the SMTP send operation takes
        $startTime = microtime(true);

        Mail::to($user->email)->send(
            new SendOtpMail($otp, $user->name)
        );

        $duration = microtime(true) - $startTime;

        Log::info('OTP email sent during registration', [
            'user_id' => $user->id,
            'email' => $user->email,
            'duration_seconds' => round($duration, 3),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. OTP sent to your email.',
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Verify OTP and activate account
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check OTP in database
        $otpRecord = OtpCode::where('email', $request->email)
            ->where('code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 422);
        }

        // Verify user email
        $user = User::where('email', $request->email)->first();

        $user->update([
            'email_verified_at' => now(),
        ]);

        // Delete used OTP
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You can now login.',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Delete old OTP
        OtpCode::where('email', $user->email)->delete();

        // Generate new OTP
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        OtpCode::create([
            'email' => $user->email,
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Measure how long the SMTP send operation takes
        $startTime = microtime(true);

        Mail::to($user->email)->send(
            new SendOtpMail($otp, $user->name)
        );

        $duration = microtime(true) - $startTime;

        Log::info('OTP email resent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'duration_seconds' => round($duration, 3),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP resent to your email',
        ]);
    }
}