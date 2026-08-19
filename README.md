# Brickspoint Hotel — Dynamic Hotel Booking Website

A modern, full-featured hotel management and booking website for **Brickspoint Hotel**, located in Wuse, Abuja, Nigeria. Built with Laravel 12, this application serves as both a public-facing hotel website and a protected admin panel for hotel staff — designed from the ground up to support **PMS (Property Management System) integration** via API or act as a standalone **booking engine** for various PMS platforms.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Database Schema](#database-schema)
- [Frontend Features](#frontend-features)
- [Admin Panel](#admin-panel)
- [Public API Endpoints](#public-api-endpoints)
- [PMS Integration Roadmap](#pms-integration-roadmap)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [Environment Variables](#environment-variables)
- [Development](#development)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

---

## Project Overview

Brickspoint Hotel Website is a dynamic, content-driven hotel website that provides:

- **Public-facing website** for guests to explore rooms, view the hotel gallery, browse local attractions, submit feedback, and contact the hotel.
- **Admin panel** for hotel staff to manage all content — rooms, media, gallery, settings, attractions, guest feedback, contact messages, WhatsApp leads, and food menus.
- **Booking engine foundation** — built with a modular architecture and REST-ready API endpoints to integrate with external PMS systems (e.g., RoomRaccoon, Cloudbeds, Hotelogix, eZee, etc.) or serve as a white-label booking engine.

The application uses **SQLite** for development with easy switching to MySQL/PostgreSQL for production.

---

## Key Features

### Guest-Facing Features

| Feature | Description |
|---|---|
| **Room Showcase** | Browse all rooms with pricing, guest capacity, features, and media gallery (images + videos) |
| **Room Details** | Full room page with description, amenities list (with icons), pricing, and media carousel |
| **Photo Gallery** | Hotel-wide image gallery with responsive grid layout |
| **Local Guide** | Nearby attractions (restaurants, shopping, landmarks) grouped by category |
| **Favorites** | Save favorite rooms to localStorage (client-side, no login required) |
| **Guest Feedback** | Submit star ratings (1–5) and reviews via modal or standalone page |
| **Contact Form** | Send messages directly to hotel management |
| **WhatsApp Lead Capture** | Click-to-chat with lead tracking (name, phone, IP) |
| **Food Menu** | View hotel restaurant menu as a PDF with book-style page rendering |
| **SEO Optimized** | JSON-LD structured data, Open Graph meta tags, semantic HTML |
| **Responsive Design** | Mobile-first layout with hamburger menu, accordion navigation |
| **Google Maps** | Embedded map showing hotel location in Wuse, Abuja |

### Admin Features

| Feature | Description |
|---|---|
| **Dashboard** | Real-time stats — rooms, gallery images, unread messages, leads, feedback, 7-day visitor chart |
| **Room Management** | Full CRUD for rooms with pricing, capacity, features, and image upload |
| **Room Media** | Upload multiple images/videos per room, auto-detect media type |
| **Gallery Management** | Multi-image upload for hotel-wide photo gallery |
| **Settings Manager** | Key-value settings store (text, image, video, file, number) with cache |
| **Attraction Management** | CRUD for local guide attractions with category grouping |
| **Contact Inbox** | View, mark as read, and manage contact form submissions |
| **Feedback Management** | Review, approve/reject, and delete guest feedback |
| **WhatsApp Leads** | View and manage captured WhatsApp leads with last-visit tracking |
| **Food Menu** | Upload and manage restaurant menu PDF |
| **Visitor Analytics** | Track unique daily visitors by IP with 7-day chart |

### PMS Integration Readiness

| Capability | Description |
|---|---|
| **REST API Ready** | Modular controller architecture designed for API layer addition |
| **Booking Engine Mode** | Can serve as a booking widget嵌入able in external PMS platforms |
| **Room Sync** | Room data structure supports external sync (price, availability, features) |
| **Lead Pipeline** | WhatsApp leads and contact forms can feed into PMS CRM |
| **JSON Settings** | Key-value settings store supports dynamic configuration from PMS |
| **Queue System** | Database-backed queue ready for async PMS webhook processing |
| **Scalable Auth** | Laravel Sanctum/Passport can be added for API authentication |

---

## Tech Stack

### Backend
- **PHP 8.2+**
- **Laravel 12** — Full-stack framework
- **Laravel UI** — Auth scaffolding (Bootstrap-based)
- **Eloquent ORM** — Database abstraction

### Frontend
- **Blade Templates** — Server-side rendering
- **Tailwind CSS 4** — Utility-first CSS (via CDN + Vite)
- **Bootstrap 5** — UI components (auth pages)
- **Font Awesome** — Icon library
- **Chart.js** — Visitor analytics charts
- **PDF.js** — PDF rendering for food menu
- **Vite** — Asset bundling with HMR
- **SASS** — CSS preprocessing

### Database & Infrastructure
- **SQLite** (default/development)
- **MySQL / PostgreSQL** (production-ready)
- **Database-backed** session, cache, and queue
- **Laravel Queue** — Background job processing
- **Laravel Cache** — Forever-cached settings helper

### Development Tools
- **PHPUnit 11** — Testing framework
- **Laravel Pint** — Code style fixer
- **Laravel Pail** — Real-time log viewer
- **Laravel Sail** — Docker development environment
- **Faker** — Test data generation
- **Concurrently** — Parallel dev server execution

---

## Architecture

### Request Lifecycle

```
HTTP Request
    │
    ▼
LogVisitor Middleware ─── Records unique visitor (IP + date)
    │
    ▼
Route Dispatcher ─────── Matches URI to controller
    │
    ▼
Controller ───────────── Fetches data via Eloquent
    │
    ▼
Blade View ───────────── Renders HTML response
    │
    ▼
JSON-LD / SEO Tags ───── Injected in layout
    │
    ▼
HTTP Response
```

### Design Patterns

- **MVC Architecture** — Models, Controllers, and Blade Views separated
- **Resource Controllers** — Standard CRUD for rooms, settings, attractions
- **Service Helper** — `setting()` global function with forever-cache
- **Repository-like Models** — Eloquent models with relationships, casts, and fillable attributes
- **Middleware Pipeline** — Visitor logging applied globally
- **CSRF Protection** — Honeypot fields on all forms (contact, feedback, registration, password reset)
- **Client-Side State** — Favorites stored in localStorage with AJAX hydration

---

## Database Schema

### Core Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `users` | Admin/user accounts | name, email, password, email_verified_at |
| `rooms` | Hotel rooms | name, description, price, guests, image, features (JSON) |
| `room_media` | Room images/videos | room_id (FK), file_path, type (image/video) |
| `galleries` | Hotel-wide gallery | path, alt_text |
| `settings` | Key-value config | key (unique), value, type (text/image/video/file/number) |
| `attractions` | Local guide entries | name, category, description, image |
| `contacts` | Contact form messages | name, email, message, is_read |
| `feedbacks` | Guest reviews | name, email, rating, message, is_read, is_approved |
| `whatsapp_leads` | WhatsApp click leads | name, phone, ip_address |
| `visitors` | Daily unique visitors | ip_address, visited_date (unique per IP per day) |

### Infrastructure Tables

| Table | Purpose |
|---|---|
| `sessions` | Database-driven sessions |
| `cache` / `cache_locks` | Database-driven cache |
| `jobs` / `job_batches` / `failed_jobs` | Queue infrastructure |
| `password_reset_tokens` | Password reset flow |

### Room Features Format

Room features are stored as a JSON array of objects:

```json
[
  {"name": "Free WiFi", "icon": "fa-wifi"},
  {"name": "Air Conditioning", "icon": "fa-snowflake"},
  {"name": "TV", "icon": "fa-tv"}
]
```

---

## Frontend Features

### Homepage (`/`)
- Hero section with dynamic media (image/video via settings)
- Featured rooms carousel (top 3 by latest)
- Guest testimonials (approved, rating ≥ 4)
- Contact form with honeypot spam protection
- Google Maps embed
- WhatsApp lead capture modal

### Rooms (`/rooms`)
- Grid listing of all rooms with image, name, price, and guest capacity
- Link to individual room detail pages

### Room Detail (`/rooms/{room}`)
- Full room description and pricing
- Amenity icons list
- Media gallery (images + videos) powered by RoomMedia
- WhatsApp inquiry button

### Gallery (`/gallery`)
- Responsive image grid of hotel photos
- Lightbox-style viewing

### Local Guide (`/local-guide`)
- Attractions grouped by category (Food, Shopping, Landmarks, etc.)
- Image and description for each attraction

### Favorites (`/favorites`)
- Client-side room favoriting via localStorage
- AJAX hydration to display saved room data
- Badge count in navigation

### Food Menu (`/menu`)
- PDF-based menu rendered with PDF.js
- Book-style page navigation with thumbnails

### Feedback (`/feedback`)
- Star rating system (1–5)
- Optional name and email
- Honeypot spam protection
- AJAX and standard form submission support

---

## Admin Panel

### Access
- **URL:** `/login`
- **All routes** under `/admin/*` require authentication
- **Default seed user:** `test@example.com` (create via `php artisan db:seed`)

### Dashboard (`/admin/dashboard`)
- Room count, gallery count, unread messages, WhatsApp leads
- Approved/unread feedback counts
- 7-day unique visitor chart (Chart.js)

### Room Management (`/admin/rooms`)
- Create, edit, delete rooms
- Set name, description, price, guest capacity, featured image
- Select amenities from predefined feature list (stored as JSON)
- Upload multiple media files (images + videos) per room
- Delete individual media items

### Gallery (`/admin/gallery`)
- Multi-image upload (images only, max 5MB each)
- Grid view with delete capability

### Settings (`/admin/settings`)
- Key-value store with type support: text, image, video, file, number
- Auto-cache invalidation on changes
- Used for hero media, food menu PDF, and dynamic site config

### Attractions (`/admin/attractions`)
- CRUD for local guide entries
- Category-based grouping
- Image upload per attraction

### Contact Messages (`/admin/contacts`)
- Paginated inbox with unread indicators
- Mark as read on view
- Delete capability

### Guest Feedback (`/admin/feedback`)
- View all submitted feedback with star ratings
- Toggle approval status (approved feedback shows on homepage)
- Mark as read, delete

### WhatsApp Leads (`/admin/whatsapp-leads`)
- View all captured leads with name, phone, IP
- Sortable columns
- Last visit date (joined from visitors table via IP)

### Food Menu (`/admin/menu`)
- Upload PDF (max 10MB)
- Preview via iframe
- Replace or delete menu

---

## Public API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/` | Homepage |
| `GET` | `/rooms` | All rooms listing |
| `GET` | `/rooms/{room}` | Single room detail |
| `GET` | `/gallery` | Photo gallery |
| `GET` | `/local-guide` | Local attractions |
| `GET` | `/favorites` | Favorites page |
| `POST` | `/api/get-favorite-rooms` | Hydrate room data by IDs (JSON) |
| `POST` | `/contact` | Submit contact form |
| `POST` | `/log-whatsapp-lead` | Log WhatsApp click (JSON) |
| `GET` | `/feedback` | Feedback form |
| `POST` | `/feedback` | Submit feedback (JSON or form) |
| `GET` | `/menu` | Food menu page |

---

## PMS Integration Roadmap

This application is designed to be extended as a **booking engine** or **PMS connector**. Below is the planned integration architecture:

### Phase 1: API Layer
- [ ] Add **Laravel Sanctum** for API token authentication
- [ ] Create RESTful API controllers for rooms, bookings, availability
- [ ] Implement JSON API endpoints mirroring Blade controller logic
- [ ] Add rate limiting and request validation

### Phase 2: Booking Engine
- [ ] Create `Booking` model with check-in/check-out dates, guest info, status
- [ ] Implement real-time room availability checking
- [ ] Add date-based pricing (seasonal rates)
- [ ] Integrate payment gateway (Paystack for Nigeria, Stripe for international)
- [ ] Booking confirmation emails and notifications

### Phase 3: PMS Integration
- [ ] **Webhook endpoints** — Receive room updates, rate changes, availability from PMS
- [ ] **Outbound sync** — Push bookings back to PMS (RoomRaccoon, Cloudbeds, eZee, etc.)
- [ ] **Channel manager support** — OTA sync (Booking.com, Expedia, Hotels.ng)
- [ ] **iCal sync** — Import/export availability via iCal format
- [ ] **API documentation** — OpenAPI/Swagger spec for third-party integrations

### Phase 4: Booking Widget
- [ ] Embeddable JavaScript widget for external websites
- [ ] Iframe-based booking flow
- [ ] White-label customization (colors, logo, domain)

### Supported PMS Platforms (Target)
| Platform | Integration Type |
|---|---|
| RoomRaccoon | API + Webhook |
| Cloudbeds | API + Channel Manager |
| Hotelogix | API |
| eZee | API + iCal |
| Mews | API + Webhook |
| Opera (Oracle) | API (via OHIP) |
| Custom PMS | REST API + Webhook |

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- SQLite (default) or MySQL/PostgreSQL

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd hotel-booking

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Create storage symlink (for uploaded files)
php artisan storage:link

# Seed default admin user (optional)
php artisan db:seed

# Start development server
composer run dev
```

### Production Build

```bash
# Build frontend assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run queue worker (if using database queue)
php artisan queue:work --sleep=3 --tries=3
```

---

## Project Structure

```
hotel-booking/
├── app/
│   ├── Helpers/
│   │   └── SettingsHelper.php          # Global setting() helper function
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       │   ├── AdminController.php          # Dashboard with stats
│   │       │   ├── AttractionController.php     # Local guide CRUD
│   │       │   ├── ContactController.php        # Contact message inbox
│   │       │   ├── FeedbackController.php       # Feedback management
│   │       │   ├── GalleryController.php        # Hotel gallery management
│   │       │   ├── MenuController.php           # Food menu PDF management
│   │       │   ├── RoomController.php           # Room CRUD + media management
│   │       │   ├── SettingController.php        # Key-value settings
│   │       │   └── WhatsappLeadController.php   # WhatsApp lead management
│   │       ├── Auth/                            # Laravel UI auth controllers
│   │       ├── Controller.php                   # Base controller
│   │       ├── FeedbackController.php           # Public feedback form + admin
│   │       ├── HomeController.php               # Legacy dashboard
│   │       ├── LeadController.php               # WhatsApp lead capture
│   │       ├── PageController.php               # All public-facing pages
│   │       └── ProfileController.php            # User profile management
│   ├── Middleware/
│   │   └── LogVisitor.php                       # Visitor tracking middleware
│   ├── Models/
│   │   ├── Attraction.php
│   │   ├── Contact.php
│   │   ├── Feedback.php
│   │   ├── Gallery.php
│   │   ├── Room.php
│   │   ├── RoomMedia.php
│   │   ├── Setting.php
│   │   ├── User.php
│   │   ├── Visitor.php
│   │   └── WhatsappLead.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/                    # 13 migration files (19 tables)
│   └── seeders/
│       └── DatabaseSeeder.php         # Default test user
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   ├── sass/
│   │   ├── _variables.scss
│   │   └── app.scss
│   └── views/
│       ├── admin/                     # 18 admin blade templates
│       ├── auth/                      # 6 authentication templates
│       ├── components/                # 6 reusable Blade components
│       ├── layouts/
│       │   ├── app.blade.php          # Public layout (349 lines)
│       │   ├── admin.blade.php        # Admin layout with sidebar
│       │   └── guest.blade.php        # Minimal auth layout
│       ├── partials/
│       │   └── _feedback_form_content.blade.php
│       ├── welcome.blade.php          # Homepage
│       ├── rooms.blade.php
│       ├── room-details.blade.php
│       ├── gallery.blade.php
│       ├── local-guide.blade.php
│       ├── favorites.blade.php
│       ├── feedback.blade.php
│       └── menu.blade.php
├── routes/
│   ├── web.php                        # All web routes
│   └── console.php                    # Artisan console routes
├── tests/                             # PHPUnit test suite
├── composer.json
├── package.json
├── vite.config.js
└── .env.example
```

---

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `Laravel` | Application name |
| `APP_ENV` | `local` | Environment (local, production) |
| `APP_DEBUG` | `true` | Debug mode |
| `APP_URL` | `http://localhost` | Application URL |
| `DB_CONNECTION` | `sqlite` | Database driver |
| `SESSION_DRIVER` | `database` | Session storage driver |
| `SESSION_LIFETIME` | `120` | Session timeout (minutes) |
| `QUEUE_CONNECTION` | `database` | Queue driver |
| `CACHE_STORE` | `database` | Cache driver |
| `FILESYSTEM_DISK` | `local` | Default storage disk |
| `MAIL_MAILER` | `log` | Mail driver (log = dev mode) |

---

## Development

### Running in Development

```bash
# Start all services concurrently (server, queue, logs, Vite)
composer run dev
```

This starts:
- `php artisan serve` — Laravel dev server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Real-time log viewer
- `npm run dev` — Vite with HMR

### Key Development Notes

- **Settings cache:** After changing settings in the database, run `php artisan cache:clear`
- **Storage link:** Run `php artisan storage:link` to serve uploaded files
- **SQLite:** No additional database setup required for development
- **CSRF Honeypot:** All forms include honeypot anti-spam fields — do not remove them
- **Favorites:** Entirely client-side (localStorage) — no server state for favorites

---

## Testing

```bash
# Run full test suite
composer test

# Or directly
php artisan test
```

Tests are located in the `tests/` directory and use PHPUnit 11.

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style
- Follow PSR-12 coding standards
- Use `laravel/pint` for automated formatting
- Write meaningful commit messages

---

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## Acknowledgments

- Built with [Laravel 12](https://laravel.com)
- UI powered by [Tailwind CSS](https://tailwindcss.com) and [Bootstrap](https://getbootstrap.com)
- Icons by [Font Awesome](https://fontawesome.com)
- Charts by [Chart.js](https://www.chartjs.org)
- PDF rendering by [PDF.js](https://mozilla.github.io/pdf.js/)
