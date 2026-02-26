# Kanban Task Manager - System Architecture Design

**Document Version:** 1.0
**Date:** 2026-02-25
**Status:** Design Complete - Ready for Implementation

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [System Components](#2-system-components)
3. [Directory Structure](#3-directory-structure)
4. [Database Schema](#4-database-schema)
5. [API Design](#5-api-design)
6. [Authentication & Security](#6-authentication--security)
7. [Google Integration](#7-google-integration)
8. [Background Jobs](#8-background-jobs)
9. [Frontend Architecture](#9-frontend-architecture)
10. [Deployment Architecture](#10-deployment-architecture)

---

## 1. Architecture Overview

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT (Browser)                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Desktop    │  │    Mobile    │  │    Tablet    │  │  Bootstrap   │ │
│  │   View       │  │    View      │  │    View      │  │   Assets     │ │
│  └──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ HTTPS/REST JSON
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                        CODEIGNITER 4 APPLICATION                        │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                        Public Layer                              │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │  │
│  │  │   CSS    │  │    JS    │  │   Images │  │  Assets  │        │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                      Routing & Filters                          │  │
│  │  ┌──────────────────────────────────────────────────────────┐   │  │
│  │  │  Routes (RESTful + Named)  │  Filters (Auth, CSRF, etc) │   │  │
│  │  └──────────────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                      Controllers                                │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐│  │
│  │  │    Auth     │ │    Board    │ │    Card     │ │   Google    ││  │
│  │  │ Controller  │ │ Controller  │ │ Controller  │ │ Controller  ││  │
│  │  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘│  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐│  │
│  │  │   Column    │ │    User     │ │   API       │ │   Gmail     ││  │
│  │  │ Controller  │ │ Controller  │ │ Controller  │ │ Controller  ││  │
│  │  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘│  │
│  └──────────────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                        Services                                  │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐│  │
│  │  │ GoogleAuth  │ │ GmailSync   │ │ CalendarSync│ │ EmailSender ││  │
│  │  │  Service    │ │  Service    │ │  Service    │ │  Service    ││  │
│  │  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘│  │
│  │  ┌─────────────┐ ┌─────────────┐                              │  │
│  │  │  CardDedupe │ │ Attachment  │                              │  │
│  │  │  Service    │ │  Service    │                              │  │
│  │  └─────────────┘ └─────────────┘                              │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                         Models                                   │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐    │  │
│  │  │  User   │ │  Board  │ │ Column  │ │  Card   │ │ Check   │    │  │
│  │  │  Model  │ │  Model  │ │  Model  │ │  Model  │ │ ListItem │    │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘    │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐    │  │
│  │  │ Tag     │ │ Attach  │ │ Google  │ │ Gmail   │ │ Gmail   │    │  │
│  │  │  Model  │ │  Model  │ │Calendar │ │ Sender │ │ Watch   │    │  │
│  │  │         │ │         │ │  Model  │ │  Model  │ │  Model  │    │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘    │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ PDO/PostgreSQL
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                         POSTGRESQL DATABASE                             │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │  users  │ │ boards  │ │ columns │ │  cards  │ │checklists│          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │  tags   │ │attachments│ google  │  gmail  │  gmail  │          │
│  │         │ │         │ │calendars│ senders │  watches│          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ HTTPS/OAuth 2.0
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                          GOOGLE APIs                                    │
│  ┌─────────────────────────┐  ┌─────────────────────────┐              │
│  │   Google Calendar API   │  │      Gmail API          │              │
│  │   (Read-only)           │  │   (Read-only + Pub/Sub) │              │
│  └─────────────────────────┘  └─────────────────────────┘              │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ Webhooks (Pub/Sub)
                                    │
┌─────────────────────────────────────────────────────────────────────────┐
│                    BACKGROUND JOB QUEUE                                 │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  Cron Jobs (5min interval) + Queue Workers                        │  │
│  │  • Gmail polling fallback                                         │  │
│  │  • Calendar refresh                                               │  │
│  │  • Due date reminders                                             │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Technology Stack Summary

| Layer | Technology | Purpose |
|-------|------------|---------|
| Backend | PHP 8.4 | Application logic |
| Framework | CodeIgniter 4.x | MVC framework |
| Database | PostgreSQL 15+ | Data persistence |
| Frontend | Bootstrap 5.3 + AsteroAdmin | UI components |
| Frontend JS | jQuery 4 | DOM manipulation |
| Rich Text | TipTap | WYSIWYG editor |
| Drag-Drop | SortableJS | Kanban interactions |
| Icons | Bootstrap Icons | Iconography |
| Web Server | Caddy | HTTP server |
| PHP FPM | PHP-FPM | PHP process manager |
| Deployment | VPS (DigitalOcean/Linode) | Hosting environment |

---

## 2. System Components

### 2.1 Component Hierarchy

```
App/
├── Controllers/           # HTTP request handlers
│   ├── AuthController.php
│   ├── BoardController.php
│   ├── ColumnController.php
│   ├── CardController.php
│   ├── UserController.php
│   ├── GoogleController.php
│   ├── GmailController.php
│   └── Api/              # RESTful API controllers
│       ├── BoardController.php
│       ├── ColumnController.php
│       └── CardController.php
├── Models/                # Database models (CI4 Model class)
│   ├── UserModel.php
│   ├── BoardModel.php
│   ├── ColumnModel.php
│   ├── CardModel.php
│   ├── ChecklistItemModel.php
│   ├── TagModel.php
│   ├── AttachmentModel.php
│   ├── GoogleCalendarModel.php
│   ├── GmailSenderModel.php
│   ├── GmailWatchModel.php
│   └── EmailModel.php
├── Services/              # Business logic services
│   ├── GoogleAuthService.php
│   ├── CalendarSyncService.php
│   ├── GmailSyncService.php
│   ├── EmailSenderService.php
│   ├── CardDedupeService.php
│   └── AttachmentService.php
├── Filters/               # Route filters (pre/post processing)
│   ├── AuthFilter.php
│   ├── ApiAuthFilter.php
│   └── CsrfFilter.php
├── Libraries/             # Custom libraries
│   └── GoogleClient.php
├── Database/              # Database migrations
│   └── Migrations/
└── Views/                 # View templates
    ├── layouts/           # Main layout templates
    ├── auth/              # Authentication views
    ├── boards/            # Board views
    ├── columns/           # Column views
    ├── cards/             # Card views
    └── components/        # Reusable components
```

---

## 3. Directory Structure

```
kanban-tasks-calendar/
├── .gitignore
├── .vscode/
│   └── settings.json
├── app/
│   ├── Config/
│   │   ├── App.php
│   │   ├── Database.php
│   │   ├── Filters.php
│   │   ├── Routes.php
│   │   ├── Services.php
│   │   └── Validation.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── BoardController.php
│   │   ├── ColumnController.php
│   │   ├── CardController.php
│   │   ├── UserController.php
│   │   ├── GoogleController.php
│   │   ├── GmailController.php
│   │   └── Api/
│   │       ├── BoardController.php
│   │       ├── ColumnController.php
│   │       └── CardController.php
│   ├── Database/
│   │   └── Migrations/
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   ├── ApiAuthFilter.php
│   │   └── CsrfFilter.php
│   ├── Libraries/
│   │   └── GoogleClient.php
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── BoardModel.php
│   │   ├── ColumnModel.php
│   │   ├── CardModel.php
│   │   ├── ChecklistItemModel.php
│   │   ├── TagModel.php
│   │   ├── AttachmentModel.php
│   │   ├── GoogleCalendarModel.php
│   │   ├── GmailSenderModel.php
│   │   ├── GmailWatchModel.php
│   │   └── EmailModel.php
│   ├── Services/
│   │   ├── GoogleAuthService.php
│   │   ├── CalendarSyncService.php
│   │   ├── GmailSyncService.php
│   │   ├── EmailSenderService.php
│   │   ├── CardDedupeService.php
│   │   └── AttachmentService.php
│   └── Views/
│       ├── layouts/
│       │   ├── main.php
│       │   ├── auth.php
│       │   └── modal.php
│       ├── auth/
│       │   ├── login.php
│       │   ├── register.php
│       │   └── forgot_password.php
│       ├── boards/
│       │   ├── index.php
│       │   ├── show.php
│       │   ├── create.php
│       │   └── edit.php
│       ├── cards/
│       │   ├── _card.php
│       │   ├── _calendar_card.php
│       │   ├── _email_card.php
│       │   ├── create.php
│       │   └── edit.php
│       └── components/
│           ├── kanban_board.php
│           ├── column.php
│           └── drag_handle.php
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   │   ├── bootstrap.min.css
│   │   │   ├── theme.css
│   │   │   └── kanban.css
│   │   ├── js/
│   │   │   ├── jquery.min.js
│   │   │   ├── bootstrap.bundle.min.js
│   │   │   ├── sortable.min.js
│   │   │   └── app.js
│   │   └── images/
│   └── uploads/
│       └── attachments/
├── writable/
│   ├── logs/
│   ├── session/
│   └── cache/
├── env
├── composer.json
├── spark
├── CLAUDE.md
├── SCOPE.md
├── DESIGN.md
└── README.md
```

---

## 4. Database Schema

### 4.1 Entity-Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    users    │       │   boards    │       │  columns    │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │◄──────│ id (PK)     │◄──────│ id (PK)     │
│ email       │       │ user_id (FK)│       │ board_id(FK)│
│ password    │  1:N  │ name        │  1:N  │ name        │
│ display_name│       │ created_at  │       │ position    │
│ created_at  │       │ archived_at │       │ created_at  │
└─────────────┘       └─────────────┘       └─────────────┘
                                                    │
                                                    │
┌─────────────┐       ┌─────────────┐              │ 1:N
│ attachments │       │    cards    │◄─────────────┤
├─────────────┤       ├─────────────┤              │
│ id (PK)     │       │ id (PK)     │              │
│ card_id (FK)│◄──────│ column_id(FK)│              │
│ filename    │ N:1   │ board_id(FK) │              │
│ filesize    │       │ title       │              │
│ mimetype    │       │ description │              │
│ stored_at   │       │ color       │              │
└─────────────┘       │ priority    │              │
                      │ due_date    │              │
                      │ created_at  │              │
┌─────────────┐       │ type        │              │
│ tags        │       └─────────────┘              │
├─────────────┤       │         │                  │
│ id (PK)     │       │         │                  │
│ name        │       │         │                  │
│ color       │       │         │                  │
└─────────────┘       │         │                  │
                      │         │                  │
┌─────────────┐       │         │                  │
│card_tags    │       │         │                  │
├─────────────┤       │         │                  │
│ card_id(FK) │───────┘         │                  │
│ tag_id (FK) │─────────────────┘                  │
└─────────────┘                                          │
┌─────────────┐       ┌─────────────┐                 │
│checklist_ite│       │google_calend│                 │
├─────────────┤       ├─────────────┤                 │
│ id (PK)     │       │ id (PK)     │                 │
│ card_id(FK) │◄──────│ user_id (FK)│                 │
│ text        │ N:1   │ calendar_id │                 │
│ completed   │       │ name        │                 │
│ position    │       │ primary     │                 │
└─────────────┘       │ selected   │                 │
                      └─────────────┘                 │
                                                      │
┌─────────────┐       ┌─────────────┐                 │
│gmail_senders│       │gmail_tokens │                 │
├─────────────┤       ├─────────────┤                 │
│ id (PK)     │       │ id (PK)     │                 │
│ user_id (FK)│       │ user_id (FK)│                 │
│ board_id(FK)│       │ access_token│                 │
│ column_id(FK)│      │ refresh_token│                │
│ email       │       │ expires_at  │                 │
│ wildcard    │       └─────────────┘                 │
└─────────────┘                                        │
┌─────────────┐       ┌─────────────┐                 │
│ gmail_watches│      │   emails    │                 │
├─────────────┤       ├─────────────┤                 │
│ id (PK)     │       │ id (PK)     │                 │
│ user_id (FK)│       │ thread_id   │                 │
│ watch_id    │       │ card_id (FK)│◄────────────────┘
│ resource_id │       │ gmail_id    │
│ history_id  │       │ from_email  │
│ expiration  │       │ subject     │
└─────────────┘       │ body        │
                      │ snippet     │
                      │ created_at  │
                      └─────────────┘
```

### 4.2 Table Definitions

#### users
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
```

#### boards
```sql
CREATE TABLE boards (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    archived_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_boards_user_id ON boards(user_id);
CREATE INDEX idx_boards_archived_at ON boards(archived_at) WHERE archived_at IS NULL;
```

#### columns
```sql
CREATE TABLE columns (
    id SERIAL PRIMARY KEY,
    board_id INTEGER NOT NULL REFERENCES boards(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    position INTEGER NOT NULL,
    is_date_based BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_columns_board_id ON columns(board_id);
CREATE UNIQUE INDEX idx_columns_board_position ON columns(board_id, position);
```

#### cards
```sql
CREATE TABLE cards (
    id SERIAL PRIMARY KEY,
    column_id INTEGER NOT NULL REFERENCES columns(id) ON DELETE CASCADE,
    board_id INTEGER NOT NULL REFERENCES boards(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6c757d',
    priority VARCHAR(10) DEFAULT 'medium', -- 'low', 'medium', 'high'
    due_date TIMESTAMP NULL,
    type VARCHAR(20) DEFAULT 'task', -- 'task', 'calendar', 'email'
    position INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_cards_column_id ON cards(column_id);
CREATE INDEX idx_cards_board_id ON cards(board_id);
CREATE INDEX idx_cards_due_date ON cards(due_date);
CREATE INDEX idx_cards_type ON cards(type);
```

#### checklist_items
```sql
CREATE TABLE checklist_items (
    id SERIAL PRIMARY KEY,
    card_id INTEGER NOT NULL REFERENCES cards(id) ON DELETE CASCADE,
    text TEXT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    position INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_checklist_items_card_id ON checklist_items(card_id);
```

#### tags
```sql
CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### card_tags
```sql
CREATE TABLE card_tags (
    card_id INTEGER NOT NULL REFERENCES cards(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (card_id, tag_id)
);

CREATE INDEX idx_card_tags_tag_id ON card_tags(tag_id);
```

#### attachments
```sql
CREATE TABLE attachments (
    id SERIAL PRIMARY KEY,
    card_id INTEGER NOT NULL REFERENCES cards(id) ON DELETE CASCADE,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    filesize INTEGER NOT NULL,
    mimetype VARCHAR(100) NOT NULL,
    stored_at VARCHAR(255) NOT NULL, -- 'local' or 's3'
    file_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_attachments_card_id ON attachments(card_id);
```

#### google_calendars
```sql
CREATE TABLE google_calendars (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    calendar_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    primary_calendar BOOLEAN DEFAULT FALSE,
    selected BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_google_calendars_user_id ON google_calendars(user_id);
```

#### google_tokens
```sql
CREATE TABLE google_tokens (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    access_token TEXT NOT NULL,
    refresh_token TEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    scope TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_google_tokens_user_id ON google_tokens(user_id);
```

#### gmail_senders
```sql
CREATE TABLE gmail_senders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    board_id INTEGER REFERENCES boards(id) ON DELETE SET NULL,
    column_id INTEGER NOT NULL REFERENCES columns(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    wildcard BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_gmail_senders_user_id ON gmail_senders(user_id);
```

#### gmail_watches
```sql
CREATE TABLE gmail_watches (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    watch_id VARCHAR(255) UNIQUE NOT NULL,
    resource_id VARCHAR(255) NOT NULL,
    history_id BIGINT NOT NULL,
    expiration TIMESTAMP NOT NULL
);

CREATE INDEX idx_gmail_watches_user_id ON gmail_watches(user_id);
```

#### emails
```sql
CREATE TABLE emails (
    id SERIAL PRIMARY KEY,
    thread_id VARCHAR(255) NOT NULL,
    gmail_id VARCHAR(255) UNIQUE NOT NULL,
    card_id INTEGER REFERENCES cards(id) ON DELETE SET NULL,
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255),
    subject VARCHAR(500) NOT NULL,
    body TEXT,
    snippet TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_emails_thread_id ON emails(thread_id);
CREATE INDEX idx_emails_card_id ON emails(card_id);
```

### 4.3 Migration Classes

CodeIgniter 4 migrations will be created in `app/Database/Migrations/`:

| Migration | Description |
|-----------|-------------|
| `2025-02-25-000001_CreateUsersTable.php` | Users table |
| `2025-02-25-000002_CreateBoardsTable.php` | Boards table |
| `2025-02-25-000003_CreateColumnsTable.php` | Columns table |
| `2025-02-25-000004_CreateCardsTable.php` | Cards table |
| `2025-02-25-000005_CreateChecklistItemsTable.php` | Checklist items |
| `2025-02-25-000006_CreateTagsTable.php` | Tags table |
| `2025-02-25-000007_CreateCardTagsTable.php` | Card-tags junction |
| `2025-02-25-000008_CreateAttachmentsTable.php` | Attachments |
| `2025-02-25-000009_CreateGoogleCalendarsTable.php` | Google calendars |
| `2025-02-25-000010_CreateGoogleTokensTable.php` | Google OAuth tokens |
| `2025-02-25-000011_CreateGmailSendersTable.php` | Gmail watched senders |
| `2025-02-25-000012_CreateGmailWatchesTable.php` | Gmail watch subscriptions |
| `2025-02-25-000013_CreateEmailsTable.php` | Stored email bodies |

---

## 5. API Design

### 5.1 API Routes

#### Authentication Routes
```
POST   /api/auth/register    - Register new user
POST   /api/auth/login       - Login user
POST   /api/auth/logout      - Logout user
POST   /api/auth/refresh     - Refresh access token
POST   /api/auth/forgot     - Request password reset
POST   /api/auth/reset      - Reset password with token
```

#### Board API Routes (RESTful Resource)
```
GET    /api/boards          - List user's boards
GET    /api/boards/{id}     - Get board details
POST   /api/boards          - Create new board
PUT    /api/boards/{id}     - Update board
DELETE /api/boards/{id}     - Delete board
POST   /api/boards/{id}/archive - Archive board
```

#### Column API Routes
```
GET    /api/boards/{boardId}/columns    - List board columns
POST   /api/boards/{boardId}/columns    - Create column
PUT    /api/columns/{id}                - Update column
DELETE /api/columns/{id}                - Delete column
POST   /api/columns/reorder             - Reorder columns
```

#### Card API Routes
```
GET    /api/columns/{columnId}/cards    - List column cards
POST   /api/columns/{columnId}/cards    - Create card
GET    /api/cards/{id}                  - Get card details
PUT    /api/cards/{id}                  - Update card
DELETE /api/cards/{id}                  - Delete card
POST   /api/cards/{id}/move             - Move card to column
POST   /api/cards/{id}/reorder          - Reorder card in column
POST   /api/cards/{id}/tags             - Add tag to card
DELETE /api/cards/{id}/tags/{tagId}     - Remove tag from card
```

#### Checklist API Routes
```
GET    /api/cards/{cardId}/checklist    - Get card checklist
POST   /api/cards/{cardId}/checklist    - Add checklist item
PUT    /api/checklist/{id}              - Update checklist item
DELETE /api/checklist/{id}              - Delete checklist item
POST   /api/checklist/{id}/toggle       - Toggle completion
```

#### Attachment API Routes
```
POST   /api/cards/{cardId}/attachments  - Upload attachment
GET    /api/attachments/{id}            - Get attachment
DELETE /api/attachments/{id}            - Delete attachment
```

#### Google Calendar API Routes
```
GET    /api/google/auth                 - Initiate OAuth flow
GET    /api/google/callback             - OAuth callback
GET    /api/google/calendars            - List user's calendars
POST   /api/google/calendars/select     - Select/deselect calendar
GET    /api/boards/{boardId}/events     - Get calendar events for board
POST   /api/boards/{boardId}/events/sync - Sync calendar events
```

#### Gmail API Routes
```
GET    /api/gmail/auth                  - Initiate OAuth (uses same as calendar)
POST   /api/gmail/senders               - Add watched sender
GET    /api/gmail/senders               - List watched senders
DELETE /api/gmail/senders/{id}          - Remove watched sender
POST   /api/gmail/watch                 - Enable Gmail watch
DELETE /api/gmail/watch                 - Disable Gmail watch
POST   /api/gmail/sync                  - Manual email sync
POST   /api/gmail/webhook               - Gmail push notification endpoint
```

#### Tag API Routes
```
GET    /api/tags                        - List all user's tags
POST   /api/tags                        - Create tag
PUT    /api/tags/{id}                   - Update tag
DELETE /api/tags/{id}                   - Delete tag
```

### 5.2 API Response Format

#### Success Response
```json
{
  "status": "success",
  "data": {
    // Response data
  },
  "meta": {
    "timestamp": "2025-02-25T12:00:00Z"
  }
}
```

#### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "errors": [
    {
      "field": "email",
      "message": "The email field is required."
    }
  ]
}
```

### 5.3 Key API Examples

#### Create Card
```http
POST /api/columns/5/cards HTTP/1.1
Content-Type: application/json
Authorization: Bearer {token}

{
  "title": "Complete project setup",
  "description": "# Setup Tasks\n\n- [ ] Initialize repo\n- [ ] Create migrations",
  "color": "#198754",
  "priority": "high",
  "due_date": "2025-03-01T12:00:00Z",
  "tags": ["urgent", "setup"],
  "checklist": [
    {"text": "Task 1", "completed": false},
    {"text": "Task 2", "completed": false}
  ]
}
```

#### Move Card
```http
POST /api/cards/42/move HTTP/1.1
Content-Type: application/json

{
  "target_column_id": 7,
  "position": 0
}
```

---

## 6. Authentication & Security

### 6.1 Authentication Flow

```
┌──────────┐                    ┌─────────────┐                    ┌─────────────┐
│  Client  │                    │   CI4 App   │                    │  Database   │
└──────────┘                    └─────────────┘                    └─────────────┘
     │                                 │                                 │
     │  POST /api/auth/login            │                                 │
     │  {email, password}               │                                 │
     │────────────────────────────────>│                                 │
     │                                 │                                 │
     │                                 │  SELECT * FROM users            │
     │                                 │  WHERE email = ?                 │
     │                                 │────────────────────────────────>│
     │                                 │<────────────────────────────────│
     │                                 │                                 │
     │  Response: {access_token,        │                                 │
     │   refresh_token, user}           │                                 │
     │<────────────────────────────────│                                 │
     │                                 │                                 │
     │  GET /api/boards                │                                 │
     │  Authorization: Bearer {token}   │                                 │
     │────────────────────────────────>│                                 │
     │                                 │  Validate token, fetch user      │
     │                                 │────────────────────────────────>│
     │                                 │<────────────────────────────────│
     │  Response: {boards}              │                                 │
     │<────────────────────────────────│                                 │
```

### 6.2 Security Measures

| Security Aspect | Implementation |
|-----------------|----------------|
| Password Hashing | `password_hash()` with PASSWORD_ARGON2ID (fallback to bcrypt) |
| Session Cookies | HTTP-only, Secure flag, SameSite=Strict |
| CSRF Protection | CodeIgniter CSRF filter on all POST/PUT/DELETE |
| SQL Injection | Parameterized queries via Query Builder |
| XSS Prevention | Output encoding via CI4's `esc()` function |
| Rate Limiting | Custom filter on auth endpoints (5 req/min) |
| Input Validation | Validation rules in Controllers |
| API Authentication | JWT tokens stored in HTTP-only cookies |
| File Upload | Type validation, size limits, sanitized filenames |

### 6.3 Filter Configuration

```php
// app/Config/Filters.php

public array $aliases = [
    'auth'       => \App\Filters\AuthFilter::class,
    'api-auth'   => \App\Filters\ApiAuthFilter::class,
    'csrf'       => \CodeIgniter\Filters\CSRF::class,
    'ratelimit'  => \App\Filters\RateLimitFilter::class,
];

public array $globals = [
    'before' => [],
    'after'  => ['toolbar'],
];

public array $methods = [
    'post' => ['csrf'],
    'put'  => ['csrf'],
    'delete' => ['csrf'],
];

public array $filters = [
    'auth' => ['before' => ['dashboard/*', 'boards/*', 'cards/*']],
    'api-auth' => ['before' => ['api/*', 'api/*/*']],
    'ratelimit' => ['before' => ['api/auth/login', 'api/auth/register']],
];
```

---

## 7. Google Integration

### 7.1 Google OAuth Flow

```
┌──────────┐  ┌─────────────┐  ┌──────────────────┐  ┌──────────┐
│  Client  │  │   CI4 App   │  │  Google OAuth 2.0 │  │   API    │
└──────────┘  └─────────────┘  └──────────────────┘  └──────────┘
     │               │                    │                   │
     │  Click       │                    │                   │
     │  "Connect"   │                    │                   │
     │─────────────>│                    │                   │
     │               │                    │                   │
     │  Redirect    │                    │                   │
     │  to Google   │                    │                   │
     │<─────────────│                    │                   │
     │───────────────────────────────────>│                   │
     │               │                    │                   │
     │               │                    │  Auth Code        │
     │               │<───────────────────│                   │
     │               │───────────────────────────────────────>│
     │               │                    │  Access Token     │
     │               │                    │  + Refresh Token  │
     │               │<───────────────────│                   │
     │               │                    │                   │
     │  Tokens saved│                    │                   │
     │  redirect    │                    │                   │
     │<─────────────│                    │                   │
```

### 7.2 Google Client Service

```php
<?php
// app/Libraries/GoogleClient.php

namespace App\Libraries;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Gmail;

class GoogleClient
{
    protected Client $client;
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = getenv('google.client.id');
        $this->clientSecret = getenv('google.client.secret');
        $this->redirectUri = getenv('google.redirect.uri');

        $this->client = new Client();
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl(string $state = ''): string
    {
        $scopes = [
            'https://www.googleapis.com/auth/calendar.readonly',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.modify', // For watch/unwatch
        ];

        $this->client->setScopes($scopes);
        return $this->client->createAuthUrl(['state' => $state]);
    }

    public function fetchAccessToken(string $authCode): array
    {
        return $this->client->fetchAccessTokenWithAuthCode($authCode);
    }

    public function setAccessToken(array $token): void
    {
        $this->client->setAccessToken($token);
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $this->client->refreshToken($refreshToken);
        return $this->client->getAccessToken();
    }

    public function getCalendarService(): Calendar
    {
        return new Calendar($this->client);
    }

    public function getGmailService(): Gmail
    {
        return new Gmail($this->client);
    }

    public function isAccessTokenExpired(): bool
    {
        return $this->client->isAccessTokenExpired();
    }
}
```

### 7.3 Calendar Sync Service

```php
<?php
// app/Services/CalendarSyncService.php

namespace App\Services;

use App\Libraries\GoogleClient;
use App\Models\BoardModel;
use App\Models\CardModel;
use App\Models\GoogleCalendarModel;
use App\Models\ColumnModel;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\Event;

class CalendarSyncService
{
    protected GoogleClient $googleClient;
    protected BoardModel $boardModel;
    protected CardModel $cardModel;
    protected GoogleCalendarModel $calendarModel;
    protected ColumnModel $columnModel;

    public function __construct(
        GoogleClient $googleClient,
        BoardModel $boardModel,
        CardModel $cardModel,
        GoogleCalendarModel $calendarModel,
        ColumnModel $columnModel
    ) {
        $this->googleClient = $googleClient;
        $this->boardModel = $boardModel;
        $this->cardModel = $cardModel;
        $this->calendarModel = $calendarModel;
        $this->columnModel = $columnModel;
    }

    public function syncCalendarEvents(int $userId, int $boardId): int
    {
        $tokens = $this->googleClient->getTokensForUser($userId);
        $this->googleClient->setAccessToken($tokens);

        $calendars = $this->calendarModel->where('user_id', $userId)
            ->where('selected', true)
            ->findAll();

        $eventsAdded = 0;

        foreach ($calendars as $calendar) {
            $service = $this->googleClient->getCalendarService();
            $events = $service->events->listEvents($calendar['calendar_id'], [
                'timeMin' => date('c'),
                'maxResults' => 50,
                'orderBy' => 'startTime',
                'singleEvents' => true,
            ]);

            foreach ($events->getItems() as $event) {
                $eventsAdded += $this->createEventCard($boardId, $event, $calendar);
            }
        }

        return $eventsAdded;
    }

    protected function createEventCard(int $boardId, Event $event, array $calendar): int
    {
        $start = $event->start->dateTime ?? $event->start->date;
        $date = date('Y-m-d', strtotime($start));

        // Find date-based column or use first column
        $column = $this->columnModel->where('board_id', $boardId)
            ->where('name', $date)
            ->first()
            ?? $this->columnModel->where('board_id', $boardId)
                ->orderBy('position', 'ASC')
                ->first();

        if (!$column) {
            return 0;
        }

        $existingCard = $this->cardModel
            ->where('type', 'calendar')
            ->where('title', $event->id) // Store event ID in title for deduplication
            ->where('column_id', $column['id'])
            ->first();

        if ($existingCard) {
            return 0;
        }

        $this->cardModel->insert([
            'column_id' => $column['id'],
            'board_id' => $boardId,
            'title' => $event->id,
            'description' => json_encode([
                'summary' => $event->summary,
                'location' => $event->location,
                'start' => $start,
                'end' => $event->end->dateTime ?? $event->end->date,
                'calendar_id' => $calendar['calendar_id'],
            ]),
            'type' => 'calendar',
            'due_date' => $start,
        ]);

        return 1;
    }
}
```

### 7.4 Gmail Watch & Sync

```php
<?php
// app/Services/GmailSyncService.php

namespace App\Services;

use App\Libraries\GoogleClient;
use App\Models\BoardModel;
use App\Models\CardModel;
use App\Models\EmailModel;
use App\Models\GmailSenderModel;
use App\Models\GmailWatchModel;
use Google\Service\Gmail as GmailService;
use Google\Service\Gmail\Message;

class GmailSyncService
{
    protected GoogleClient $googleClient;
    protected BoardModel $boardModel;
    protected CardModel $cardModel;
    protected EmailModel $emailModel;
    protected GmailSenderModel $senderModel;
    protected GmailWatchModel $watchModel;

    // ... constructor ...

    public function setupWatch(int $userId): array
    {
        $tokens = $this->googleClient->getTokensForUser($userId);
        $this->googleClient->setAccessToken($tokens);

        $gmail = $this->googleClient->getGmailService();
        $request = new GmailService\WatchRequest();

        $request->setTopicName(getenv('gmail.pubsub.topic'));
        $request->setLabelIds(['INBOX']);

        $watchResponse = $gmail->users->watch('me', $request);

        // Store watch subscription
        $this->watchModel->insert([
            'user_id' => $userId,
            'watch_id' => $watchResponse->historyId,
            'resource_id' => $watchResponse->messageId,
            'history_id' => $watchResponse->historyId,
            'expiration' => date('Y-m-d H:i:s', $watchResponse->expiration / 1000),
        ]);

        return [
            'watch_id' => $watchResponse->historyId,
            'expiration' => $watchResponse->expiration,
        ];
    }

    public function syncNewEmails(int $userId, string $historyId = null): int
    {
        $tokens = $this->googleClient->getTokensForUser($userId);
        $this->googleClient->setAccessToken($tokens);

        $gmail = $this->googleClient->getGmailService();
        $watch = $this->watchModel->where('user_id', $userId)->first();

        if (!$watch) {
            return 0;
        }

        $startHistoryId = $historyId ?? $watch['history_id'];

        // Get history since last check
        $history = $gmail->users_history->list('me', [
            'startHistoryId' => $startHistoryId,
        ]);

        $cardsCreated = 0;

        foreach ($history->getHistory() as $h) {
            $watch['history_id'] = $h->id;

            foreach ($h->messagesAdded ?? [] as $msgAdded) {
                $message = $gmail->users_messages->get('me', $msgAdded->messageId, [
                    'format' => 'metadata',
                    'metadataHeaders' => ['From', 'Subject'],
                ]);

                $headers = $this->parseHeaders($message->payload->headers);
                $fromEmail = $headers['From'] ?? '';

                // Check if sender is watched
                $sender = $this->findMatchingSender($userId, $fromEmail);

                if ($sender) {
                    $cardsCreated += $this->createEmailCard(
                        $userId,
                        $msgAdded->messageId,
                        $sender,
                        $headers
                    );
                }
            }
        }

        $this->watchModel->update($watch['id'], [
            'history_id' => $watch['history_id'],
        ]);

        return $cardsCreated;
    }

    protected function findMatchingSender(int $userId, string $fromEmail): ?array
    {
        // Extract email from "Name <email@domain.com>"
        preg_match('/<([^>]+)>/', $fromEmail, $matches);
        $email = $matches[1] ?? $fromEmail;
        $domain = substr(strrchr($email, '@'), 1);

        return $this->senderModel
            ->where('user_id', $userId)
            ->groupStart()
                ->where('email', $email)
                ->orWhere('wildcard', true)
                ->where('email', '@' . $domain)
            ->groupEnd()
            ->first();
    }

    protected function createEmailCard(
        int $userId,
        string $messageId,
        array $sender,
        array $headers
    ): int {
        // Check for duplicate by thread ID
        $existing = $this->emailModel->where('gmail_id', $messageId)->first();
        if ($existing && $existing['card_id']) {
            return 0; // Already created card for this message
        }

        // Get full message body
        $gmail = $this->googleClient->getGmailService();
        $message = $gmail->users_messages->get('me', $messageId, ['format' => 'full']);
        $body = $this->extractBody($message->payload);

        // Create card
        $cardId = $this->cardModel->insert([
            'column_id' => $sender['column_id'],
            'board_id' => $sender['board_id'],
            'title' => $headers['Subject'] ?? 'New Email',
            'type' => 'email',
            'priority' => 'medium',
        ]);

        // Store email data
        $this->emailModel->insert([
            'thread_id' => $message->threadId,
            'gmail_id' => $messageId,
            'card_id' => $cardId,
            'from_email' => $this->extractEmail($headers['From'] ?? ''),
            'from_name' => $this->extractName($headers['From'] ?? ''),
            'subject' => $headers['Subject'] ?? '',
            'body' => $body,
            'snippet' => $message->snippet,
        ]);

        return 1;
    }
}
```

---

## 8. Background Jobs

### 8.1 Job Queue Architecture

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Cron Job  │    │ Queue       │    │ Workers     │
│   (5 min)   │    │ (Database)  │    │ (PHP)       │
└─────────────┘    └─────────────┘    └─────────────┘
     │                   │                   │
     │  Check for jobs   │                   │
     │──────────────────>│                   │
     │                   │                   │
     │  Job pending?     │                   │
     │<──────────────────│                   │
     │                   │                   │
     │  Execute job      │                   │
     │──────────────────────────────────────>│
     │                   │                   │
     │                   │  Mark complete    │
     │                   │<──────────────────│
```

### 8.2 Cron Jobs

| Job | Command | Schedule | Description |
|-----|---------|----------|-------------|
| Gmail Sync Fallback | `php spark gmail:sync-all` | */5 * * * * | Sync Gmail for all users (webhook fallback) |
| Calendar Refresh | `php spark calendar:refresh-all` | */15 * * * * | Refresh calendar events for all users |
| Due Date Reminders | `php spark reminders:send` | 0 9 * * * | Send due date reminder emails |
| Token Refresh | `php spark google:refresh-tokens` | 0 * * * * | Refresh expired Google tokens |
| Cleanup Old Data | `php spark cleanup:old` | 0 2 * * 0 | Cleanup old data weekly |

### 8.3 Job Queue Table

```sql
CREATE TABLE jobs (
    id SERIAL PRIMARY KEY,
    queue VARCHAR(50) DEFAULT 'default',
    payload JSONB NOT NULL,
    attempts INTEGER DEFAULT 0,
    reserved_at TIMESTAMP NULL,
    available_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_jobs_queue_available ON jobs(queue, available_at) WHERE reserved_at IS NULL;
```

---

## 9. Frontend Architecture

### 9.1 Component Structure

```
Views/
├── layouts/
│   ├── main.php          # Main application layout
│   └── modal.php         # Modal dialog layout
├── auth/
│   ├── login.php         # Login form
│   └── register.php      # Registration form
├── boards/
│   ├── index.php         # Board list / kanban view
│   └── show.php          # Single board view
├── cards/
│   ├── _card.php         # Card component (task)
│   ├── _calendar_card.php # Calendar event card
│   ├── _email_card.php   # Email card
│   ├── create.php        # New card form
│   └── edit.php          # Edit card form
└── components/
    ├── kanban_board.php  # Kanban board container
    ├── column.php        # Column component
    └── drag_handle.php   # Drag handle component
```

### 9.2 JavaScript Modules

```javascript
// public/assets/js/app.js

const KTM = {
    // State management
    state: {
        currentBoard: null,
        columns: [],
        cards: [],
        dragSource: null,
    },

    // Initialize app
    init() {
        this.initDragAndDrop();
        this.initModals();
        this.initTipTap();
        this.initAutoSave();
    },

    // Drag and drop using SortableJS
    initDragAndDrop() {
        const columns = document.querySelectorAll('.kanban-column');
        columns.forEach(column => {
            new Sortable(column.querySelector('.card-list'), {
                group: 'cards',
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'dragging',
                onEnd: (evt) => this.handleCardDrop(evt),
            });
        });

        // Column reordering
        new Sortable(document.querySelector('.columns-container'), {
            animation: 150,
            handle: '.column-header',
            onEnd: (evt) => this.handleColumnDrop(evt),
        });
    },

    // Handle card drop
    async handleCardDrop(evt) {
        const cardId = evt.item.dataset.cardId;
        const newColumnId = evt.to.closest('.kanban-column').dataset.columnId;
        const newPosition = evt.newIndex;

        const response = await fetch(`/api/cards/${cardId}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                target_column_id: newColumnId,
                position: newPosition,
            }),
        });

        if (!response.ok) {
            // Revert on error
            evt.from.appendChild(evt.item);
        }
    },

    // TipTap editor initialization
    initTipTap() {
        document.querySelectorAll('.tiptap-editor').forEach(el => {
            const editor = new Editor({
                element: el,
                extensions: [
                    StarterKit,
                    TaskList,
                    TaskItem,
                    Image,
                    Table,
                    TableRow,
                    TableHeader,
                    TableCell,
                ],
                content: el.dataset.content || '',
                onUpdate: ({ editor }) => {
                    el.dataset.markdown = editor.getMarkdown();
                },
            });
        });
    },

    // Auto-save card changes
    initAutoSave() {
        let saveTimeout;
        document.querySelectorAll('[data-auto-save]').forEach(el => {
            el.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => this.saveCard(el), 1000);
            });
        });
    },

    async saveCard(element) {
        const cardId = element.closest('[data-card-id]').dataset.cardId;
        // Save logic...
    },

    // Mobile swipe navigation
    initMobileSwipe() {
        let touchStartX = 0;
        const boardContainer = document.querySelector('.columns-container');

        boardContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
        });

        boardContainer.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > 100) {
                if (diff > 0) {
                    this.scrollNextColumn();
                } else {
                    this.scrollPrevColumn();
                }
            }
        });
    },
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => KTM.init());
```

### 9.3 TipTap Editor Configuration

```javascript
// public/assets/js/editor.js

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';
import Image from '@tiptap/extension-image';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import TableCell from '@tiptap/extension-table-cell';

export function createEditor(element, content = '') {
    return new Editor({
        element: element,
        extensions: [
            StarterKit.configure({
                heading: { levels: [1, 2, 3, 4, 5, 6] },
                codeBlock: { languageClass: 'language-' },
            }),
            TaskList,
            TaskItem,
            Image,
            Table.configure({
                resizable: true,
            }),
            TableRow,
            TableHeader,
            TableCell,
        ],
        content: content,
        editorProps: {
            attributes: {
                class: 'tiptap-content prose dark:prose-invert max-w-none',
            },
        },
        onUpdate: ({ editor }) => {
            // Store Markdown
            const markdown = editor.storage.markdown?.getMarkdown() ||
                             editor.getText();

            element.dataset.value = markdown;
        },
    });
}
```

---

## 10. Deployment Architecture

### 10.1 VPS Server Layout

```
┌─────────────────────────────────────────────────────────────┐
│                      VPS (Ubuntu 24.04)                      │
├─────────────────────────────────────────────────────────────┤
│  Caddy (80, 443)                                            │
│  ├── SSL/Let's Encrypt                                      │
│  └── Reverse proxy to PHP-FPM                               │
├─────────────────────────────────────────────────────────────┤
│  PHP 8.4 + FPM                                             │
│  ├── CodeIgniter 4 Application                              │
│  └── Composer dependencies                                 │
├─────────────────────────────────────────────────────────────┤
│  PostgreSQL 15+                                             │
│  ├── kanban_db                                             │
│  └── Scheduled backups                                     │
├─────────────────────────────────────────────────────────────┤
│  Supervisor (Process Manager)                               │
│  ├── queue:worker (long-running)                            │
│  └── horizon:work (alternative)                            │
├─────────────────────────────────────────────────────────────┤
│  Cron Jobs                                                  │
│  ├── */5 * * * * php spark jobs:work                       │
│  ├── */5 * * * * php spark gmail:sync-all                  │
│  └── 0 2 * * * pg_dump kanban_db > backup.sql             │
└─────────────────────────────────────────────────────────────┘
```

### 10.2 Caddy Configuration

```Caddy
# /etc/caddy/Caddyfile

WRITE THIS
```

### 10.3 Environment Configuration

```ini
# app/.env

# Application
app.baseURL = 'https://yourdomain.com'
app.environment = production

# Database
database.default.hostname = localhost
database.default.database = kanban_db
database.default.username = kanban_user
database.default.password = strong_password_here
database.default.port = 5432
database.default.DBDriver = Postgre

# Google OAuth
google.client.id = your_google_client_id
google.client.secret = your_google_client_secret
google.redirect.uri = https://yourdomain.com/google/callback

# Gmail Pub/Sub
gmail.pubsub.topic = projects/your-project/topics/your-topic
gmail.webhook.secret = your_webhook_secret

# Email (SMTP for password reset)
email.fromName = "Kanban Task Manager"
email.fromEmail = noreply@yourdomain.com
email.protocol = smtp
email.host = smtp.yourdomain.com
email.port = 587
email.user = your_smtp_user
email.pass = your_smtp_password

# Session
session.driver = file
session.savePath = WRITEPATH . 'session'
session.expiration = 7200
session.matchIP = true

# Security
security.tokenName = csrf_token
security.headerName = X-CSRF-TOKEN
security.cookieName = csrf_cookie
security.expires = 7200
security.regenerate = true
security.redirect = true
```

---

## 11. Implementation Phases

### Phase 1: Foundation
- [ ] Initialize CodeIgniter 4 project
- [ ] Create database migrations
- [ ] Set up AsteroAdmin assets
- [ ] Configure routing and filters
- [ ] Base authentication (login/register)

### Phase 2: Core Kanban
- [ ] Board CRUD
- [ ] Column CRUD with reordering
- [ ] Card CRUD with drag-drop
- [ ] Rich text editor (TipTap)
- [ ] Tags and attachments

### Phase 3: Google Integration
- [ ] Google OAuth flow
- [ ] Calendar API integration
- [ ] Calendar event display in columns
- [ ] Gmail OAuth (shared with calendar)
- [ ] Gmail sender configuration
- [ ] Email-triggered task creation

### Phase 4: Background Jobs
- [ ] Job queue system
- [ ] Gmail polling (cron fallback)
- [ ] Gmail watch/webhooks
- [ ] Calendar refresh
- [ ] Due date reminders

### Phase 5: Mobile & Polish
- [ ] Responsive layout
- [ ] Mobile swipe navigation
- [ ] Touch-optimized controls
- [ ] Accessibility (ARIA labels)
- [ ] Performance optimization

---

**End of Design Document**