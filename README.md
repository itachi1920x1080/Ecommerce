# Ecommerce Project

## Overview

Ecommerce Project is a full-stack web application that provides an online shopping platform with user authentication, product management, order processing, and role-based access control.

### Technologies Used

**Frontend**

* Vue 3 (Vite)
* Tailwind CSS
* JavaScript

**Backend**

* Laravel 12 (PHP)

**Database**

* MySQL

---

## Database Setup

### Database Requirements

* MySQL 8.0 or higher

### Create Database

Create a database named:

```sql
CREATE DATABASE laravel;
```

### Configure Environment Variables

Open the backend `.env` file and update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Adjust these values according to your MySQL configuration.

### Initialize Database

The project uses Laravel migrations and seeders. No manual SQL import is required.

Run:

```bash
php artisan migrate:fresh --seed
```

This command will:

* Create all database tables
* Generate sample data
* Create default user accounts

---

## Backend Installation

### Step 1: Navigate to Backend Folder

```bash
cd backend
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Create Environment File

Linux/macOS:

```bash
cp .env.example .env
```

Windows:

```cmd
copy .env.example .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Configure Database

Update the `.env` file with your MySQL credentials.

### Step 6: Run Migrations and Seeders

```bash
php artisan migrate:fresh --seed
```

### Step 7: Start Backend Server

```bash
php artisan serve
```

Backend URL:

```text
http://localhost:8000
```

---

## Frontend Installation

Open a new terminal window.

### Step 1: Navigate to Frontend Folder

```bash
cd frontend
```

### Step 2: Install Dependencies

```bash
npm install
```

### Step 3: Start Development Server

```bash
npm run dev
```

Frontend URL:

```text
http://localhost:5173
```

---

## Application Startup Order

Follow the order below when running the project:

1. Start MySQL Server
2. Start Laravel Backend Server
3. Start Vue Frontend Development Server
4. Open the frontend URL in a browser

---

## Default URLs

| Service     | URL                   |
| ----------- | --------------------- |
| Frontend    | http://localhost:5173 |
| Backend API | http://localhost:8000 |

---

## Project Structure

```text
Ecommerce/
│
├── backend/          # Laravel API and database logic
├── frontend/         # Vue.js application
├── README.md         # Project documentation
│
└── database/
    ├── migrations/
    └── seeders/
```

---

## Test Accounts

After running:

```bash
php artisan migrate:fresh --seed
```

You can log in using the following accounts.

### Administrator

```text
Email: admin@test.com
Password: password123
```

### User

```text
Email: user@test.com
Password: password123
```

### Driver

```text
Email: driver@test.com
Password: password123
```

---

## Software Requirements

* PHP 8.2+
* Composer 2+
* MySQL 8+
* Node.js 18+
* npm 9+

---

## Troubleshooting

### Database Connection Error

* Verify MySQL is running.
* Check database credentials in `.env`.
* Ensure the database exists.

### Port Already in Use

Laravel:

```bash
php artisan serve --port=8001
```

Vite:

```bash
npm run dev
```

Check the terminal output for the correct URL.

### Dependency Installation Issues

Backend:

```bash
composer install
```

Frontend:

```bash
npm install
```

If necessary, delete:

```text
vendor/
node_modules/
```

and reinstall dependencies.

---

## Author

Student: Mao Visal
University: Royal University of Phnom Penh (RUPP)
Major: Computer Science


ស៊ីគុយទាវមួយតុ ម៉ុកចេញលុយម្នាក់ឯង @MAOVISAL🗿
