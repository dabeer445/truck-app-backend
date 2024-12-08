<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run seeders for roles
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);

        // Verify role assignment
        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('customer'));
    }

    /** @test */
    public function registration_validates_email_format()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_prevents_duplicate_emails()
    {
        // Create initial user
        User::create([
            'name' => 'Existing User',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890'
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_validates_password_strength()
    {
        $weakPasswords = [
            'short', // too short
            'onlylowercase',
            'ONLYUPPERCASE',
            '12345678',
            'nospecialchars123'
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->postJson('/api/v1/auth/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => $password,
                'password_confirmation' => $password,
                'phone' => '1234567890'
            ]);
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        }
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'phone' => '1234567890'
        ]);
        $user->assignRole('customer');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);
    }

    /** @test */
    public function login_fails_with_incorrect_password()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'phone' => '1234567890'
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword123!'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function login_fails_with_unverified_email_if_verification_required()
    {
        // Only if email verification is required
        if (config('auth.verify_email')) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('Password123!'),
                'phone' => '1234567890'
            ]);

            $response = $this->postJson('/api/v1/auth/login', [
                'email' => 'john@example.com',
                'password' => 'Password123!'
            ]);

            $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Email not verified'
                ]);
        }
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'phone' => '1234567890'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);

        // Verify token was deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id
        ]);
    }

    /** @test */
    public function logout_fails_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function multiple_devices_can_login()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'phone' => '1234567890'
        ]);

        // Login from multiple devices
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => 'john@example.com',
                'password' => 'Password123!'
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure(['token']);
        }

        // Verify multiple tokens exist
        $this->assertEquals(3, $user->tokens()->count());
    }
}