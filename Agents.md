# Agents.md — Brickspoint Hotel Booking Website

## Project Overview
This is a Laravel 12 hotel management and booking website for **Brickspoint Hotel**, located in Wuse, Abuja, Nigeria. It consists of a public-facing frontend for guests and a protected admin panel for hotel staff.

## Tech Stack
- **Backend:** PHP 8.2+, Laravel 12, Laravel UI (auth scaffolding)
- **Frontend:** Blade templates, Tailwind CSS 4, Bootstrap 5, Vite
- **Database:** SQLite (default/dev); configurable for MySQL/PostgreSQL via `.env`
- **Queue/Cache/Session:** Database driver (all three)
- **Asset Pipeline:** Vite with `laravel-vite-plugin`, SASS

## Running the Project

### Prerequisites
- PHP 8.2+
- Composer
- Node.js + npm

### Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

### Development (all-in-one)
```bash
composer run dev
```
This concurrently starts:
- `php artisan serve` — Laravel dev server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer
- `npm run dev` — Vite HMR

### Build for Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Run Tests
```bash
composer test
```
This clears config and runs PHPUnit (`php artisan test`).

## Architecture

### Directory Structure
```
app/
  Helpers/          # Global helper functions (autoloaded)
  Http/
    Controllers/    # Request handling
      Admin/        # Protected admin panel controllers
      Auth/         # Laravel UI auth controllers
    Middleware/     # Custom middleware
  Models/           # Eloquent ORM models
  Providers/        # AppServiceProvider
database/
  migrations/       # Schema definitions
  seeders/          # Database seeders
routes/
  web.php           # All web routes (public + admin)
  console.php       # Artisan console routes
resources/          # Blade views, CSS, JS
public/             # Compiled assets (via Vite)
```

## Core Components

### Public Frontend (`PageController`)
Handles all guest-facing pages. Routes are unprotected.

| Route | Method | Description |
|---|---|---|
| `/` | GET | Home page with featured rooms, hero media, testimonials |
| `/rooms` | GET | All rooms listing |
| `/rooms/{room}` | GET | Room detail with media gallery |
| `/gallery` | GET | Photo gallery |
| `/local-guide` | GET | Nearby attractions grouped by category |
| `/favorites` | GET | Saved favorites (client-side, via localStorage + AJAX) |
| `/api/get-favorite-rooms` | POST | Returns room data by IDs for favorites page |
| `/contact` | POST | Stores contact form submission |
| `/feedback` | GET/POST | Guest feedback form |
| `/log-whatsapp-lead` | POST | Captures WhatsApp click leads |

### Admin Panel (`/admin/*`)
All admin routes require authentication (`auth` middleware) and share the `admin.` route name prefix.

| Controller | Responsibility |
|---|---|
| `AdminController` | Dashboard with stats (rooms, gallery, messages, leads, feedback, visitors) |
| `RoomController` | Full CRUD for rooms + separate media upload/delete |
| `GalleryController` | Upload and delete hotel gallery images |
| `SettingController` | Key-value site settings (e.g., hero media) |
| `AttractionController` | Manage local guide attractions |
| `ContactController` | View and manage contact form submissions |
| `WhatsappLeadController` | View and delete WhatsApp leads |
| `FeedbackController` | View, approve/reject, and delete guest feedback |

### Models
| Model | Key Fields / Notes |
|---|---|
| `Room` | name, description, price, guests, image (path), features (JSON array of `{name, icon}`) |
| `RoomMedia` | file_path, type (`image`/`video`), belongs to `Room` |
| `Gallery` | Hotel-wide photo gallery entries |
| `Setting` | Key-value store with `type` column; cached via `setting()` helper |
| `Contact` | name, email, message, `is_read` flag |
| `Visitor` | ip_address, visited_date (unique per day per IP) |
| `WhatsappLead` | Captures visitors who clicked WhatsApp button |
| `Feedback` | Guest reviews; has `rating`, `is_approved`, `is_read` flags |
| `Attraction` | Local area attractions with `category` for grouping |
| `User` | Standard Laravel auth user |

### Middleware
- **`LogVisitor`** — Applied globally (or per-route). Calls `Visitor::firstOrCreate` per IP per day to track unique daily visitors. Errors are silently caught to avoid disrupting requests.

### Helpers
- **`setting($key, $default = null)`** — Global function in `app/Helpers/SettingsHelper.php`. Fetches a site setting by key, backed by `Cache::rememberForever('settings', ...)`. Cache must be cleared (`php artisan cache:clear`) after changing settings.

## Data Flow

### Request Lifecycle
1. HTTP request hits Laravel's front controller (`public/index.php`)
2. `LogVisitor` middleware records the visitor
3. Route dispatcher matches the URI and invokes the appropriate controller
4. Controller fetches data from Eloquent models, passes it to Blade views
5. Blade renders the response HTML

### Admin Authentication
- Handled by Laravel UI scaffolded controllers under `App\Http\Controllers\Auth\`
- Login at `/login`; all `/admin/*` routes require an authenticated session

### File Storage
- Room images and media stored via `Storage::disk('public')` → `storage/app/public/`
- Run `php artisan storage:link` to create the `public/storage` symlink

## Key Conventions
- **Room features** are stored as a JSON column: an array of `{name, icon}` objects where `icon` is a Font Awesome class (e.g., `fa-wifi`)
- **Settings** are always fetched via the `setting()` helper — never query `Setting` directly in views
- **Feedback approval** is a toggle; only approved feedback with rating ≥ 4 appears publicly on the home page
- **Favorites** are entirely client-side (localStorage); the `/api/get-favorite-rooms` endpoint only hydrates room data from stored IDs
- Admin routes use Laravel resource controllers where possible, with manual routes added for non-standard actions (e.g., media management, feedback approval toggle)

## Environment Variables (Key)
| Variable | Default | Purpose |
|---|---|---|
| `APP_ENV` | `local` | Environment name |
| `DB_CONNECTION` | `sqlite` | Database driver |
| `QUEUE_CONNECTION` | `database` | Queue driver |
| `CACHE_STORE` | `database` | Cache driver |
| `SESSION_DRIVER` | `database` | Session driver |
| `FILESYSTEM_DISK` | `local` | Default storage disk |

## Testing
- Framework: PHPUnit 11 via `php artisan test`
- Test directory: `tests/`
- Run with: `composer test` (clears config cache before running)
