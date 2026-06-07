# Ecommerce Project

![Project Status](https://img.shields.io/badge/Status-Active-success?style=flat-square) ![License](https://img.shields.io/badge/License-Academic-blue?style=flat-square)

## Overview

The **Ecommerce Project** is a comprehensive, full-stack online shopping platform designed to handle the complete end-to-end retail experience. It connects customers with products, provides administrators with robust management tools, and offers drivers a dedicated interface for handling deliveries. 

Built with a modern tech stack featuring a decoupled architecture—a powerful **Vue 3 (Vite)** frontend communicating with a secure **Laravel REST API** backend—this project is highly scalable, containerized using **Docker / Laravel Sail**, and production-ready.

---

## Features

### 🔐 Authentication & Authorization
* **User Authentication**
  * **Description:** Secure registration, login, and session management using Laravel Sanctum.
  * **User Role:** All Roles
  * **Related Pages:** `Login.vue`, `Register.vue`
  * **Related APIs:** `POST /register`, `POST /login`, `POST /logout`
* **Role-Based Access Control (RBAC)**
  * **Description:** Distinct routing and middleware access restrictions for Administrators, Customers, and Delivery Drivers.
  * **User Role:** Admin / User / Driver
  * **Related Pages:** `router/index.js` (Route Guards)
  * **Related APIs:** `auth:sanctum`, `admin`, and `driver` Middlewares

### 🛍️ Shopping Experience
* **Product Browsing & Search**
  * **Description:** Browse the product catalog with filtering, categories, and dynamic search.
  * **User Role:** Public / User
  * **Related Pages:** `Shop.vue`, `Home.vue`, `ProductDetail.vue`
  * **Related APIs:** `GET /products`, `GET /categories`, `GET /products/{product}/variants`
* **Shopping Cart & Checkout**
  * **Description:** Add/remove items from the cart, update quantities, apply coupons, and checkout securely.
  * **User Role:** User
  * **Related Pages:** `Cart.vue`, `Checkout.vue`
  * **Related APIs:** `GET/POST/PUT/DELETE /cart`, `POST /cart/checkout`, `POST /coupons/verify`
* **Wishlist System**
  * **Description:** Save favorite products for future purchases.
  * **User Role:** User
  * **Related Pages:** `Wishlist.vue`
  * **Related APIs:** `GET /wishlists`, `POST /wishlists/{productId}/toggle`
* **Reviews & Ratings**
  * **Description:** Customers can rate and review purchased products.
  * **User Role:** User
  * **Related Pages:** `ProductDetail.vue`
  * **Related APIs:** `GET /products/{product}/reviews`, `POST /products/{product}/reviews`

### 📦 Order & Delivery Management
* **Customer Order History**
  * **Description:** View past orders, track current order statuses, and generate invoices.
  * **User Role:** User
  * **Related Pages:** `MyOrders.vue`, `Invoice.vue`
  * **Related APIs:** `GET /my-orders`, `GET /orders/{id}/status`
* **Driver Delivery Management**
  * **Description:** Drivers can view available orders, accept deliveries, and update the delivery status in real-time. Includes photo upload for delivery proof.
  * **User Role:** Driver
  * **Related Pages:** `DriverDashboard.vue`
  * **Related APIs:** `GET /driver/available-orders`, `GET /driver/active-orders`, `POST /driver/orders/{id}/accept`, `PUT /driver/orders/{id}/status`
* **Automated Telegram Notifications**
  * **Description:** System sends automated alerts for order statuses via Telegram Webhooks.
  * **User Role:** System
  * **Related APIs:** `POST /webhook`
* **Payment Webhook Integration**
  * **Description:** Asynchronous webhook listener to process payment statuses.
  * **User Role:** System
  * **Related APIs:** `POST /payment/webhook`

### ⚙️ Administration
* **Admin Dashboard**
  * **Description:** Overview analytics and reporting for store performance.
  * **User Role:** Admin
  * **Related Pages:** `AdminDashboard.vue`
  * **Related APIs:** `GET /dashboard/analytics`
* **Product & Category Management**
  * **Description:** Full CRUD operations for products, product variants (size/color), and categories. Handles inventory out-of-stock statuses and discounts.
  * **User Role:** Admin
  * **Related Pages:** `AdminProducts.vue`, `AdminCategories.vue`
  * **Related APIs:** `POST/PUT/DELETE /products`, `POST/DELETE /products/{product}/variants`, `POST/DELETE /categories`
* **Coupon Management**
  * **Description:** Create and distribute promotional discount codes.
  * **User Role:** Admin
  * **Related Pages:** `AdminCoupons.vue`
  * **Related APIs:** `GET/POST/DELETE /coupons`
* **User Management**
  * **Description:** View, update, or remove registered users across the platform.
  * **User Role:** Admin
  * **Related Pages:** `AdminUsers.vue`
  * **Related APIs:** `GET/PUT/DELETE /admin/users`
* **Global Order Management**
  * **Description:** Admins oversee all orders and can manually adjust order statuses.
  * **User Role:** Admin
  * **Related Pages:** `AdminOrders.vue`
  * **Related APIs:** `GET /admin/orders`, `PUT /orders/{id}/status`

---

## Technology Stack

### Frontend
* **Framework:** Vue 3 (Composition API)
* **Build Tool:** Vite
* **State Management:** Pinia
* **Routing:** Vue Router
* **Styling:** Tailwind CSS v4
* **HTTP Client:** Axios
* **Icons & UI:** Lucide Vue
* **Specialized Libraries:** `html5-qrcode` (Scanner), `tesseract.js` (OCR capabilities)

### Backend
* **Framework:** Laravel 13.x (PHP 8.3+)
* **Authentication:** Laravel Sanctum (Token-based API Auth)
* **Media Storage:** Cloudinary PHP SDK (For persistent image storage on ephemeral hosts like Railway)
* **Utilities:** `simplesoftwareio/simple-qrcode`

### Database & Containerization
* **Database:** MySQL 8+
* **Environment:** Docker & Laravel Sail
* **Caching/Queues:** Redis (Configured via Sail)

---

## Database Design

### Core Tables
1. **`users`**: Stores user credentials, roles (`admin`, `user`, `driver`), and profiles.
2. **`categories`**: Product classifications.
3. **`products`**: Product details, `category_id`, `discount_percent`, `out_of_stock_status`.
4. **`product_variants`**: Size, color, and specific SKU variations.
5. **`carts`**: Active shopping sessions for users.
6. **`addresses`**: User shipping and billing addresses.
7. **`orders`**: Order metadata, `user_id`, `driver_id`, `discount`, `delivery_photo_url`, status.
8. **`order_items`**: Individual products purchased within an order.
9. **`coupons`**: Discount codes and usage rules.
10. **`wishlists`**: Saved products linked to users.
11. **`reviews`**: Ratings and feedback linked to products and users.

### ERD Summary
* A **User** has many **Orders**, **Addresses**, **Reviews**, and **Wishlists**.
* A **Category** has many **Products**.
* A **Product** has many **ProductVariants**, **Reviews**, and **OrderItems**.
* An **Order** belongs to a Customer (**User**) and optionally a Delivery **Driver**. It contains many **OrderItems**.

---

## Installation & Environment Configuration

### Prerequisites
* Docker Desktop installed and running.
* Git installed.

### Step-by-Step Setup
1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd Ecommerce
   ```

2. **Backend Configuration:**
   ```bash
   cd backend
   cp .env.example .env
   ```
   *Ensure the `.env` file matches your local environment requirements, including adding your `CLOUDINARY_URL` for image uploads. Laravel Sail handles the DB configuration by default.*

3. **Install Backend Dependencies:**
   If you have Composer installed locally, you can simply run:
   ```bash
   composer install
   ```

   *Alternatively, to install via Docker without local PHP/Composer:*
   ```bash
   docker run --rm \
   -u "$(id -u):$(id -g)" \
   -v $(pwd):/opt/project \
   -w /opt/project \
   laravelsail/php84-composer:latest \
   composer install
   ```

4. **Install Laravel Sail:**
   ```bash
   php artisan sail:install
   ```

5. **Configure Laravel Sail Alias (Optional):**

   **1. Open bash config**
   ```bash
   nano ~/.bashrc
   ```
   
   **✅ 2. Add alias at the bottom**
   Scroll all the way down and paste:
   ```bash
   alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
   ```
   
   **✅ 3. Save file in nano**
   Press: `Ctrl + O`  
   Then press: `Enter` *(This confirms the filename to save)*
   
   **✅ 4. Exit nano**
   Press: `Ctrl + X`
   
   **✅ 5. Apply changes immediately**
   Run:
   ```bash
   source ~/.bashrc
   ```

6. **Frontend Configuration:**
   ```bash
   cd ../frontend
   npm install
   ```

---

## Running the Project & Docker Setup

### 1. Start the Backend (Laravel Sail)
Navigate to the `backend` directory and start Docker containers:
```bash
cd backend
sail up -d
```

### 2. Initialize the Database
While containers are running, generate the key and seed the database:
```bash
sail artisan key:generate
sail artisan migrate:fresh --seed
```

### 3. Start the Frontend Development Server
Open a new terminal, navigate to the `frontend` directory:
```bash
cd frontend
npm run dev
```

### Common Docker/Sail Commands
* `sail up -d` : Start all containers in the background.
* `sail down` : Stop and remove containers.
* `sail shell` : Access the container's bash shell.
* `sail artisan migrate` : Run database migrations.
* `sail logs` : View application logs.
* `./vendor/bin/sail artisan make:factory ProductFactory` : Generate a new factory for the Product model.
* `./vendor/bin/sail artisan make:factory UserFactory` : Generate a new factory for the User model.

---

## API Documentation

All API routes are prefixed with `/api`. Authentication is handled via Bearer Tokens provided by Laravel Sanctum upon `/api/login`. 

**Key Endpoints Overview:**
* **Public:** `/api/products`, `/api/categories`
* **Auth:** `/api/login`, `/api/register`, `/api/logout`
* **User (Protected):** `/api/cart`, `/api/my-orders`, `/api/user/profile`
* **Admin (Protected):** `/api/admin/orders`, `/api/admin/users`, `/api/products` (POST/PUT/DELETE)
* **Driver (Protected):** `/api/driver/available-orders`, `/api/driver/orders/{id}/status`

---

## Test Accounts

Following a successful `migrate:fresh --seed`, the following accounts are available:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@test.com` | `password123` |
| **User/Customer** | `user@test.com` | `password123` |
| **Driver** | `driver@test.com` | `password123` |

---

## Folder Structure

```text
Ecommerce/
├── backend/                  # Laravel API (Backend)
│   ├── app/                  # Controllers, Models, Middleware
│   ├── database/             # Migrations, Seeders
│   ├── routes/               # API, Web, Console routing
│   ├── tests/                # PHPUnit automated tests
│   ├── composer.json         # PHP dependencies
│   └── docker-compose.yml    # Sail Docker configuration
│
├── frontend/                 # Vue 3 SPA (Frontend)
│   ├── src/
│   │   ├── api/              # Axios configurations
│   │   ├── assets/           # Static images & CSS
│   │   ├── components/       # Reusable Vue components
│   │   ├── composables/      # Shared logic (Vue Composition API)
│   │   ├── pages/            # View components (Admin, User, Public)
│   │   ├── router/           # Vue Router configuration
│   │   └── stores/           # Pinia State Management
│   ├── package.json          # Node dependencies
│   └── vite.config.js        # Vite configurations
│
└── README.md                 # Documentation
```

---

## Screenshots

<!-- Add actual screenshot images here -->
<details>
<summary>Click to view screenshots</summary>

| Home Page | Shop & Filter |
| :---: | :---: |
| ![Home](https://via.placeholder.com/400x250.png?text=Home+Page) | ![Shop](https://via.placeholder.com/400x250.png?text=Shop+Page) |

| User Dashboard | Admin Dashboard |
| :---: | :---: |
| ![Cart](https://via.placeholder.com/400x250.png?text=User+Cart) | ![Admin](https://via.placeholder.com/400x250.png?text=Admin+Dashboard) |

</details>

---

## Future Improvements

* Integrate a real-time WebSocket server (e.g., Laravel Reverb) for live driver tracking and chat.
* Implement OAuth2 (Google/Facebook) for seamless social logins.
* Integrate robust third-party payment gateways (Stripe/PayPal) directly into the frontend.
* Implement AI-based product recommendations.

---

## Team & University Information

| Role | Name |
| :--- | :--- |
| **Backend** | Mao Visal |
| **Frontend** | ម៉ាច ប៊ុនណេង |

* **University:** Royal University of Phnom Penh (RUPP)
* **Major:** Computer Science & Engineering

---

## License

This project was developed strictly for academic and learning purposes as a final university project. It demonstrates modern web development practices and system design. It is not licensed for commercial use.
