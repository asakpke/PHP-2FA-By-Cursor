# PHP 2FA Implementation

This project implements Two-Factor Authentication (2FA) in PHP using the `robthree/twofactorauth` library.

## Setup Instructions

1. Clone the repository
```bash
git clone https://github.com/asakpke/PHP-2FA-By-Cursor.git
cd PHP-2FA-By-Cursor
```

2. Install dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
cp config/database.example.php config/database.php
```

4. Update the `.env` file with your configuration

5. Set up the database
```sql
-- Create your database tables here
```

## Security Notes

- Never commit the `.env` file
- Keep your 2FA secret keys secure
- Regularly update dependencies
- Use strong password hashing

## Development

1. Start the local server
```bash
php -S localhost:8000
```

2. Run tests
```bash
vendor/bin/phpunit
```

## License

[Add your license here]