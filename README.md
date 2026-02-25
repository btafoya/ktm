# Kanban Task Manager (KTM)

A personal task management web application that combines kanban board functionality with Google Calendar appointment display and Gmail integration.

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-FF4F28?style=flat&logo=codeigniter&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-4169E1?style=flat&logo=postgresql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat)

## Features

### Kanban Board Management
- Create, edit, and archive multiple boards
- Fully dynamic column management (create, rename, delete, reorder)
- Custom column names and date-based columns
- Minimum 1 column, configurable maximum limit

### Card Management
- Create cards with rich text descriptions
- Color coding for visual categorization
- Multiple tags/labels per card
- Priority levels (Low, Medium, High)
- Optional due dates with email reminders
- File attachments support
- Nested checklists with completion tracking
- Drag-and-drop between columns
- Reordering within columns

### Rich Text Editor
- WYSIWYG editor based on TipTap
- Content stored as Markdown in database
- Bold, italic, underline, strikethrough
- Ordered and unordered lists with nesting
- Links, headings (H1-H6), code blocks with syntax highlighting
- Tables, images, task lists, block quotes, horizontal rules

### Google Calendar Integration
- OAuth authentication for Google Calendar access
- Select which calendars to display
- Events appear as read-only cards in appropriate columns
- Visual distinction from task cards
- Event details modal with full information
- Manual refresh for latest calendar data

### Gmail Integration
- Email-triggered task creation from important senders
- Configure sender email addresses and domain wildcards
- Map senders to specific columns
- Auto-create task cards for new emails and replies
- Direct link to open email in Gmail
- Email preview (subject, sender, snippet)
- Deduplication prevention for same email threads
- Optional auto-dismiss when marked read/replied

### Mobile Experience
- Full-width columns with single column view
- Swipe left/right to navigate between columns
- Touch-optimized controls (minimum 44x44px)
- Bottom navigation for primary actions
- Full-screen card detail modals
- Long-press to initiate card dragging

## Tech Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Backend Language | PHP | 8.4 |
| Framework | CodeIgniter | 4.x |
| Database | PostgreSQL | 15+ |
| UI Framework | Bootstrap | 5.3.3 |
| Admin Template | AsteroAdmin | Latest |
| Frontend | jQuery | 4 |
| Rich Text Editor | TipTap | Latest |
| Icon Library | Bootstrap Icons | Latest |
| Drag-Drop | SortableJS | Latest |
| Google APIs | Calendar API, Gmail API | Latest |

## Installation

### Prerequisites
- PHP 8.4 or higher
- PostgreSQL 15 or higher
- Composer
- Node.js (for asset builds, optional)
- Google Cloud project with Calendar and Gmail APIs enabled

### Setup

1. Clone the repository
   ```bash
   git clone https://github.com/btafoya/ktm.git
   cd ktm
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Configure environment
   ```bash
   cp env .env
   ```
   Edit `.env` with your database and Google OAuth credentials:
   ```ini
   database.default.hostname = localhost
   database.default.database = kanban_db
   database.default.username = your_username
   database.default.password = your_password
   database.default.port = 5432

   google.client.id = your_google_client_id
   google.client.secret = your_google_client_secret
   google.redirect.uri = https://yourdomain.com/auth/google/callback
   ```

4. Run database migrations
   ```bash
   php spark migrate
   ```

5. Set permissions
   ```bash
   chmod -R 777 writable/
   ```

6. Configure web server to point to the `public/` directory

## Google API Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Calendar API and Gmail API
4. Create OAuth 2.0 credentials (Web application)
5. Add authorized redirect URI: `https://yourdomain.com/auth/google/callback`
6. Copy Client ID and Client Secret to your `.env` file

Required scopes:
- `https://www.googleapis.com/auth/calendar.readonly`
- `https://www.googleapis.com/auth/gmail.readonly`

## Usage

1. Register a new account
2. Create your first kanban board
3. Add columns to match your workflow
4. Create cards with tasks, descriptions, and due dates
5. Connect Google Calendar to see events alongside tasks
6. Configure Gmail senders for email-triggered task creation

## Screenshots

*Coming soon*

## Browser Support

| Platform | Minimum Version |
|----------|-----------------|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |
| Mobile Safari | 14+ |
| Chrome Mobile | 90+ |

## Security

- Secure password hashing (Argon2id/bcrypt)
- HTTP-only session cookies
- CSRF protection on all state-changing requests
- Server-side input validation
- Parameterized queries (SQL injection prevention)
- XSS prevention with output encoding
- Rate limiting on authentication endpoints
- Secure Google OAuth token storage and refresh
- Minimal Gmail API scopes (read-only)

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please ensure your code follows the existing code style and includes appropriate tests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [CodeIgniter](https://codeigniter.com/) - PHP framework
- [Bootstrap](https://getbootstrap.com/) - UI framework
- [AsteroAdmin](https://themeselection.com/item/astero-admin-free-bootstrap-admin-template/) - Admin template
- [TipTap](https://tiptap.dev/) - Rich text editor
- [SortableJS](https://sortablejs.github.io/) - Drag-and-drop library

---

**Repository:** https://github.com/btafoya/ktm