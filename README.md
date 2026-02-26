# Kanban Task Manager (KTM)

![PHP](https://img.shields.io/badge/PHP-8.4+-blue?logo=php)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.7.0-red?logo=codeigniter)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue?logo=postgresql)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-In%20Progress-yellow)

A personal task management web application combining kanban board functionality with Google Calendar appointment display and Gmail integration. Provides a unified interface for managing tasks alongside scheduled events and important emails.

## Features

### Core Kanban
- Create and manage multiple kanban boards
- Fully dynamic columns (add, rename, reorder, delete)
- Drag-and-drop card management between columns
- Card features:
  - Rich text descriptions with Markdown storage
  - Priority levels (Low, Medium, High)
  - Due dates with overdue indicators
  - Tags/labels for organization
  - File attachments
  - Checklists with progress tracking

### Google Calendar Integration
- OAuth 2.0 authentication
- Connect multiple Google calendars
- Display calendar events as cards
- Optional calendar sync to specific boards

### Gmail Integration
- Configure sender rules for automatic task creation
- Match emails by sender and subject keywords
- Automatic card creation for matching emails
- Link to original Gmail messages

### User Experience
- Dark theme only (AsteroAdmin style with Bootstrap 5.3)
- Responsive design with mobile offcanvas navigation
- Session-based authentication
- Password reset via email

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.4+ |
| Framework | CodeIgniter 4.7.0 |
| Database | PostgreSQL 15+ |
| Frontend CSS | Bootstrap 5.3 |
| Icons | Bootstrap Icons 1.11.3 |
| JavaScript | jQuery 4.0 |
| Drag & Drop | SortableJS 1.15.2 |
| Rich Text Editor | TipTap 2.2.4 |

## Installation

### Prerequisites

- PHP 8.4 or higher
- PostgreSQL 15 or higher
- Composer 2.x

### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/btafoya/ktm.git
   cd ktm
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create environment file:
   ```bash
   cp env .env
   ```

4. Configure `.env` with your database settings:
   ```ini
   database.default.hostname = localhost
   database.default.database = kanban_db
   database.default.username = postgres
   database.default.password = your_password
   database.default.port = 5432
   database.default.DBDriver = Postgre
   ```

5. Run migrations:
   ```bash
   php spark migrate
   ```

6. Start development server:
   ```bash
   php spark serve
   ```

7. Open browser: `http://localhost:8080`

## Database Schema

The application uses the following tables:

- `users` - User accounts
- `boards` - Kanban boards
- `columns` - Board columns
- `cards` - Task cards
- `checklist_items` - Card checklist items
- `tags` - User-defined tags
- `card_tags` - Card-tag associations
- `attachments` - File attachments
- `google_calendars` - Google calendar sync settings
- `google_tokens` - OAuth tokens
- `gmail_senders` - Gmail sender rules
- `gmail_watches` - Gmail webhook subscriptions
- `emails` - Linked emails
- `jobs` - Background job queue
- `password_resets` - Password reset tokens

## Configuration

### Google Integration

To enable Google Calendar and Gmail integration:

1. Create a Google Cloud project with OAuth 2.0 credentials
2. Enable Calendar API and Gmail API
3. Add the client ID and secret to `.env`:
   ```ini
   google.client.id = your_google_client_id
   google.client.secret = your_google_client_secret
   google.redirect.uri = http://localhost:8080/google/callback
   ```

### Encryption

Generate a secure 32-byte random key for encryption and add it to `.env`:

```ini
encryption.key = your-generated-key-here
```

#### Methods to generate the key:

**Using PHP:**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

**Using OpenSSL:**
```bash
openssl rand -hex 32
```

**Using Python:**
```bash
python3 -c "import secrets; print(secrets.token_hex(32))"
```

**Using Node.js:**
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'));"
```

Any of these commands will output a 64-character hexadecimal string (32 bytes), which is the required format for CodeIgniter's encryption key.

## Development

### Running Tests

```bash
phpunit
```

### CodeIgniter CLI

```bash
php spark list              # List all commands
php spark migrate            # Run migrations
php spark migrate:rollback   # Rollback migrations
php spark cache:clear        # Clear cache
php spark routes             # List all routes
```

## Project Structure

```
kanban-tasks-calendar/
├── app/
│   ├── Controllers/     # HTTP request handlers
│   ├── Models/          # Database models
│   ├── Views/           # HTML templates
│   ├── Filters/         # Middleware
│   ├── Config/          # Application configuration
│   └── Database/
│       └── Migrations/  # Database schema migrations
├── public/
│   ├── assets/
│   │   ├── css/         # Stylesheets
│   │   ├── js/          # JavaScript
│   │   └── images/      # Static images
│   └── index.php         # Front controller
├── writable/
│   ├── cache/           # Application cache
│   ├── session/         # Session files
│   ├── uploads/         # User uploads
│   └── logs/            # Application logs
└── vendor/              # Composer dependencies
```

## Documentation

- [Project Documentation Index](docs/INDEX.md) - Comprehensive project documentation
- [SCOPE.md](SCOPE.md) - Requirements specification
- [DESIGN.md](DESIGN.md) - System design
- [WORKFLOW.md](WORKFLOW.md) - Implementation workflow with progress tracking

## Status

Current implementation progress: **54%** (68 of 126 tasks complete)

| Phase | Status |
|-------|--------|
| Phase 0: Prerequisites & Setup | Complete |
| Phase 1: Foundation Layer | Complete |
| Phase 2: Core Kanban Features | Complete |
| Phase 3: Frontend & UI | In Progress (74%) |
| Phase 4: Google Integration | Pending |
| Phase 5: Background Jobs | Pending |
| Phase 6: Mobile & Polish | Pending |
| Phase 7: Deployment | Pending |

See [WORKFLOW.md](WORKFLOW.md) for detailed progress tracking.

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

MIT License - see LICENSE file for details.

## Author

btafoya - [github.com/btafoya](https://github.com/btafoya)

---

Built with [CodeIgniter 4](https://codeigniter.com)