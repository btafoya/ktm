# Kanban Task Manager - Project Documentation Index

**Project:** github.com/btafoya/ktm
**Version:** 1.0.0
**Last Updated:** 2026-02-26

---

## Quick Navigation

- [Architecture Overview](#architecture-overview)
- [Directory Structure](#directory-structure)
- [API Reference](#api-reference)
- [Database Schema](#database-schema)
- [Configuration](#configuration)
- [Development Guide](#development-guide)

---

## Architecture Overview

### Tech Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Backend | PHP | 8.4 |
| Framework | CodeIgniter | 4.7.0 |
| Database | PostgreSQL | 15+ |
| Frontend CSS | Bootstrap | 5.3 |
| Icons | Bootstrap Icons | 1.11.3 |
| JavaScript | jQuery | 4.0 |
| Drag & Drop | SortableJS | 1.15.2 |
| Rich Text Editor | TipTap | 2.2.4 |
| Testing | PHPUnit | 10.5 |

### MVC Structure

```
app/
├── Controllers/    # Request handling
├── Models/         # Database operations
├── Views/          # Templates
├── Filters/        # Middleware
└── Config/         # Configuration
```

---

## Directory Structure

### Application (`app/`)

| Path | Description |
|------|-------------|
| `Controllers/` | HTTP request controllers |
| `Models/` | Database models with business logic |
| `Views/` | HTML templates |
| `Filters/` | Request filters (auth, rate limiting) |
| `Config/` | Application configuration |
| `Database/Migrations/` | Database schema migrations |

### Public (`public/`)

| Path | Description |
|------|-------------|
| `assets/css/` | Stylesheets (Bootstrap, custom) |
| `assets/js/` | JavaScript (jQuery, SortableJS, TipTap) |
| `assets/images/` | Static images |
| `index.php` | Front controller |

### Writable (`writable/`)

| Path | Description |
|------|-------------|
| `cache/` | Application cache |
| `session/` | Session files |
| `uploads/` | User uploaded files |
| `logs/` | Application logs |

---

## API Reference

### Authentication

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/auth/login` | Login form |
| POST | `/auth/login` | Authenticate user |
| GET | `/auth/register` | Registration form |
| POST | `/auth/register` | Create account |
| GET | `/auth/logout` | Logout user |
| GET | `/auth/forgot-password` | Forgot password form |
| POST | `/auth/forgot-password` | Request reset link |
| GET | `/auth/reset-password` | Reset password form |
| POST | `/auth/reset-password` | Reset password |

### Boards

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/boards` | List user's boards | Yes |
| POST | `/boards/create` | Create new board | Yes |
| GET | `/boards/{id}` | Show board details | Yes |
| GET | `/boards/{id}/edit` | Edit board form | Yes |
| POST | `/boards/{id}/edit` | Update board | Yes |
| DELETE | `/boards/{id}` | Delete board | Yes |
| POST | `/boards/{id}/set-default` | Set as default board | Yes |
| POST | `/boards/{id}/reorder-columns` | Reorder columns | Yes |

### Columns

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| POST | `/columns` | Create column | Yes |
| PUT | `/columns/{id}` | Update column | Yes |
| DELETE | `/columns/{id}` | Delete column | Yes |

### Cards

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/cards/{id}` | Show card details | Yes |
| POST | `/cards` | Create card | Yes |
| PUT | `/cards/{id}` | Update card | Yes |
| DELETE | `/cards/{id}` | Delete card | Yes |
| POST | `/cards/move` | Move/reorder cards | Yes |

### Checklists

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| POST | `/checklists` | Create checklist item | Yes |
| POST | `/checklists/{id}/toggle` | Toggle completion | Yes |
| PUT | `/checklists/{id}` | Update item | Yes |
| DELETE | `/checklists/{id}` | Delete item | Yes |

### Tags

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/tags` | List user's tags | Yes |
| POST | `/tags` | Create tag | Yes |
| PUT | `/tags/{id}` | Update tag | Yes |
| DELETE | `/tags/{id}` | Delete tag | Yes |
| POST | `/tags/{id}/add-to-card/{cardId}` | Add tag to card | Yes |
| POST | `/tags/{id}/remove-from-card/{cardId}` | Remove tag from card | Yes |

### Attachments

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| POST | `/attachments/{cardId}/upload` | Upload file | Yes |
| GET | `/attachments/{id}/download` | Download file | Yes |
| DELETE | `/attachments/{id}` | Delete attachment | Yes |

### Google Integration

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/google/auth` | Initiate OAuth flow | Yes |
| GET | `/google/callback` | OAuth callback | Yes |
| GET | `/google/calendars` | List calendars | Yes |
| POST | `/google/sync-calendar` | Sync calendar | Yes |
| POST | `/google/{id}/toggle-sync` | Toggle sync | Yes |
| POST | `/google/disconnect` | Disconnect account | Yes |

### Gmail Integration

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/gmail/senders` | List sender rules | Yes |
| POST | `/gmail/senders` | Create sender rule | Yes |
| PUT | `/gmail/senders/{id}` | Update sender rule | Yes |
| DELETE | `/gmail/senders/{id}` | Delete sender rule | Yes |
| POST | `/gmail/webhook` | Gmail webhook endpoint | - |

### Settings

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/settings` | Settings page | Yes |
| POST | `/settings/update-profile` | Update profile | Yes |
| POST | `/settings/update-password` | Update password | Yes |

---

## Database Schema

### Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts |
| `boards` | Kanban boards |
| `columns` | Board columns |
| `cards` | Task cards |
| `checklist_items` | Card checklist items |
| `tags` | User-defined tags |
| `card_tags` | Card-tag associations |
| `attachments` | Card file attachments |
| `google_calendars` | Google calendar sync settings |
| `google_tokens` | OAuth tokens |
| `gmail_senders` | Gmail sender rules |
| `gmail_watches` | Gmail webhook subscriptions |
| `emails` | Linked emails |
| `jobs` | Background job queue |
| `password_resets` | Password reset tokens |

### Key Relationships

```
users (1) ----< (*) boards
boards (1) ----< (*) columns
columns (1) ----< (*) cards
cards (1) ----< (*) checklist_items
cards (1) ----< (*) attachments
cards (*) ----< (*) tags (via card_tags)
```

---

## Configuration

### Environment Variables (`.env`)

| Variable | Description | Default |
|----------|-------------|---------|
| `CI_ENVIRONMENT` | Environment mode | `development` |
| `app.baseURL` | Application base URL | `http://localhost:8080/` |
| `database.default.hostname` | Database host | `localhost` |
| `database.default.database` | Database name | `kanban_db` |
| `database.default.username` | Database user | `postgres` |
| `database.default.password` | Database password | `postgres` |
| `database.default.port` | Database port | `5432` |
| `database.default.DBDriver` | Database driver | `Postgre` |
| `session.driver` | Session driver | `file` |
| `session.savePath` | Session path | `writable/session` |
| `encryption.key` | Encryption key (32 bytes) | Required |
| `google.client.id` | Google OAuth client ID | Required |
| `google.client.secret` | Google OAuth secret | Required |
| `gmail.webhook.secret` | Gmail webhook secret | Required |

---

## Development Guide

### Setting Up

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Configure environment:**
   ```bash
   cp env.example .env
   # Edit .env with your settings
   ```

3. **Run migrations:**
   ```bash
   php spark migrate
   ```

4. **Start development server:**
   ```bash
   php spark serve
   ```

### Running Tests

```bash
phpunit
```

### CodeIgniter CLI

```bash
php spark                    # List all commands
php spark migrate            # Run migrations
php spark migrate:rollback   # Rollback migrations
php spark db:seed            # Run seeders
php spark cache:clear        # Clear cache
php spark routes             # List all routes
php spark serve              # Start dev server
```

---

## Project Files Index

### Controllers (13 files)

| File | Purpose |
|------|---------|
| `BaseController.php` | Base controller with helpers |
| `HomeController.php` | Home page |
| `AuthController.php` | Authentication flows |
| `BoardController.php` | Board CRUD operations |
| `ColumnController.php` | Column operations |
| `CardController.php` | Card operations |
| `ChecklistController.php` | Checklist operations |
| `TagController.php` | Tag operations |
| `AttachmentController.php` | File operations |
| `GoogleController.php` | Google OAuth integration |
| `GmailController.php` | Gmail integration |
| `SettingsController.php` | User settings |

### Models (15 files)

| File | Purpose |
|------|---------|
| `UserModel.php` | User authentication |
| `BoardModel.php` | Board queries |
| `ColumnModel.php` | Column queries |
| `CardModel.php` | Card queries |
| `ChecklistItemModel.php` | Checklist queries |
| `TagModel.php` | Tag queries |
| `AttachmentModel.php` | File queries |
| `GoogleCalendarModel.php` | Calendar sync |
| `GoogleTokenModel.php` | OAuth token storage |
| `GmailSenderModel.php` | Sender rules |
| `GmailWatchModel.php` | Webhook subscriptions |
| `EmailModel.php` | Linked emails |
| `JobModel.php` | Job queue |
| `PasswordResetModel.php` | Reset tokens |

### Filters (3 files)

| File | Purpose |
|------|---------|
| `AuthFilter.php` | Session authentication |
| `ApiAuthFilter.php` | API authentication |
| `RateLimitFilter.php` | Request rate limiting |

### Views (17 files)

| Directory | Files |
|-----------|-------|
| `layouts/` | `main.php` |
| `auth/` | `login.php`, `register.php`, `forgot_password.php`, `reset_password.php` |
| `boards/` | `index.php`, `show.php`, `create.php`, `edit.php` |
| `cards/` | `show.php`, `_card.php` |
| `emails/` | `password_reset.php` |
| `settings/` | `index.php` |

### Migrations (15 files)

| Migration | Table |
|-----------|-------|
| `000001_CreateUsersTable.php` | `users` |
| `000002_CreateBoardsTable.php` | `boards` |
| `000003_CreateColumnsTable.php` | `columns` |
| `000004_CreateCardsTable.php` | `cards` |
| `000005_CreateChecklistItemsTable.php` | `checklist_items` |
| `000006_CreateTagsTable.php` | `tags` |
| `000007_CreateCardTagsTable.php` | `card_tags` |
| `000008_CreateAttachmentsTable.php` | `attachments` |
| `000009_CreateGoogleCalendarsTable.php` | `google_calendars` |
| `000010_CreateGoogleTokensTable.php` | `google_tokens` |
| `000011_CreateGmailSendersTable.php` | `gmail_senders` |
| `000012_CreateGmailWatchesTable.php` | `gmail_watches` |
| `000013_CreateEmailsTable.php` | `emails` |
| `000014_CreateJobsTable.php` | `jobs` |
| `000015_CreatePasswordResetsTable.php` | `password_resets` |

---

## External Documentation

- [CodeIgniter 4 User Guide](https://codeigniter.com/userguide/)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [SortableJS Documentation](https://sortablejs.github.io/Sortable/)
- [TipTap Documentation](https://tiptap.dev/)

---

**End of Documentation Index**