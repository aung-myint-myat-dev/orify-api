<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Authentication API

All authentication endpoints are under `/api`. Successful responses include a `message`; registration and login also return a `data` object containing a user resource and a Sanctum bearer token. Validation failures use Laravel's JSON `422` response format.

| Method | Endpoint | Authentication | Purpose |
| --- | --- | --- | --- |
| `POST` | `/api/register` | Public | Register with `name`, `email`, `password`, and `password_confirmation`; creates a user with the customer role and a customer profile, sends an email verification link, and returns a token. `role_id` is ignored; customer codes are generated sequentially. |
| `POST` | `/api/login` | Public | Log in with `email` and `password`; returns a bearer token. |
| `POST` | `/api/logout` | Bearer token | Revoke the current access token. |
| `POST` | `/api/forgot-password` | Public | Request a reset link with `email`. The response does not disclose whether the account exists. |
| `POST` | `/api/reset-password` | Public | Reset with `email`, `token`, `password`, and `password_confirmation`. |
| `POST` | `/api/change-password` | Bearer token | Change with `current_password`, `password`, and `password_confirmation`; revokes all of the user's tokens. |
| `GET` | `/api/email/verify/{id}/{hash}` | Signed link | Verify the email address using the signed link sent by email. |
| `POST` | `/api/email/verification-notification` | Bearer token | Resend the verification email for an unverified account. |

Send protected requests with `Authorization: Bearer <token>`. Registration requires the active `customer` role to exist (provided by `RoleSeeder`). Customer profiles are created without a phone number; `phone_number_1` is nullable. User and customer creation run in a single database transaction. Password-reset email links use `APP_URL` as their base and append `/reset-password?token=...&email=...`; the client application should handle that page and submit the reset form to `/api/reset-password`. Sensitive endpoints are rate-limited.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
