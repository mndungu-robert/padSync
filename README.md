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

## Database Backup Workflow

This project includes a one-command backup flow that writes timestamped SQL dumps.

Run an immediate backup:

```bash
php artisan db:backup
```

Keep a custom number of recent backups:

```bash
php artisan db:backup --keep=30
```

Backups are saved to:

```text
storage/app/backups/database
```

Daily automation is enabled via Laravel scheduler:

- Command: `db:backup --keep=14`
- Time: `01:30` server time

To enable scheduled execution on Linux, add this cron entry:

```cron
* * * * * cd /var/www/html/pad-sync && php artisan schedule:run >> /dev/null 2>&1
```

## M-Pesa Daraja Integration (STK Push)

This project now supports M-Pesa STK Push for public donations.

### 1. Configure Environment

Set these values in `.env`:

```dotenv
MPESA_BASE_URL=https://sandbox.safaricom.co.ke
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_lipa_na_mpesa_passkey
MPESA_TRANSACTION_TYPE=CustomerPayBillOnline
MPESA_CALLBACK_URL=https://your-public-domain-or-ngrok/daraja/callback
```

Use production values only when going live:

- Base URL: `https://api.safaricom.co.ke`
- Production shortcode and passkey from Safaricom portal

### 2. Apply Migration

```bash
php artisan migrate
```

### 3. Verify Routes

```bash
php artisan route:list --path=daraja
php artisan route:list --path=donate
```

### 4. Run Local Testing

- Open `/donate`
- Submit donor details, valid Kenyan phone number, and amount
- Confirm STK prompt on phone

Callback endpoint:

- `POST /daraja/callback`

### 5. If Testing Locally, Expose Callback URL

Daraja must reach your callback over public HTTPS. During sandbox testing, use a tunnel:

```bash
ngrok http 8000
```

Set `MPESA_CALLBACK_URL` to:

- `https://<ngrok-id>.ngrok-free.app/daraja/callback`

### 6. Troubleshooting

- If STK push fails immediately, check `MPESA_CONSUMER_KEY` and `MPESA_CONSUMER_SECRET`
- If STK push sends but never updates, check `MPESA_CALLBACK_URL` reachability
- Inspect logs in `storage/logs/laravel.log` for `Daraja` messages
