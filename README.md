# Mini Bank Application

A simplified banking system built with Laravel that's easy to understand and use.

## What This App Does

This is a basic banking application where users can:
- **Clients**: View their account balance, make deposits/withdrawals, transfer money
- **Employees**: Help clients and view account information
- **Admins**: Manage users and oversee all banking operations

## Quick Start

### 1. Install Everything
```bash
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install
```

### 2. Setup Database
```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create database tables
php artisan migrate

# Add some test data
php artisan db:seed
```

### 3. Run the App
```bash
# Start the server
php artisan serve
```

Visit: http://localhost:8000

## User Types & What They Can Do

### 👤 Client (Regular User)
- View account balance
- Make deposits and withdrawals
- Transfer money to other clients
- View transaction history
- Update profile

### 👔 Employee (Bank Staff)
- View all client accounts
- Help clients with their banking needs
- View transaction reports
- Cannot modify balances directly

### 👑 Admin (Manager)
- Everything employees can do
- Create new users
- Edit/delete users
- Approve large transactions
- View system reports

## Test Users

After running `php artisan db:seed`, you can login with:

**Client:**
- Email: `client@test.com`
- Password: `password`

**Employee:**
- Email: `employee@test.com`
- Password: `password`

**Admin:**
- Email: `admin@test.com`
- Password: `password`

## File Structure (Simplified)

```
mini-bank/
├── app/
│   ├── Http/Controllers/     # Where the main logic lives
│   │   ├── ClientController.php    # Client banking features
│   │   ├── EmployeeController.php  # Employee features
│   │   └── AdminController.php     # Admin features
│   └── Models/              # Database models
│       ├── User.php         # User accounts
│       ├── Account.php      # Bank accounts
│       └── Transaction.php  # Money transactions
├── database/
│   ├── migrations/          # Database structure
│   └── seeders/            # Test data
├── resources/views/         # Web pages (HTML)
└── routes/web.php          # URL routes
```

## Key Features Explained

### 💰 Account Balance
Each client has one bank account with a balance. They can check it anytime.

### 📝 Transactions
Every deposit, withdrawal, or transfer is recorded with:
- Amount
- Type (deposit/withdrawal/transfer)
- Date and time
- Description

### 🔒 Security
- Users must login
- Each user type can only access their allowed features
- Passwords are encrypted
- Sessions expire for security

## Database Tables

**users** - Stores user information and login details
**accounts** - Stores bank account balances
**transactions** - Records all money movements

## Development

### Adding New Features
1. Add routes in `routes/web.php`
2. Add controller methods in appropriate controller
3. Create views in `resources/views/`
4. Update database if needed

### Running Tests
```bash
php artisan test
```

### Clear Cache (if things act weird)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Common Issues

**Problem**: Can't login
**Solution**: Make sure you ran the seeders: `php artisan db:seed`

**Problem**: Database errors
**Solution**: Check your `.env` file database settings

**Problem**: Page not found
**Solution**: Make sure the server is running: `php artisan serve`

## Need Help?

1. Check the Laravel documentation: https://laravel.com/docs
2. Look at the controller files to understand how features work
3. Check the database seeders to see test data

---

This simplified version focuses on core banking features without unnecessary complexity!