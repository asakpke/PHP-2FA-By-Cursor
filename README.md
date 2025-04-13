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

5. Set up the database (see Database Setup section below)

## Database Setup

1. Create a new MySQL database
2. Import the database schema:
```bash
mysql -u your_username -p your_database_name < database/migrations/schema.sql
```

### Database Structure
The application uses the following key tables:

- `users`: Stores user information and 2FA settings
- `activity_logs`: Tracks user actions and system events
- `api_keys`: Manages API authentication
- `email_verifications`: Handles email verification process
- `password_resets`: Manages password reset requests
- `roles` and `permissions`: Implements role-based access control

For complete schema details, see [database/migrations/schema.sql](database/migrations/schema.sql)

## Running the Application

1. Start the local server
```bash
php -S localhost:8000 -t public/
```

2. Access the application at http://localhost:8000

## Security Notes

- Never commit the `.env` file
- Keep your 2FA secret keys secure
- Regularly update dependencies
- Use strong password hashing

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Copyright (c) 2025 Aamir Shahzad