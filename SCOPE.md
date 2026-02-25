# Kanban Board with Google Calendar & Gmail Integration - Requirements Specification

**Document Version:** 1.3
**Date:** 2026-02-25
**Status:** All Critical Questions Resolved - Ready for Architecture Design

---

## 1. Project Overview

### 1.1 Purpose
A personal task management web application that combines kanban board functionality with Google Calendar appointment display and Gmail integration. The application provides a unified interface for managing tasks alongside scheduled events and important emails, with full support for both mobile and desktop experiences.

### 1.1.1 Primary Goal
Enable individuals to organize and track tasks using kanban methodology while visualizing their calendar events and managing email-triggered tasks in context with their workflow.

### 1.1.2 Target Users
- Individual users managing personal tasks and projects
- Users who need both task management and calendar visibility in one interface
- Users accessing the application from both mobile and desktop devices

---

## 2. Functional Requirements

### 2.1 User Authentication

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-001 | User Registration | New users can create an account with email and password | High |
| FR-002 | User Login | Existing users can authenticate with email and password | High |
| FR-003 | Password Reset | Users can reset forgotten passwords via email link | Medium |
| FR-004 | Session Management | Secure session handling with timeout and logout | High |
| FR-005 | Profile Management | Users can update email, password, and display name | Low |

**Note:** No OAuth/Social login is required - email/password authentication only.

### 2.2 Kanban Board Management

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-010 | Create Board | Users can create new kanban boards with custom names | High |
| FR-011 | Edit Board | Users can rename, archive, or delete boards | Medium |
| FR-012 | Board Selection | Users can switch between multiple boards | High |
| FR-013 | Board Templates | Pre-defined board templates for common workflows (optional) | Low |

### 2.3 Column Management (Fully Dynamic)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-020 | Create Column | Users can add custom-named columns to any board | High |
| FR-021 | Rename Column | Column names can be edited at any time | High |
| FR-022 | Delete Column | Columns can be removed (with confirmation) | Medium |
| FR-023 | Reorder Columns | Columns can be reordered via drag and drop | High |
| FR-024 | Column Limits | Minimum 1 column, maximum configurable limit | Medium |
| FR-025 | Date-Based Columns | Support for date-named columns (e.g., "Today", "Tomorrow", "This Week") | Medium |

### 2.4 Card Management

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-030 | Create Card | Users can create cards in any column | High |
| FR-031 | Edit Card | Card details can be edited in modal | High |
| FR-032 | Delete Card | Cards can be deleted (with confirmation) | Medium |
| FR-033 | Move Card | Cards can be moved between columns via drag-drop or buttons | High |
| FR-034 | Reorder Cards | Cards within a column can be reordered | High |

### 2.5 Card Features

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-040 | Title | Each card has a required title field | High |
| FR-041 | Description | Rich text description using Markdown/WYSIWYG editor | High |
| FR-042 | Color Coding | Cards can be assigned background colors | High |
| FR-043 | Labels/Tags | Cards can have multiple user-defined tags | High |
| FR-044 | Priority | Cards can have priority levels (Low, Medium, High) | Medium |
| FR-045 | Due Date | Cards can have optional due dates | High |
| FR-046 | Attachments | Cards can support file attachments (configurable size limit) | Medium |
| FR-047 | Checklists | Cards can contain checklist items with completion tracking | High |
| FR-048 | Reminders | Optional email reminders for cards with due dates | Low |

### 2.6 Rich Text Editor (Markdown with WYSIWYG)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-050 | WYSIWYG Mode | Always-visible what-you-see-is-what-you-get editor | High |
| FR-051 | Markdown Storage | Content stored as Markdown in database | High |
| FR-052 | Basic Formatting | Bold, italic, underline, strikethrough | High |
| FR-053 | Lists | Ordered and unordered lists with nesting | High |
| FR-054 | Links | Insert/edit hyperlinks | High |
| FR-055 | Headings | Support for H1-H6 heading levels | Medium |
| FR-056 | Code Blocks | Inline code and multi-line code blocks with syntax highlighting | High |
| FR-057 | Tables | Create and edit tables | Medium |
| FR-058 | Images | Insert images via URL or file upload | Medium |
| FR-059 | Task Lists | Checkbox/task list items with completion toggling | Medium |
| FR-060 | Block Quotes | Block quote formatting | Low |
| FR-061 | Horizontal Rules | Divider/separator lines | Low |
| FR-062 | Embedded Content | Support for embedded iframes (YouTube, etc.) | Low |

### 2.7 Google Calendar Integration (Read-Only Display)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-070 | Google OAuth | Users can authenticate with Google for calendar access | High |
| FR-071 | Calendar Selection | Users can select which Google calendars to display | High |
| FR-072 | Event Display | Calendar events display as cards in appropriate columns | High |
| FR-073 | Read-Only Events | Calendar event cards cannot be modified (distinguished visually) | High |
| FR-074 | Event Details | Tapping an event card shows full event information | High |
| FR-075 | Event Filtering | Filter events by date range or specific calendar | Medium |
| FR-076 | Event Refresh | Manual refresh button to sync latest calendar events | High |
| FR-077 | Embedded Display | Events appear embedded within kanban columns | High |

### 2.8 Drag and Drop

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-080 | Mouse Drag | Desktop users can drag cards between columns with mouse | High |
| FR-081 | Touch Drag | Mobile users can drag cards with touch gestures | High |
| FR-082 | Drag Handles | Cards have visible drag handles for precise control | High |
| FR-083 | Long-Press | Long-press on mobile also initiates drag | Medium |
| FR-084 | Move Buttons | Cards can be moved via button action in card detail modal | High |
| FR-085 | Column Drag | Columns can be reordered via drag and drop (desktop) | Medium |

### 2.9 Mobile Experience

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-090 | Swipe Navigation | Mobile users swipe left/right to navigate between columns | High |
| FR-091 | Full-Width Columns | Mobile columns display full-width (single column view) | High |
| FR-092 | Responsive Layout | Layout adapts to screen size breakpoints | High |
| FR-093 | Touch Optimized | All controls sized for touch interaction (min 44x44px) | High |
| FR-094 | Bottom Navigation | Primary actions accessible via bottom navigation on mobile | Medium |
| FR-095 | Mobile Card Modal | Card details open in full-screen modal on mobile | High |

### 2.10 Gmail Integration (Email-Triggered Tasks)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| FR-100 | Gmail OAuth | Users can authenticate with Gmail for email monitoring (uses same Google auth as Calendar) | High |
| FR-101 | Sender Configuration | Users can configure a list of email addresses/domains that trigger task creation | High |
| FR-102 | Column Mapping | Users can map sender lists to specific columns (e.g., "boss@company.com" → "New" column) | High |
| FR-103 | Auto Task Creation | New emails from configured senders automatically create task cards | High |
| FR-104 | Reply Detection | Replies from configured senders also create task cards | High |
| FR-105 | Card Link | Email task cards include direct link to open the email in Gmail | High |
| FR-106 | Email Preview | Card shows email subject, sender, and snippet preview | High |
| FR-107 | Deduplication | Prevent duplicate task cards for the same email thread | High |
| FR-108 | Auto-Dismiss Option | Option to auto-dismiss task cards when email is marked as read/replied | Medium |
| FR-109 | Manual Refresh | Manual refresh button to fetch latest emails and create tasks | High |
| FR-110 | Email Filter | Option to filter by folder (Inbox, Sent, All, etc.) | Medium |
| FR-111 | Wildcard Senders | Support for wildcard domains (e.g., *@company.com) | Medium |
| FR-112 | Task Template | Customizable default card content for email-triggered tasks | Low |

---

## 3. Non-Functional Requirements

### 3.1 Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Backend Language | PHP | 8.4 |
| Framework | CodeIgniter | 4.x (latest) |
| Database | PostgreSQL | 15+ |
| UI Framework | Bootstrap | 5.3.3 (via AsteroAdmin) |
| Admin Template | AsteroAdmin | Latest |
| Frontend | jQuery | 4 |
| Rich Text Editor | TipTap | Latest |
| Icon Library | Bootstrap Icons | Latest |
| Drag-Drop | SortableJS | Latest |
| API Protocol | REST JSON | - |
| Google APIs | Calendar API, Gmail API | Latest |
| Background Jobs | Cron/Queue Worker | - |

#### 3.1.1 AsteroAdmin Integration

| Aspect | Decision |
|--------|----------|
| Integration Method | Copy pre-built CSS/JS assets to CodeIgniter `public/` directory |
| Asset Files | `bootstrap.min.css`, custom theme CSS, Bootstrap JS |
| Navigation | Sidebar (desktop) + Offcanvas (mobile) |
| Theme | Dark mode only (default, no light mode toggle) |
| Components Extracted | Modals, Dropdowns, Cards/Panels, Auth pages |
| Build Process | No build tools on deployment - use pre-built assets |

### 3.1.2 UI/UX Architecture

Based on AsteroAdmin Bootstrap 5.3 template:

#### Layout Structure
```
┌─────────────────────────────────────────────────────────┐
│  [Logo]  Board Selector  [Settings]  [Profile]  [☰]    │  ← Top Navbar (mobile)
├──────────┬──────────────────────────────────────────────┤
│          │                                               │
│  Boards  │   ┌──────────┬──────────┬──────────┬─────┐  │
│  (Aside) │   │  To Do   │   In     │  Done    │ ... │  │
│          │   │  [+]     │ Progress │          │     │  │
│          │   ├──────────┼──────────┼──────────┼─────┤  │
│  • Home  │   │ [Card]   │ [Card]   │ [Card]   │     │  │
│  • Proj1 │   │ [Card]   │ [Card]   │          │     │  │
│  • Proj2 │   │ [Card]   │          │          │     │  │
│          │   │  [+]     │  [+]     │  [+]     │     │  │
│  [+New]  │   └──────────┴──────────┴──────────┴─────┘  │
│          │                                               │
└──────────┴──────────────────────────────────────────────┘
           ↑                          ↑
     Desktop Sidebar           Kanban Board Area
   (collapsible)              (SortableJS columns)
```

#### Responsive Behavior

| Breakpoint | Layout | Navigation |
|------------|--------|------------|
| Desktop (≥992px) | Sidebar + Main Content | Click sidebar items |
| Tablet (768-991px) | Collapsible Sidebar | Click to expand/collapse |
| Mobile (<768px) | Offcanvas Drawer | Swipe/hamburger to open |

#### Dark Theme Configuration

- Base: AsteroAdmin dark theme SCSS variables
- Default: Dark mode only (no toggle)
- Customization: Override Bootstrap dark mode variables for kanban-specific needs

#### Component Mapping

| AsteroAdmin Component | Kanban Usage |
|-----------------------|--------------|
| Sidebar/Aside | Board list navigation |
| Offcanvas | Mobile board drawer |
| Modal | Card detail, column edit, settings dialogs |
| Dropdown | Filters, column selector, actions menu |
| Card/Panel | Kanban card styling base |
| Buttons | New card, new column, refresh, etc. |
| Form Controls | Login, registration, card editing |
| Toast/Alert | Success/error notifications |

#### Custom Components (Not from AsteroAdmin)

| Component | Implementation |
|-----------|----------------|
| Kanban Columns | Custom CSS Grid + SortableJS |
| Drag Handles | Custom handle with SortableJS integration |
| Card Drag-Drop | SortableJS with touch support |
| WYSIWYG Editor | TipTap (Markdown-based, headless editor) |
| Calendar Event Cards | Special styling to distinguish from tasks |
| Email Task Cards | Special styling with Gmail link |

### 3.2 Performance

| ID | Requirement | Description |
|----|-------------|-------------|
| NFR-001 | Page Load | Initial page load under 2 seconds on 4G |
| NFR-002 | Drag Performance | Smooth 60fps drag animations |
| NFR-003 | API Response | REST API responses under 200ms (p90) |
| NFR-004 | Calendar Sync | Google Calendar fetch under 3 seconds |
| NFR-005 | Gmail Sync | Gmail fetch under 5 seconds for monitored senders |

### 3.3 Accessibility

| ID | Requirement | Description |
|----|-------------|-------------|
| NFR-020 | Keyboard Navigation | All features accessible via keyboard |
| NFR-021 | Screen Reader | ARIA labels for all interactive elements |
| NFR-022 | Color Contrast | WCAG AA contrast ratios in dark theme |
| NFR-023 | Touch Targets | Minimum 44x44px for touch controls |

### 3.4 Security

| ID | Requirement | Description |
|----|-------------|-------------|
| NFR-030 | Authentication | Secure password hashing (Argon2id or bcrypt) |
| NFR-031 | Session Security | Secure, HTTP-only session cookies |
| NFR-032 | CSRF Protection | CSRF tokens on all state-changing requests |
| NFR-033 | Input Validation | Server-side validation of all inputs |
| NFR-034 | SQL Injection | Parameterized queries throughout |
| NFR-035 | XSS Prevention | Output encoding for user-generated content |
| NFR-036 | Rate Limiting | Rate limits on authentication endpoints |
| NFR-037 | Google OAuth | Secure token storage and refresh handling |
| NFR-038 | Gmail API Scopes | Minimal required scopes only (read-only email access) |

### 3.5 Usability

| ID | Requirement | Description |
|----|-------------|-------------|
| NFR-040 | Dark Theme | Dark theme only (no light mode), based on AsteroAdmin dark theme |
| NFR-041 | Theme Consistency | AsteroAdmin-based design system throughout |
| NFR-042 | Error Messages | Clear, actionable error messages |
| NFR-043 | Loading States | Visual feedback for async operations |
| NFR-044 | Empty States | Helpful empty state messages and illustrations |
| NFR-045 | AsteroAdmin Assets | Pre-built CSS/JS copied from AsteroAdmin, no build step required |

### 3.6 Browser Support

| Platform | Minimum Version |
|----------|-----------------|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |
| Mobile Safari | 14+ |
| Chrome Mobile | 90+ |

---

## 4. User Stories

### 4.1 Board Management

| ID | User Story | Acceptance Criteria |
|----|-------------|---------------------|
| US-001 | As a user, I want to create multiple kanban boards so I can organize different projects separately | - Board creation form with name field<br>- Board appears in navigation<br>- Can create unlimited boards |
| US-002 | As a user, I want to create custom columns so my workflow matches my process | - Add column button on board<br>- Column can be named anything<br>- Minimum 1 column enforced |
| US-003 | As a user, I want to reorder columns so the workflow makes logical sense | - Drag columns on desktop<br>- Columns persist in new order |
| US-004 | As a user, I want to add color to cards so I can visually categorize tasks | - Color picker on card edit<br>- Background color applies to entire card |

### 4.2 Card Management

| ID | User Story | Acceptance Criteria |
|----|-------------|---------------------|
| US-010 | As a user, I want to create cards with rich descriptions so I can include detailed information | - Card title required<br>- WYSIWYG editor for description<br>- All formatting options available |
| US-011 | As a user, I want to add checklists to cards so I can track subtasks | - Add checklist item button<br>- Checkboxes for each item<br>- Progress indicator shown |
| US-012 | As a user, I want to add tags to cards so I can filter and group related items | - Add multiple tags per card<br>- Tags are reusable across cards<br>- Tag colors customizable |
| US-013 | As a user, I want to set due dates so I know when tasks need completion | - Date picker for due date<br>- Overdue cards visually highlighted<br>- Optional reminder email toggle |

### 4.3 Google Calendar

| ID | User Story | Acceptance Criteria |
|----|-------------|---------------------|
| US-020 | As a user, I want to connect my Google Calendar so I can see appointments alongside my tasks | - OAuth flow initiates on click<br>- After auth, show available calendars<br>- Select multiple calendars |
| US-021 | As a user, I want calendar events to display in my kanban so I have context for my day | - Events appear as cards in columns<br>- Read-only (no editing events)<br>- Distinguished visually from task cards |
| US-022 | As a user, I want to refresh calendar events so I see the latest changes | - Refresh button available<br>- Manual trigger syncs latest data |

### 4.4 Mobile Experience

| ID | User Story | Acceptance Criteria |
|----|-------------|---------------------|
| US-030 | As a mobile user, I want to swipe between columns so navigation is natural on touch devices | - Left swipe moves to next column<br>- Right swipe moves to previous<br>- Visual indicator of available columns |
| US-031 | As a mobile user, I want drag handles on cards so precise dragging is easier | - Drag handle visible on card<br>- Dragging handle initiates move<br>- Long-press anywhere also works |
| US-032 | As a mobile user, I want move buttons in the card modal as an alternative to drag | - Card detail modal has "Move to" dropdown<br>- Lists all available columns<br>- Moves card on selection |

### 4.5 Gmail Integration

| ID | User Story | Acceptance Criteria |
|----|-------------|---------------------|
| US-040 | As a user, I want to configure important email senders so I don't miss critical messages | - Add sender email addresses to watch list<br>- Support for individual emails and domain wildcards<br>- Can remove senders from list |
| US-041 | As a user, I want emails from important senders to create task cards automatically | - New emails from watched senders create cards<br>- Replies from watched senders also create cards<br>- Cards appear in configured column |
| US-042 | As a user, I want to map different senders to different columns so I can categorize email tasks | - Assign sender(s) to specific column per board<br>- Default column for unmapped senders<br>- Column mapping persists across sessions |
| US-043 | As a user, I want to quickly access the original email from the task card | - Card displays email subject and sender<br>- "Open in Gmail" button/link on card<br>- Direct link opens email in new tab |
| US-044 | As a user, I want to prevent duplicate task cards for the same email thread | - System detects existing cards for same email<br>- Updates existing card instead of creating new<br>- Option to force new card if desired |

---

## 5. Open Questions

| ID | Question | Notes |
|----|----------|-------|
| OQ-001 | Should there be a maximum number of boards per user? | Suggest: No limit or configurable default |
| OQ-002 | Should users be able to share boards with others? | Currently scope is personal use only |
| OQ-003 | What file types should be supported for attachments? | Images, PDF, documents - size limit? |
| OQ-004 | Should there be email notifications beyond password reset? | Due date reminders requested in card features |
| OQ-008 | Should there be data export functionality? | JSON export of boards/cards? |
| OQ-011 | Should email task cards be auto-archived after action? | Option to dismiss when marked read/replied in Gmail? |
| OQ-013 | ~~Which AsteroAdmin CSS files should be copied?~~ | **RESOLVED: Full theme** |
| OQ-006 | ~~What is the target deployment environment?~~ | **RESOLVED: VPS (DigitalOcean, Linode)** |
| OQ-009 | ~~How should Gmail polling work?~~ | **RESOLVED: Webhooks + Cron Fallback** |
| OQ-005 | ~~How should date-based columns behave?~~ | **RESOLVED: Auto-populate with events/cards** |
| OQ-010 | ~~What is the cron fallback polling interval?~~ | **RESOLVED: 5 minutes** |
| OQ-012 | ~~Should email content be stored locally?~~ | **RESOLVED: Store full email body** |

### 5.1 Resolved Decisions

| ID | Question | Resolution |
|----|----------|------------|
| RD-001 | Frontend JavaScript library | jQuery 4 (as originally specified) |
| RD-002 | AsteroAdmin integration method | Copy pre-built assets only to CodeIgniter public/ |
| RD-003 | Navigation pattern | Sidebar (desktop) + Offcanvas (mobile) |
| RD-004 | Dark mode approach | Dark theme only (no toggle), AsteroAdmin dark theme |
| RD-005 | Drag-drop library | SortableJS for columns and cards |
| RD-006 | AsteroAdmin components to use | Modals, Dropdowns, Cards/Panels, Auth pages (converted to Bootstrap) |
| RD-007 | WYSIWYG editor library | TipTap for rich text card descriptions |
| RD-008 | Icon library | Bootstrap Icons (matches Bootstrap theme) |
| RD-009 | AsteroAdmin asset scope | Full theme (all CSS/JS files) |
| RD-010 | Deployment environment | VPS (DigitalOcean, Linode) - supports webhooks, queue workers, long-running processes |
| RD-011 | Gmail polling architecture | Webhooks (Google Pub/Sub) for real-time + Cron job fallback for reliability |
| RD-012 | Date-based columns behavior | Auto-populate with matching calendar events and cards due within that date range |
| RD-013 | Cron fallback polling interval | 5 minutes - balances responsiveness with resource usage |
| RD-014 | Email content storage | Store full email body in database for offline viewing and search capability |

---

## 6. Requirements Matrix Summary

| Category | High Priority | Medium Priority | Low Priority | Total |
|----------|---------------|-----------------|--------------|-------|
| Authentication | 4 | 1 | 0 | 5 |
| Board Management | 2 | 1 | 0 | 3 |
| Column Management | 4 | 1 | 0 | 5 |
| Card Management | 4 | 2 | 0 | 6 |
| Card Features | 5 | 2 | 1 | 8 |
| Rich Text Editor | 4 | 2 | 3 | 9 |
| Google Calendar | 6 | 2 | 0 | 8 |
| Gmail Integration | 9 | 2 | 1 | 12 |
| Drag and Drop | 3 | 2 | 0 | 5 |
| Mobile Experience | 4 | 1 | 0 | 5 |
| **Total** | **45** | **14** | **5** | **64** |

---

## 7. Next Steps

This requirements specification is complete and ready for:

1. **Architecture Design** - Use `/sc:design` to create system architecture, database schema, and API contracts
2. **Implementation Planning** - Use `/sc:workflow` to generate structured implementation steps
3. **AsteroAdmin Asset Extraction** - Copy full theme CSS/JS files to CodeIgniter `public/` directory

---

**End of Requirements Specification**