# Jamis-Art Project Conventions and Feature Planning

This document serves as the central guide for project conventions, architecture, and feature planning for the `jamis-art` application. All developers and AI agents should follow these guidelines when contributing to the repository.

## 1. Project Architecture and Structure

The project uses a custom Model-View-Controller (MVC) architecture built with plain PHP, PDO for database interactions, and MySQL (MariaDB).

### Directory Structure
*   **`models/`**: Contains classes that map to database tables. Models use private properties with standard getter and setter methods. (e.g., `Article.php`, `User.php`).
*   **`controllers/`**: Contains business logic and database queries using PDO. Controller classes correspond to models and contain standard CRUD operations (`getAll`, `getById`, `save`, `update`, `delete`). (e.g., `ArticleController.php`).
*   **`views/`**: Contains PHP templates for the user interface, organized by sections such as `admin`, `auth`, and `landing`.
*   **`components/`**: Reusable UI partials (headers, navbars, forms) included within views, organized by sections.
*   **`lib/` / `api/`**: Support libraries and API endpoint handlers.
*   **`assets/`**: Static files like CSS, JS, and images.
*   **`storage/`**: Directory for user-uploaded files managed by the application.
*   **`connect.php`**: Global database connection initialization via PDO.

### Naming Conventions
*   **Models**: PascalCase, singular (e.g., `SupportMessage`, `ArtType`).
*   **Controllers**: PascalCase, singular model name followed by `Controller` (e.g., `SupportMessageController`).
*   **Database Tables**: kebab-case (e.g., `support-messages`, `upload-group`, `art-type`).
*   **Database Columns**: camelCase (e.g., `publishedAt`, `artTypeId`, `firstName`).
*   **Views/Components Files**: kebab-case or snake_case, ending in `.php`.

### Code Style Guidelines
*   **Database Access**: Controllers should use `global $pdo` initialized in `connect.php`. Use prepared statements (`$pdo->prepare()`) to prevent SQL injection.
*   **Routing**: Standard PHP include/require logic is used to route requests to specific controllers and views.
*   **Error Handling**: Controllers typically use `try/catch` blocks around database queries, failing with `die("Error: " . $e->getMessage())` on exceptions.

---

## 2. Feature Planning and Implementations

The following core features define the application scope, as determined by the database schema and existing controller structure.

### 2.1 User Authentication and Roles
*   **Objective**: Manage user accounts and permissions.
*   **Details**: Supports registering and logging in. Users have specific roles (`Standard`, `Admin`) and are tied to a specific `artTypeId`.
*   **Entities**: `User`, `AuthController`, `UserController`.

### 2.2 Art Types Management
*   **Objective**: The central categorization for the platform (Painting, Music, Dance, Acting).
*   **Details**: Allows assigning specific colors and representative images (uploads) to each art type. All content (articles, resources, locations, users) is linked to an art type.
*   **Entities**: `ArtType`, `ArtTypeController`.

### 2.3 Articles and Blog System
*   **Objective**: Allow authors to publish content.
*   **Details**: Articles contain titles, content, descriptions, and cover images. They are categorized by `artTypeId` and `variant` (Interview, Highlight, Technique, History).
*   **Features**: Includes search functionality, filtering by category, and infinite scroll for browsing.
*   **Entities**: `Article`, `ArticleController`.

### 2.4 Media and Upload Management
*   **Objective**: Centralized file handling system.
*   **Details**: Supports file uploads grouped logically using hierarchical categories (`ROOT`, `art-type`, `articles`, `resources`, `profiles`). Files can be marked as temporary or private.
*   **Entities**: `Upload`, `UploadGroup`, `UploadController`, `UploadGroupController`.

### 2.5 Resources Library
*   **Objective**: A repository of downloadable or viewable materials.
*   **Details**: Links physical uploaded files (`uploadId`) to specific art types with a label and description.
*   **Entities**: `Resource`, `ResourceController`.

### 2.6 Locations Mapping
*   **Objective**: Geographic points of interest related to art types.
*   **Details**: Stores `latitude`, `longitude`, `label`, and `description` to be rendered on maps.
*   **Entities**: `Location`, `LocationController`.

### 2.7 Support and Contact System
*   **Objective**: Allow users to contact administration.
*   **Details**: A contact form that adjusts behavior based on authentication status. Guests must provide an email and select an art type, while authenticated users have these fields pre-filled or implied.
*   **Entities**: `SupportMessage`, `SupportMessageController`.
