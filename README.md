# Ecommerce Project

A full-stack Ecommerce web application featuring a robust API and an interactive user interface.

## Technologies Used

* **Frontend:** Vue 3 (Vite), Tailwind CSS
* **Backend:** Laravel (PHP)
* **Database:** SQLite

## Database Setup

* **Database Used:** SQLite
* **Creation & Import:** 
  Since the project uses SQLite, a lightweight file-based database, no separate database server installation (like MySQL or PostgreSQL) is required.
  * A pre-initialized database file is already included at `backend/database/database.sqlite`.
  * If you wish to initialize the database from scratch (which creates the tables and imports sample data via seeders), you will run the `php artisan migrate:fresh --seed` command during the backend setup phase.
* **Environment Variables:**
  The `.env` file in the backend folder must have the following configuration to connect to SQLite:
  ```env
  DB_CONNECTION=sqlite
  ```

## Backend Setup

Please follow these step-by-step instructions to set up the backend server:

1. Open your terminal.
2. Navigate to the backend folder.
3. Install PHP dependencies.
4. Configure the environment variables.
5. Generate the application key.
6. Run database migrations and seeders (to create tables and insert sample data).
7. Start the backend server.

**Example commands:**
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```
*(Note: If you are using Windows Command Prompt, use `copy .env.example .env` instead of `cp`)*

## Frontend Setup

Please follow these step-by-step instructions to set up the frontend client:

1. Open a **new** terminal window.
2. Navigate to the frontend folder.
3. Install Node.js dependencies.
4. Start the development server.

**Example commands:**
```bash
cd frontend
npm install
npm run dev
```

## Execution Order

To run the application successfully, please follow this startup order:

1. **Start Database Server:** (Not applicable since SQLite is file-based and runs automatically)
2. **Start Backend Server:** Run `php artisan serve` in the backend directory.
3. **Start Frontend Application:** Run `npm run dev` in the frontend directory.
4. **Open the application in the browser:** Navigate to the frontend URL.

## Default URLs

Once both servers are running, you can access the application here:

* **Frontend Application:** http://localhost:5173
* **Backend API:** http://localhost:8000

## Project Structure

```text
Ecommerce/
├── backend/          # Contains the Laravel API, migrations, and database
├── frontend/         # Contains the Vue 3 Vite application and UI components
├── README.md         # Project documentation and setup instructions
```

## Additional Notes

### Required Software
Ensure you have the following installed on your machine before setting up the project:
* **PHP** (v8.2 or higher recommended)
* **Composer** (PHP dependency manager)
* **Node.js** (v18 or higher recommended) and **npm**

### Default Login Credentials
If you ran the database seeders (`php artisan migrate:fresh --seed`), the following test accounts are available:

* **Admin Account:**
  * Email: `admin@test.com`
  * Password: `password123`
* **User Account:**
  * Email: `user@test.com`
  * Password: `password123`
* **Driver Account:**
  * Email: `driver@test.com`
  * Password: `password123`

### Troubleshooting Tips
* **Database errors on backend start:** Ensure the `database/database.sqlite` file exists inside the `backend` folder. If it does not exist, create an empty file named `database.sqlite` inside the `backend/database` folder before running `php artisan migrate`.
* **Port already in use:** If port 8000 is occupied, Laravel might start on `http://localhost:8001`. Always check the terminal output for the correct URL. Similarly, Vite might start on `http://localhost:5174` if 5173 is busy.


ស៊ីគុយទាវមួយតុ ម៉ុកចេញលុយម្នាក់ឯង @MAOVISAL🗿
