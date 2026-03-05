# Project Index: Kanban Task Manager (KTM)

**Generated:** 2026-03-05

## Project Overview

A personal task management web application combining kanban board functionality with Google Calendar appointment display and Gmail integration.

**Tech Stack:** PHP 8.4, CodeIgniter 4.7, PostgreSQL 15, Bootstrap 5.3

---

## Directory Structure

```
kanban-tasks-calendar/
├── app/
│   ├── Commands/        # CLI commands (7 files)
│   ├── Config/          # Application configuration (40+ files)
│   ├── Controllers/     # HTTP request handlers (13 files)
│   ├── Database/
│   │   └── Migrations/  # Database migrations (17 files)
│   ├── Filters/         # Middleware (3 files)
│   ├── Language/        # Translations
│   ├── Models/          # Database models (14 files)
│   ├── Services/        # Business logic (4 files)
│   └── Views/           # HTML templates (19 files)
├── docs/                # Documentation
├── public/
│   └── assets/          # CSS, JS, fonts
├── tests/               # PHPUnit tests
├── vendor/              # Composer dependencies
├── writable/            # Runtime: cache, logs, uploads, session
├── .env                 # Environment config (from .env.example)
└── spark                # CodeIgniter CLI entry point
```

---

## Entry Points

| Entry Point | Path | Purpose |
|-------------|------|---------|
| HTTP Server | `public/index.php` | Main front controller for web requests |
| CLI | `spark` | Command-line interface tool |
| Development Server | `php spark serve` | Built-in PHP development server |

---

## Controllers (13 files)

| Controller | Purpose | Key Routes |
|------------|---------|------------|
| `AuthController` | Login, register, password reset | `/auth/*` |
| `BaseController` | Base class with common functionality | - |
| `BoardController` | Board CRUD operations | `/boards/*` |
| `CardController` | Card CRUD, move operations | `/cards/*` |
| `ChecklistController` | Checklist item management | `/checklists/*` |
| `ColumnController` | Column CRUD, reordering | `/columns/*` |
| `GoogleController` | Google OAuth, calendar sync | `/google/*` |
| `GmailController` | Gmail senders, sync, webhooks | `/gmail/*` |
| `SettingsController` | User settings, profile | `/settings/*` |
| `TagController` | Tag management, filtering | `/tags/*` |
| `AttachmentController` | File upload/download | `/attachments/*` |
| `HomeController` | Dashboard, board selection | `/` |
| `Home` | Legacy welcome page | - |

---

## Models (14 files)

| Model | Table | Purpose |
|-------|-------|---------|
| `UserModel` | `users` | User accounts |
| `BoardModel` | `boards` | Kanban boards |
| `ColumnModel` | `columns` | Board columns |
| `CardModel` | `cards` | Task cards |
| `ChecklistItemModel` | `checklist_items` | Card checklists |
| `TagModel` | `tags` | User-defined tags |
| `AttachmentModel` | `attachments` | File attachments |
| `GoogleCalendarModel` | `google_calendars` | Calendar sync settings |
| `GoogleTokenModel` | `google_tokens` | OAuth tokens |
| `GmailSenderModel` | `gmail_senders` | Email sender rules |
| `GmailWatchModel` | `gmail_watches` | Webhook subscriptions |
| `EmailModel` | `emails` | Linked emails |
| `JobModel` | `jobs` | Background job queue |
| `PasswordResetModel` | `password_resets` | Reset tokens |

---

## Services (4 files)

| Service | Purpose |
|---------|---------|
| `GoogleAuthService` | OAuth token management, refresh |
| `CalendarSyncService` | Google Calendar event sync |
| `GmailSyncService` | Email processing, watch management |
| `JobService` | Job queue dispatch and processing |

---

## CLI Commands (7 files)

| Command | Description | Usage |
|---------|-------------|-------|
| `JobWorker` | Process jobs from queue | `php spark jobs:work [--daemon]` |
| `SyncGmail` | Sync Gmail emails | `php spark sync:gmail [--user-id=1]` |
| `SyncCalendar` | Sync Calendar events | `php spark sync:calendar --all` |
| `RefreshTokens` | Refresh expired tokens | `php spark tokens:refresh` |
| `SendReminders` | Send due date reminders | `php spark reminders:send` |
| `CleanupJobs` | Clean old completed jobs | `php spark jobs:cleanup --days=30` |
| `RenewGmailWatches` | Renew expired watches | `php spark gmail:renew-watches` |

---

## Filters (3 files)

| Filter | Purpose | Usage |
|--------|---------|-------|
| `AuthFilter` | Require authenticated user | `'filter' => 'auth'` |
| `ApiAuthFilter` | API authentication | API routes |
| `RateLimitFilter` | Rate limiting | Login/register |

---

## Database Migrations (17 files)

| ID | Migration | Purpose |
|----|-----------|---------|
| 000001 | CreateUsersTable | User accounts |
| 000002 | CreateBoardsTable | Kanban boards |
| 000003 | CreateColumnsTable | Board columns |
| 000004 | CreateCardsTable | Task cards |
| 000005 | CreateChecklistItemsTable | Card checklists |
| 000006 | CreateTagsTable | Tags |
| 000007 | CreateCardTagsTable | Many-to-many card tags |
| 000008 | CreateAttachmentsTable | File attachments |
| 000009 | CreateGoogleCalendarsTable | Calendar sync |
| 000010 | CreateGoogleTokensTable | OAuth tokens |
| 000011 | CreateGmailSendersTable | Email sender rules |
| 000012 | CreateGmailWatchesTable | Webhook watches |
| 000013 | CreateEmailsTable | Linked emails |
| 000014 | CreateJobsTable | Background jobs |
| 000015 | CreatePasswordResetsTable | Reset tokens |
| 000016 | AddExternalFieldsToCardsTable | Calendar/Gmail IDs |
| 000017 | AddCardBodyField | Rich text body |

---

## Views (19 files)

| Path | Purpose |
|------|---------|
| `layouts/main.php` | Main layout template |
| `auth/*.php` | Login, register, password reset |
| `boards/*.php` | Board list, create, edit, show |
| `cards/*.php` | Card detail, card partial |
| `emails/*.php` | Email templates |
| `settings/*.php` | User settings |
| `errors/` | Error pages |

---

## Frontend Assets

### CSS
- `bootstrap.min.css` - Bootstrap 5.3 framework
- `bootstrap-icons.css` - Bootstrap Icons 1.11.3
- `kanban.css` - Custom styles, dark theme

### JavaScript
- `jquery.min.js` - jQuery 4.0
- `bootstrap.bundle.min.js` - Bootstrap JS
- `sortable.min.js` - SortableJS 1.15.2 (drag-drop)
- `marked.min.js` - Markdown parser
- `turndown.min.js` - HTML to Markdown
- `tiptap/*.js` - TipTap 2.2.4 rich text editor
- `app.js` - Application JS

---

## Configuration

| File | Purpose |
|------|---------|
| `.env` | Environment variables (database, Google API keys) |
| `app/Config/Routes.php` | URL routing |
| `app/Config/Filters.php` | Middleware configuration |
| `app/Config/App.php` | Base URL, timezone |
| `app/Config/Database.php` | Database connection |

---

## Documentation

| File | Purpose |
|------|---------|
| `PROJECT_INDEX.md` | This file - quick reference |
| `README.md` | Installation and usage guide |
| `SCOPE.md` | Requirements specification |
| `DESIGN.md` | System design |
| `WORKFLOW.md` | Implementation workflow (75% complete) |
| `CLAUDE.md` | Project instructions for Claude |
| `docs/INDEX.md` | Documentation index |
| `docs/BACKGROUND_JOBS.md` | Background jobs setup |
| `docs/crontab.example` | Crontab configuration |
| `docs/supervisor.conf` | Supervisor configuration |
| `.env.example` | Environment template |

---

## Key Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `codeigniter4/framework` | ^4.7 | Main framework |
| `google/apiclient` | ^2.15 | Google API integration |
| `phpunit/phpunit` | ^10.5 | Testing framework |

---

## Google Integration Requirements

### Environment Variables (.env)
```ini
google.client.id = your_client_id
google.client.secret = your_client_secret
google.redirect.uri = http://yourdomain.com/google/callback
gmail.pubsub.topic = projects/your-project/topics/your-topic
gmail.webhook.secret = your_random_secret
```

### OAuth Scopes
- `https://www.googleapis.com/auth/calendar` - Calendar access
- `https://www.googleapis.com/auth/gmail.readonly` - Gmail read access

---

## Quick Start

1. **Install dependencies:** `composer install`
2. **Configure environment:** `cp .env.example .env` and edit
3. **Run migrations:** `php spark migrate`
4. **Start server:** `php spark serve`
5. **Open browser:** `http://localhost:8080`

---

## Background Jobs Setup

### Supervisor (recommended)
```bash
sudo cp docs/supervisor.conf /etc/supervisor/conf.d/kanban-worker.conf
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start kanban-worker
```

### Crontab
```bash
# Copy and edit crontab
crontab -e
# Add entries from docs/crontab.example
```

---

## Implementation Progress

**Overall:** 75% (6 of 8 phases complete)

| Phase | Status |
|-------|--------|
| Phase 0: Prerequisites & Setup | Complete |
| Phase 1: Foundation Layer | Complete |
| Phase 2: Core Kanban Features | Complete |
| Phase 3: Frontend & UI | Complete |
| Phase 4: Google Integration | Complete |
| Phase 5: Background Jobs | Complete |
| Phase 6: Mobile & Polish | In Progress (13%) |
| Phase 7: Deployment | Pending |

---

## Testing

```bash
phpunit                    # Run all tests
php spark test              # Alternative test command
```

---

## Git Repository

- **Remote:** `https://github.com/btafoya/ktm.git`
- **Main Branch:** `main`
- **Recent Work:** Phase 5 Background Jobs (commit: 6841679)