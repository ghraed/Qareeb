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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
## Automated tests

Run all PHP, JavaScript, and overlapping image-upload integration tests:

```bash
npm run test:all
```

Run individual suites with `composer test`, `npm test`, or
`python3 tests/integration/image-concurrency.py`. PHP tests default to an isolated
in-memory SQLite database; they do not need the MySQL database in `.env.testing`.
An explicit `DB_CONNECTION` and `DB_DATABASE` environment override can select a
dedicated test database instead. Never select a database containing real data:
feature tests reset their database. PHP needs PDO SQLite and GD; the other suites
need Node.js 22+ and Python 3. See [the test coverage guide](tests/README.md).

## Admin account

Seed the development administrator with:

```bash
php artisan db:seed --class=AdminUserSeeder
```

The seeder reads `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` from the environment.

## Provider testing data

To remove existing providers except `rozer@qareeb.com`, and admins except
`admin@qareeb.com`, run the explicit cleanup once. Both retained accounts must
already exist with the correct roles; their passwords and profiles are preserved.
Seekers are preserved. Other providers/admins, their sessions and API tokens,
associated profiles/reviews, and their uploaded business images are deleted.

```bash
php artisan db:seed --class=ResetProviderAccountsSeeder
php artisan db:seed --class=ProviderSeeder
php artisan storage:link
```

`ProviderSeeder` creates 30 approved providers: 10 each in Photography, Gardening,
and Interior Design across Beirut, Matn, Keserwan, Byblos, Batroun, Tripoli, Zahlé,
Sidon, Tyre, and Chouf. Other categories receive no demo providers, so empty
category results can be tested (a retained provider may still belong to one).
Profiles include valid district/city pairs, coordinates, Lebanese sample phone
numbers, service descriptions, availability variations, local profile/cover
photos, and 2–3 shuffled gallery photos matching their category. Four demo seekers
provide 0–4 reviews per business; rating summaries match the actual reviews.
All identities, contact details and reviews are fictional testing data.

Demo provider emails follow `demo.photography.1@example.test`,
`demo.gardening.1@example.test`, and `demo.interior-design.1@example.test` through
number 10. Demo seekers use `demo.seeker.1@example.test` through number 4.
All demo accounts initially use password `QareebTest2026!`. Rerunning
`ProviderSeeder` refreshes demo profiles and reviews without duplicating accounts,
files or reviews, or resetting existing passwords. It preserves other accounts.
The cleanup seeder deletes demo providers too, so run it before reseeding.

Normal `php artisan db:seed` runs `AdminUserSeeder` and `ProviderSeeder`; it does
not perform destructive cleanup. Use these seeders only for development/testing.

Photos are bundled in `database/seeders/assets/providers` so seeding works offline.
The source URLs are listed in that folder's `SOURCES.md`. They are illustrative
samples, not evidence of work performed by any real business.
