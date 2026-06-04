# Ecommerce Project

## Overview

Ecommerce Project is a full-stack web application built with:

* Frontend: Vue 3 + Vite + Tailwind CSS
* Backend: Laravel 12
* Database: MySQL
* Containerization: Docker & Docker Compose

This project is fully containerized using Docker. No manual installation of PHP, Composer, MySQL, or Node.js is required.

---

## Prerequisites

Before running the project, install:

* Docker Desktop
* Docker Compose

Verify installation:

```bash
docker --version
docker compose version
```

---

## Running the Project

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Ecommerce
```

### 2. Configure Environment

```bash
cd backend
cp .env.example .env
```

### 3. Start Docker Containers

Return to the project root directory and run:

```bash
docker compose up -d --build
```

### 4. Generate Application Key

```bash
docker compose exec backend php artisan key:generate
```

### 5. Run Database Migrations and Seeders

```bash
docker compose exec backend php artisan migrate:fresh --seed
```

---

## Access the Application

| Service     | URL                   |
| ----------- | --------------------- |
| Frontend    | http://localhost:5173 |
| Backend API | http://localhost:8000 |

---

## Test Accounts

### Administrator

Email: [admin@test.com](mailto:admin@test.com)

Password: password123

### User

Email: [user@test.com](mailto:user@test.com)

Password: password123

### Driver

Email: [driver@test.com](mailto:driver@test.com)

Password: password123

---

## Useful Docker Commands

### Start Containers

```bash
docker compose up -d
```

### Stop Containers

```bash
docker compose down
```

### View Logs

```bash
docker compose logs -f
```

### Access Backend Container

```bash
docker compose exec backend bash
```

---

## Project Structure

```text
Ecommerce/
├── backend/
├── frontend/
├── docker-compose.yml
└── README.md
```

---

## Author
## Team Members

| No. | Name         |
| --- | ------------ |
| 1   | Mao Visal    |
| 2   | ម៉ាច ប៊ុនណេង |

### University

Royal University of Phnom Penh (RUPP)

### Major

Computer Science


ស៊ីគុយទាវមួយតុ ម៉ុកចេញលុយម្នាក់ឯង @MAOVISAL🗿
