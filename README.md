# Explore Egypt

Explore Egypt is a Laravel tourism web application built as a graduation project. It includes:

- A clean API-first backend with normalized relational design
- Laravel Breeze authentication
- A modern Blade-based frontend UI
- Seeded tourism data for demo and development

This document is the full technical documentation of what has been implemented.

## 1. Project Overview

The platform allows users to:

- Explore civilizations and Egyptian regions (governorates)
- Browse and search attractions
- View attraction details and average ratings
- Add one review per attraction (per user)
- Save/remove favorite attractions
- Manage profile and view favorite attractions

## 2. Tech Stack

- Framework: Laravel 12
- Authentication: Laravel Breeze (session-based)
- Database: SQLite (local default), compatible with MySQL/PostgreSQL
- API: REST-style JSON endpoints
- Frontend: Blade templates + custom CSS design system
- Testing: PHPUnit feature and unit tests

## 3. Architecture and Code Organization

### Core Backend Layers

- Models: domain and relationships
- Migrations: schema definition and integrity constraints
- Form Requests: validation and authorization
- Controllers: API/business flow orchestration
- Resources: normalized JSON API response formatting
- Seeders: demo dataset for civilizations, regions, and attractions

### Frontend Layers

- Blade layout/component system for reusable page shell
- Feature pages grouped by tourism domain
- Shared CSS design system with project color palette and spacing rules

## 4. Database Design

### Main Entities

- users
- civilizations
- regions
- attractions
- reviews
- favorites

### Table Details

#### users

Managed by Breeze + Laravel default auth fields.

#### civilizations

- id
- name (unique)
- description
- image (nullable)
- timestamps

#### regions

- id
- name (unique)
- description (nullable)
- image (nullable)
- timestamps

#### attractions

- id
- name
- description
- image (nullable)
- location (string)
- civilization_id (FK)
- region_id (FK)
- timestamps

#### reviews

- id
- user_id (FK)
- attraction_id (FK)
- rating (1-5)
- comment (nullable)
- timestamps
- unique(user_id, attraction_id)

#### favorites

- id
- user_id (FK)
- attraction_id (FK)
- timestamps
- unique(user_id, attraction_id)

### Integrity Rules

- Foreign keys use cascade delete
- One favorite per user per attraction
- One review per user per attraction
- Rating validation constrained to 1-5

## 5. Relationship Model

Implemented relationships include:

- Civilization hasMany Attractions
- Region hasMany Attractions
- Attraction belongsTo Civilization
- Attraction belongsTo Region
- Attraction hasMany Reviews
- User hasMany Reviews
- User hasMany Favorites
- Favorite belongsTo User
- Favorite belongsTo Attraction
- User belongsToMany Attractions via favorites (favoriteAttractions)
- Attraction belongsToMany Users via favorites (usersWhoFavorited)

## 6. Business Logic Implemented

### Attraction Rating Logic

- `averageRating()` method exists in Attraction model
- Aggregated fields exposed in API responses:
  - average_rating
  - ratings_count

### Favorites Context

- API can return `is_favorited` for authenticated users

### Search and Pagination

Attractions listing supports:

- Search on name and description
- Filtering by civilization_id and region_id
- Pagination with `paginate(10)`

## 7. API Documentation

Base path:

- `/api`
- `/api/v1` (versioned aliases)

### Public Endpoints

- GET `/api/attractions`
- GET `/api/attractions/{id}`
- GET `/api/civilizations`
- GET `/api/regions`
- GET `/api/civilizations/{id}/attractions`
- GET `/api/regions/{id}/attractions`

### Protected Endpoints (auth + web middleware)

- POST `/api/reviews`
- POST `/api/ratings` (compatibility alias to review creation)
- POST `/api/favorites`
- DELETE `/api/favorites/{attraction}`

### Query Parameters (attractions)

- `search`
- `civilization_id`
- `region_id`

### Attraction Response Shape

- id
- name
- description
- image
- location
- civilization
- region
- average_rating
- ratings_count
- reviews (in details response)
- is_favorited

### Error Response Structure

API errors use JSON shape:

- message
- errors

Used status codes include:

- 401 unauthenticated
- 404 not found
- 422 validation
- 500 server error

## 8. Seed Data

Implemented seeders:

- CivilizationSeeder
  - Ancient Egypt
  - Islamic
  - Coptic
  - Greco-Roman
- RegionSeeder
  - Cairo
  - Luxor
  - Aswan
  - Alexandria
- AttractionSeeder
  - Linked to both civilization and region
  - Includes representative tourism records

DatabaseSeeder calls all tourism seeders and creates/updates:

- Default admin user (via AdminUserSeeder)
- Default test user

Admin user seed can be configured from environment variables:

- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

Default seeded admin credentials (local/demo):

- Email: `admin@example.com`
- Password: `password`

Role system values:

- `admin`
- `user`

To verify admin access controls quickly:

- `php artisan test --filter=AdminAccessTest`

## 9. Frontend UI Documentation

A complete Blade UI has been implemented using the required clean and professional style.

### Design System

- Primary: `#1E3A5F`
- Accent: `#C9A24D`
- Background: `#F8F9FB`
- Text: `#1B2430`
- Typography: Poppins
- Card style: white surface, soft shadow, 12px radius
- Motion: subtle hover and transitions only

### Implemented Pages

- Home page
  - Navbar, hero, civilizations preview, regions preview, featured attractions, footer
- Civilizations index and details
- Regions index and details
- Attractions discover/listing page
- Attraction details page
- Login page (redesigned)
- Profile page with favorites cards and account settings

### Frontend Routing

- GET `/`
- GET `/discover`
- GET `/civilizations`
- GET `/civilizations/{civilization}`
- GET `/regions`
- GET `/regions/{region}`
- GET `/attractions/{attraction}`
- GET `/profile` (authenticated)

## 10. Authentication and Session

- Laravel Breeze handles login/register/password reset
- Session-based authentication is used for protected actions
- Profile update, password update, and account deletion are active

## 11. Testing and Verification

Current project test status:

- Full suite passing
- API feature tests included for:
  - pagination
  - search
  - nested listings
  - favorite state in responses
  - versioned endpoint availability

Run all tests:

```bash
php artisan test
```

Run API feature tests only:

```bash
php artisan test tests/Feature/Api/AttractionApiTest.php
```

## 12. Local Setup Guide

### Prerequisites

- PHP 8.2+
- Composer

### Setup Steps

1. Install dependencies

```bash
composer install
```

1. Prepare environment

```bash
copy .env.example .env
php artisan key:generate
```

1. Configure database in `.env`

Local recommended defaults used in this project:

- `DB_CONNECTION=sqlite`
- `DB_DATABASE=database/database.sqlite`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

1. Create SQLite file if missing

```bash
type nul > database\database.sqlite
```

1. Run migrations and seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

1. Start application

```bash
php artisan serve
```

## 13. Useful Commands

```bash
php artisan route:list
php artisan migrate
php artisan migrate:fresh --seed
php artisan config:clear
php artisan test
```

## 14. Notes for Future Work

Potential next enhancements:

- Add API endpoint docs in OpenAPI/Swagger format
- Add authorization policies for advanced role-based access
- Add image upload/storage pipeline
- Add caching for heavy listings and aggregates
- Add dedicated API tests for error contracts and review endpoints

---

Explore Egypt is now implemented with a scalable Laravel backend, structured API responses, seeded tourism data, and a clean modern frontend ready for presentation and further expansion.
