<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\PendingRegistration;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
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

        $email = strtolower(trim($request->email));

        // Replace any existing pending registration for this email
        PendingRegistration::where('email', $email)->delete();

        // Remove any previous registration OTP
        OtpCode::where('email', $email)
            ->where('type', 'registration')
            ->delete();

        // Store registration data temporarily until OTP is verified
        $pendingRegistration = PendingRegistration::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        // Save registration OTP
        OtpCode::create([
            'email' => $email,
            'type' => 'registration',
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Measure SMTP send duration
        $startTime = microtime(true);

        Mail::to($email)->send(
            new SendOtpMail(
                $otp,$pendingRegistration->name,'registration')
        );

        $duration = microtime(true) - $startTime;

        Log::info('OTP email sent during registration', [
            'email' => $email,
            'duration_seconds' => round($duration, 3),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. OTP sent to your email.',
            'user_id' => null,
            'email' => $email,
        ]);
    }

    /**
     * Verify OTP and activate account
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Find pending registration
        $pendingRegistration = PendingRegistration::where('email', $email)
            ->first();

        if (! $pendingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data not found or has expired.',
            ], 422);
        }

        // Check pending registration expiration
        if ($pendingRegistration->isExpired()) {
            $pendingRegistration->delete();

            OtpCode::where('email', $email)
                ->where('type', 'registration')
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Registration session has expired. Please register again.',
            ], 422);
        }

        // Check registration OTP
        $otpRecord = OtpCode::where('email', $email)
            ->where('type', 'registration')
            ->where('code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 422);
        }

        // Safety check: prevent duplicate user creation
        if (User::where('email', $email)->exists()) {
            $pendingRegistration->delete();
            $otpRecord->delete();

            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar. Silakan login.',
            ], 422);
        }

        // Create the actual user only after successful OTP verification
        $user = User::create([
            'name' => $pendingRegistration->name,
            'email' => $pendingRegistration->email,
            'password' => $pendingRegistration->password,
            'role' => 'writer',
            'slug' => Str::slug($pendingRegistration->name) . '-' . Str::lower(Str::random(6)),
            'email_verified_at' => now(),
        ]);

        // Remove temporary registration and used OTP
        $pendingRegistration->delete();
        $otpRecord->delete();

        // Automatically login verified user
        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'user_id' => $user->id,
            'redirect' => '/writer/dashboard',
        ]);
    }

    /**
     * Resend OTP
     */
   public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Find pending registration
        $pendingRegistration = PendingRegistration::where('email', $email)
            ->first();

        if (! $pendingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data not found or has expired.',
            ], 422);
        }

        // Check registration expiration
        if ($pendingRegistration->isExpired()) {
            $pendingRegistration->delete();

            OtpCode::where('email', $email)
                ->where('type', 'registration')
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Registration session has expired. Please register again.',
            ], 422);
        }

        // Delete old registration OTP
        OtpCode::where('email', $email)
            ->where('type', 'registration')
            ->delete();

        // Generate new OTP
        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        OtpCode::create([
            'email' => $email,
            'type' => 'registration',
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        $pendingRegistration->update([
            'expires_at' => now()->addMinutes(10),
        ]);

        // Measure SMTP send duration
        $startTime = microtime(true);

        Mail::to($email)->send(
            new SendOtpMail(
                $otp,$pendingRegistration->name,'registration')
        );

        $duration = microtime(true) - $startTime;

        Log::info('OTP email resent', [
            'email' => $email,
            'duration_seconds' => round($duration, 3),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP resent to your email',
        ]);
    }
}    