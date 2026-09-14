<?php

use App\Mail\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

describe('Registration', function () {
    test('user can register with valid credentials', function () {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful. OTP sent to your email.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        Mail::assertSent(SendOtpMail::class);
    });

    test('user cannot register with invalid email', function () {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    test('user cannot register with short password', function () {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass123',
            'password_confirmation' => 'Pass123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    test('user cannot register with mismatched passwords', function () {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password456',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    test('user cannot register with duplicate email', function () {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/register', [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    test('otp code is generated and stored on registration', function () {
        $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertDatabaseHas('otp_codes', [
            'email' => 'john@example.com',
        ]);

        $otpRecord = OtpCode::where('email', 'john@example.com')->first();
        $this->assertNotNull($otpRecord);
        $this->assertEquals(6, strlen($otpRecord->code));
        $this->assertTrue($otpRecord->expires_at->isFuture());
    });
});

describe('OTP Verification', function () {
    test('user can verify valid otp', function () {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        $otp = OtpCode::create([
            'email' => 'john@example.com',
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/verify-otp', [
            'email' => 'john@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email verified successfully. You can now login.',
            ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseMissing('otp_codes', ['id' => $otp->id]);
    });

    test('user cannot verify with invalid otp', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        OtpCode::create([
            'email' => 'john@example.com',
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/verify-otp', [
            'email' => 'john@example.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ]);
    });

    test('user cannot verify with expired otp', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        OtpCode::create([
            'email' => 'john@example.com',
            'code' => '123456',
            'expires_at' => now()->subMinutes(1),
        ]);

        $response = $this->postJson('/verify-otp', [
            'email' => 'john@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ]);
    });

    test('user cannot verify with non-existent email', function () {
        $response = $this->postJson('/verify-otp', [
            'email' => 'nonexistent@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });
});

describe('OTP Resend', function () {
    test('user can resend otp', function () {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        OtpCode::create([
            'email' => 'john@example.com',
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::fake();

        $response = $this->postJson('/resend-otp', [
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP resent to your email',
            ]);

        Mail::assertSent(SendOtpMail::class);

        // Old OTP should be deleted
        $this->assertDatabaseMissing('otp_codes', ['code' => '123456']);

        // New OTP should exist
        $this->assertDatabaseHas('otp_codes', [
            'email' => 'john@example.com',
        ]);
    });

    test('user cannot resend otp with non-existent email', function () {
        $response = $this->postJson('/resend-otp', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });
});
