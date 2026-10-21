<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use App\Services\CustomerService;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_customer_user_and_sends_verification_email(): void
    {
        $this->seed(RoleSeeder::class);
        Notification::fake();
        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        $response = $this->postJson('/api/register', [
            'name' => 'Alex Example',
            'email' => '  ALEX@Example.COM  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $adminRoleId,
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Registration successful. Please verify your email address.')
            ->assertJsonPath('data.user.email', 'alex@example.com')
            ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email', 'email_verified_at'], 'token']]);

        $user = User::query()->where('email', 'alex@example.com')->firstOrFail();

        $this->assertSame('customer', $user->role->name);
        $this->assertSame('CUS-00001', $user->customer->customer_code);
        $this->assertNull($user->customer->phone_number_1);
        $this->assertSame('alex@example.com', $user->email);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_generates_unique_sequential_customer_codes(): void
    {
        $this->seed(RoleSeeder::class);

        $this->postJson('/api/register', [
            'name' => 'First Customer',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $this->postJson('/api/register', [
            'name' => 'Second Customer',
            'email' => 'second@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUS-00001',
            'user_id' => User::query()->where('email', 'first@example.com')->value('id'),
        ]);
        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUS-00002',
            'user_id' => User::query()->where('email', 'second@example.com')->value('id'),
        ]);
        $this->assertDatabaseCount('customers', 2);
    }

    public function test_registration_rolls_back_the_user_when_customer_creation_fails(): void
    {
        $this->seed(RoleSeeder::class);
        Notification::fake();
        $customerService = $this->mock(CustomerService::class);
        $customerService->shouldReceive('createForUser')
            ->once()
            ->andThrow(new RuntimeException('Customer creation failed.'));

        try {
            app(AuthService::class)->register([
                'name' => 'Alex Example',
                'email' => 'alex@example.com',
                'password' => 'password123',
            ]);
            self::fail('Registration should fail when customer creation fails.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Customer creation failed.', $exception->getMessage());
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('customers', 0);
        Notification::assertNothingSent();
    }

    public function test_registration_returns_validation_errors_for_missing_and_invalid_fields(): void
    {
        $this->seed(RoleSeeder::class);
        Notification::fake();

        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $this->assertDatabaseCount('users', 0);
        Notification::assertNothingSent();
    }

    public function test_registration_rejects_an_existing_email_with_different_case(): void
    {
        $this->seed(RoleSeeder::class);
        User::factory()->create(['email' => 'alex@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Alex Example',
            'email' => ' ALEX@EXAMPLE.COM ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_auth_requests_preserve_non_string_email_validation(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Alex Example',
            'email' => ['alex@example.com'],
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->postJson('/api/login', [
            'email' => ['alex@example.com'],
            'password' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->postJson('/api/forgot-password', [
            'email' => ['alex@example.com'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->postJson('/api/reset-password', [
            'email' => ['alex@example.com'],
            'token' => 'token',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_registration_returns_a_token_and_reports_verification_notification_failures(): void
    {
        $this->seed(RoleSeeder::class);
        $failure = new RuntimeException('Mail transport failed.');
        Exceptions::fake(RuntimeException::class);
        Notification::shouldReceive('send')->once()->andThrow($failure);

        $response = $this->postJson('/api/register', [
            'name' => 'Alex Example',
            'email' => 'alex@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['user', 'token']]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_email_normalization_migration_backfills_users_and_password_reset_tokens(): void
    {
        $user = User::factory()->create(['email' => 'MIXED.Case@example.com']);
        DB::table('password_reset_tokens')->insert([
            'email' => 'MIXED.Case@example.com',
            'token' => 'existing-token',
            'created_at' => now(),
        ]);
        $migration = require database_path('migrations/2026_10_21_100110_normalize_existing_user_email_addresses.php');

        $migration->up();

        $this->assertSame('mixed.case@example.com', $user->fresh()->email);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'mixed.case@example.com',
            'token' => 'existing-token',
        ]);
    }

    public function test_email_normalization_migration_refuses_case_insensitive_duplicates(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);
        User::factory()->create(['email' => 'DUPLICATE@example.com']);
        $migration = require database_path('migrations/2026_10_21_100110_normalize_existing_user_email_addresses.php');

        try {
            $migration->up();
            self::fail('Email normalization should reject case-insensitive duplicates.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Cannot normalize users emails while case-insensitive duplicate addresses exist.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseHas('users', ['email' => 'duplicate@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'DUPLICATE@example.com']);
    }

    public function test_login_returns_a_token_for_an_active_user(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => '  '.strtoupper($user->email).'  ',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($response->json('data.token'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    public function test_login_returns_401_for_invalid_credentials_or_inactive_users(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);
        $user->forceFill(['is_active' => false])->save();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertUnauthorized();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnauthorized();

        $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_returns_validation_errors_for_invalid_payloads(): void
    {
        $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_revokes_the_current_access_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout successful.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_forgot_password_sends_a_reset_notification_without_disclosing_unknown_emails(): void
    {
        $user = User::factory()->create();
        Notification::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => '  '.strtoupper($user->email).'  ',
        ]);

        $response->assertOk()
            ->assertJsonPath(
                'message',
                'If an account exists for that email address, a password reset link has been sent.'
            );
        Notification::assertSentTo($user, ResetPassword::class);
        $notification = Notification::sent($user, ResetPassword::class)->first();
        $expectedUrl = config('app.url').'/reset-password?token='.$notification->token
            .'&email='.urlencode($user->email);
        $this->assertSame($expectedUrl, $notification->toMail($user)->actionUrl);

        $this->postJson('/api/forgot-password', ['email' => 'unknown@example.com'])
            ->assertOk()
            ->assertJsonPath('message', $response->json('message'));

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', $response->json('message'));
    }

    public function test_forgot_password_returns_validation_errors_for_invalid_email(): void
    {
        $this->postJson('/api/forgot-password', ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_reset_password_updates_password_and_revokes_existing_tokens(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $user->createToken('existing-token');
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'email' => '  '.strtoupper($user->email).'  ',
            'token' => $token,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Password reset successful.');

        $this->assertTrue(password_verify('new-password123', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_reset_password_rejects_an_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('token');
    }

    public function test_reset_password_requires_a_confirmed_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => 'not-used-after-validation',
            'password' => 'new-password123',
            'password_confirmation' => 'different-password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_change_password_requires_the_current_password_and_revokes_all_tokens(): void
    {
        $user = User::factory()->create(['password' => 'old-password123']);
        $token = $user->createToken('current-token');
        $user->createToken('another-device');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/change-password', [
                'current_password' => 'old-password123',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password changed successfully. Please sign in again.');

        $this->assertTrue(password_verify('new-password123', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_change_password_returns_validation_error_for_an_incorrect_current_password(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('current-token');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/change-password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_verification_signed_link_marks_the_user_verified(): void
    {
        $user = User::factory()->unverified()->create();
        Event::fake([Verified::class]);
        Notification::fake();
        $user->sendEmailVerificationNotification();
        $url = Notification::sent($user, VerifyEmail::class)->first()->toMail($user)->actionUrl;

        $this->getJson($url)
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_verification_rejects_invalid_or_expired_signed_links(): void
    {
        $user = User::factory()->unverified()->create();
        $invalidHashUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1('different@example.com')]
        );

        $this->getJson($invalidHashUrl)->assertForbidden();

        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->getJson($expiredUrl)->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_authenticated_user_can_resend_verification_email(): void
    {
        $user = User::factory()->unverified()->create();
        $token = $user->createToken('current-token');
        Notification::fake();

        $this->withToken($token->plainTextToken)
            ->postJson('/api/email/verification-notification')
            ->assertStatus(202)
            ->assertJsonPath('message', 'A verification email has been sent.');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_routes_reject_requests_without_a_token(): void
    {
        $this->postJson('/api/logout')->assertUnauthorized();
        $this->postJson('/api/change-password')->assertUnauthorized();
        $this->postJson('/api/email/verification-notification')->assertUnauthorized();
    }

    public function test_unauthenticated_api_requests_without_json_headers_return_json_401(): void
    {
        $this->get('/api/user')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.'])
            ->assertHeaderMissing('Location');
    }
}
