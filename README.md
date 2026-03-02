# Sari-Sari Store Inventory System

This project is a simple inventory management system built with HTML, CSS, JavaScript, PHP, and MySQL (XAMPP environment).

## Features

- Login page for admin/staff (session-based)
- Add, view, update, and delete inventory items
- Data stored in MySQL (`sari_sari_db` database)
- Responsive layout and basic validation

## Setup

1. Start **Apache** and **MySQL** via XAMPP control panel.
2. Open your browser to: `http://localhost/sari-sari-inventory/login.php`
3. Log in using default credentials:
   - **admin** / `admin123` (role: admin)
   - **staff** / `staff123` (role: staff)
4. After login you'll be redirected to the inventory page.

The database and tables are created automatically on first load, or you can import `db/init.sql` manually.

## API Endpoints

All endpoints use JSON:

- **GET** `/api/items.php` - Get all items
- **POST** `/api/items.php` - Create new item
- **PUT** `/api/items.php?id=X` - Update item
- **DELETE** `/api/items.php?id=X` - Delete item
- **POST** `/api/auth.php` - Login (body: `{username,password}`)
- **GET** `/api/auth.php` - Check session status
- **DELETE** `/api/auth.php` - Logout

## Troubleshooting

### Login problems

- Default accounts are in the database (username/password):
  - `admin` / `admin123` (role=admin)
  - `staff` / `staff123` (role=staff)
- If you forget credentials, re-run `db/init.sql` or edit the `users` table via phpMyAdmin.

### General

- Ensure Apache & MySQL are running
- Access the app via HTTP (`http://localhost/...`), not `file://`

## License

MIT License. Feel free to reuse or modify this code.
