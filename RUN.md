# How to Run Mini Bank - Easy Steps!

A simple banking app where clients can deposit, withdraw, and transfer money.

## 🚀 Super Quick Start

### 1. One Command Setup
```bash
composer run setup
```
This installs everything and creates test users for you!

### 2. Start the App
```bash
composer run dev
```
Then visit: **http://localhost:8000**

That's it! You're ready to bank! 🎉

---

## 📧 Test Login Accounts

After setup, you can login with these accounts:

### 👤 Client (Regular Customer)
- **Email:** `client@test.com`
- **Password:** `password`
- **Can do:** View balance, deposit, withdraw, transfer money

### 👔 Employee (Bank Staff)
- **Email:** `employee@test.com`
- **Password:** `password`  
- **Can do:** Help customers, view accounts, reports

### 👑 Admin (Bank Manager)
- **Email:** `admin@test.com`
- **Password:** `password`
- **Can do:** Everything! Manage users, system settings

---

## 🔧 Manual Setup (if needed)

If the quick setup doesn't work:

### Step 1: Install Dependencies
```bash
composer install
npm install
```

### Step 2: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### Step 3: Database Setup
```bash
php artisan migrate:fresh --seed
```

### Step 4: Build Frontend
```bash
npm run build
```

### Step 5: Start Server
```bash
php artisan serve
```

---

## 🎯 What Each User Can Do

### Client Features
- ✅ View account balance
- ✅ Make deposits
- ✅ Make withdrawals  
- ✅ Transfer money to other users
- ✅ View transaction history
- ✅ Update profile

### Employee Features
- ✅ View all client accounts
- ✅ Search for clients
- ✅ View transaction reports
- ✅ Help customers with banking

### Admin Features
- ✅ Everything employees can do
- ✅ Create/edit/delete users
- ✅ View system reports
- ✅ Manage system settings

---

## 🗃️ Database Info

The app uses **SQLite** by default (simple file database).
- Database file: `database/database.sqlite`
- No setup needed - it's created automatically!

Want to use MySQL instead? Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_bank
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## 🧹 Useful Commands

### Reset Everything
```bash
composer run fresh-start
```
Resets database and clears all caches.

### Clear Caches
```bash
composer run clear-all
```
Clears all Laravel caches if things get weird.

### Create New Test Data
```bash
php artisan db:seed
```

---

## ❓ Common Problems & Solutions

### Problem: "Page not found" or weird errors
**Solution:** Clear caches
```bash
composer run clear-all
```

### Problem: Can't login with test accounts
**Solution:** Re-create test data
```bash
php artisan migrate:fresh --seed
```

### Problem: Database errors
**Solution:** Check `.env` file has correct database settings

### Problem: Permission errors
**Solution:** Make sure storage folders are writable
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 💡 Development Tips

### View All Routes
```bash
php artisan route:list
```

### Run Tests
```bash
php artisan test
```

### Check Database Status
```bash
php artisan migrate:status
```

### Generate New User
```bash
php artisan tinker
>>> User::factory()->create(['email' => 'test@example.com'])
```

---

## 📁 Project Structure (Simplified)

```
mini-bank/
├── app/
│   ├── Http/Controllers/     # Business logic
│   │   ├── ClientController.php
│   │   ├── EmployeeController.php  
│   │   └── AdminController.php
│   └── Models/              # Database models
│       ├── User.php
│       ├── Account.php
│       └── Transaction.php
├── database/
│   ├── migrations/          # Database structure
│   └── seeders/            # Test data
├── resources/views/         # Web pages
├── routes/web.php          # URL routes
└── .env                    # Settings
```

---

## 🎨 Customization

### Add New User Role
1. Add constant in `User.php`
2. Update role middleware
3. Create new controller
4. Add routes

### Add New Transaction Type  
1. Add constant in `Transaction.php`
2. Update controller logic
3. Add to forms and views

---

## 🆘 Need Help?

1. **Check Laravel docs:** https://laravel.com/docs
2. **Look at the controller files** to understand how features work
3. **Check the database seeder** to see how test data is created
4. **Read the model files** - they have lots of helpful comments!

---

**Happy Banking! 💰**