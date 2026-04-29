# Explore Egypt Tourism Platform

## Graduation Project Technical Report

## 1. Abstract

Explore Egypt is a tourism web application developed to provide users with a structured and user-friendly way to discover Egyptian civilizations, regions, and attractions. The project was implemented using Laravel with an API-first architecture and an integrated Blade-based frontend. The backend design prioritizes normalization, scalable relationships, data integrity, and maintainable business logic. The frontend was designed with a minimal and professional visual system to support usability and clarity.

This report documents the project objectives, architecture, implementation process, validation strategy, outcomes, and potential future enhancements.

## 2. Problem Statement

Tourism information is often fragmented across websites and social platforms, making it difficult for users to compare destinations and build confidence in travel decisions. The project addresses this by delivering:

- A centralized tourism information system
- Structured exploration by civilization and region
- Consistent attraction details with ratings and favorites
- A maintainable backend that supports future growth

## 3. Project Objectives

The main objectives were:

- Build a clean and scalable Laravel backend for tourism data
- Design a normalized relational database with strong constraints
- Provide RESTful API endpoints ready for frontend/mobile integration
- Implement user features such as reviews and favorites
- Create a modern, minimal, and presentation-ready frontend UI
- Maintain high code quality suitable for graduation evaluation

## 4. Scope

### In Scope

- User authentication using Laravel Breeze
- Civilizations, regions, attractions, reviews, and favorites domain modeling
- API development with structured JSON resources
- Search, filtering, pagination, and review/favorite logic
- Seeders for realistic demonstration data
- Frontend pages for browsing and profile management
- Automated testing and verification

### Out of Scope

- Online booking/payment workflows
- Admin dashboard and content moderation panel
- Multi-language support
- Token-based API authentication (session-based auth is implemented)

## 5. System Architecture

The system follows a layered Laravel architecture:

- Database Layer: migrations, foreign keys, unique constraints
- Domain Layer: Eloquent models and relationships
- Application Layer: controllers and form requests
- Presentation Layer: API resources and Blade views
- Infrastructure Layer: environment/configuration, seeders, and testing

This structure improves readability, testability, and extensibility.

## 6. Backend Design and Implementation

## 6.1 Core Entities

The final backend includes:

- Users
- Civilizations
- Regions
- Attractions
- Reviews
- Favorites

## 6.2 Relational Model

Main relationships:

- Civilization hasMany Attractions
- Region hasMany Attractions
- Attraction belongsTo Civilization
- Attraction belongsTo Region
- Attraction hasMany Reviews
- User hasMany Reviews
- User hasMany Favorites
- Favorite belongsTo User
- Favorite belongsTo Attraction
- User belongsToMany Attractions through favorites
- Attraction belongsToMany Users through favorites

## 6.3 Integrity Constraints

Implemented constraints include:

- Cascade delete on foreign keys
- Unique review per user per attraction
- Unique favorite per user per attraction
- Review rating constrained between 1 and 5

## 6.4 Business Logic

Implemented logic includes:

- Attraction model method averageRating()
- Aggregated attraction response fields:
  - average_rating
  - ratings_count
- User-specific favorite context:
  - is_favorited

## 6.5 API Design

Implemented endpoints support:

- Listing and details for attractions
- Civilizations and regions listings
- Nested civilization/region attraction browsing
- Review creation (authenticated)
- Favorite add/remove (authenticated)
- Route versioning through /api/v1 aliases

### API Features

- Eager loading to avoid N+1 queries
- Pagination on key listing endpoints
- Search by attraction name and description
- Structured validation and JSON error format

## 6.6 Validation and Error Handling

Input validation is handled using Form Requests for protected actions.

Error handling strategy:

- 401 for unauthenticated access
- 404 for missing resources
- 422 for validation failures
- 500 for unexpected server errors

All API errors return a consistent JSON envelope with message and errors keys.

## 6.7 Seeders

Seeders were implemented for demonstration and development:

- Civilizations: Ancient Egypt, Islamic, Coptic, Greco-Roman
- Regions: Cairo, Luxor, Aswan, Alexandria
- Attractions linked to both civilization and region

Seeders were built to be repeatable and safe for multiple runs.

## 7. Frontend Design and Implementation

## 7.1 Design Goals

The UI was implemented with these priorities:

- Clean and minimal structure
- Professional presentation quality
- High readability and strong hierarchy
- Content-first layout (image + text)
- Subtle interaction without heavy animation

## 7.2 Visual System

- Primary: #1E3A5F
- Accent: #C9A24D
- Background: #F8F9FB
- Text: #1B2430
- Typography: Poppins
- Card style: rounded corners, white surfaces, soft shadows

## 7.3 Implemented Pages

- Home page
- Civilizations list and details
- Regions list and details
- Attractions discover/listing page
- Attraction details page
- Login page
- Profile page with favorites

## 7.4 Frontend Data Integration

Pages are connected to backend entities through a dedicated controller and consume normalized data structures. Authenticated state enables user-aware rendering for favorites.

## 8. Testing and Quality Assurance

Testing and verification included:

- Feature tests for authentication and profile workflows
- API feature tests for pagination, search, nested listing, and favorite-state behavior
- Regression test runs after major refactors
- Migration and seeding validation

Final status during implementation:

- Full automated test suite passed
- Backend changes and UI integration remained stable

## 9. Challenges and Resolutions

### Challenge 1: Environment database mismatch

Issue: Application attempted MySQL connection on a machine without active MySQL service.

Resolution: Configured local environment to SQLite and migrated schema successfully.

### Challenge 2: Domain evolution from ratings to reviews

Issue: Project initially used rating-only structure.

Resolution: Introduced full Reviews entity with comment support and preserved compatibility in API flow.

### Challenge 3: Seeder idempotency

Issue: Duplicate test user creation on repeated seed runs.

Resolution: Updated seeding logic to use update-or-create behavior.

## 10. Outcomes

The project achieved:

- A scalable Laravel backend with normalized tourism domain design
- Clean API resources and maintainable controller/service boundaries
- Complete browse/review/favorite user journey support
- A polished frontend suitable for demos and academic presentation
- Stable test-backed development workflow

## 11. Future Enhancements

Potential next steps:

- Admin dashboard for attraction/civilization/region management
- OpenAPI/Swagger API documentation generation
- Token-based API authentication for mobile clients
- Image upload pipeline and media optimization
- Advanced analytics and recommendation features
- Localization and multilingual content

## 12. Conclusion

Explore Egypt was successfully implemented as a full-stack Laravel tourism platform with emphasis on clean architecture, backend quality, and practical usability. The final solution is structured, extensible, and aligned with graduation-level standards for software engineering quality.
