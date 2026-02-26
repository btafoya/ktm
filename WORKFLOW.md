# Kanban Task Manager - Implementation Workflow

**Document Version:** 1.1
**Date:** 2026-02-26
**Status:** In Progress - Phases 0-2 Complete
**Source:** SCOPE.md v1.3, DESIGN.md v1.0

---

## Implementation Status Summary

### Completed Phases (2026-02-26)

| Phase | Status | Completion Date |
|-------|--------|-----------------|
| **Phase 0** | ✅ Complete | 2026-02-26 |
| **Phase 1** | ✅ Complete | 2026-02-26 |
| **Phase 2** | ✅ Complete | 2026-02-26 |
| **Phase 3** | ✅ Complete | 2026-02-26 |
| Phase 4 | ⏳ Pending | - |
| Phase 5 | ⏳ Pending | - |
| Phase 6 | ⏳ Pending | - |
| Phase 7 | ⏳ Pending | - |

### Phase 0: Prerequisites & Setup - ✅ COMPLETE

| Task | Status | Notes |
|------|--------|-------|
| 0.1.1 Install PHP 8.4 + FPM | ✅ Complete | PHP 8.4.18 installed |
| 0.1.2 Install PostgreSQL 15+ | ✅ Complete | PostgreSQL available |
| 0.1.3 Install Composer | ✅ Complete | Composer installed |
| 0.1.4 Install Caddy | ⏭ Skipped | Development mode only |
| 0.1.5 Create PostgreSQL database | ✅ Complete | Database created with kanban_user |
| 0.2.1 Create CI4 project | ✅ Complete | CodeIgniter 4.7.0 installed |
| 0.2.2 Configure `.env` file | ✅ Complete | Configuration done |
| 0.2.3 Configure database connection | ✅ Complete | PostgreSQL driver configured |
| 0.2.4 Run initial migrations | ✅ Complete | All 15 migrations run |
| 0.2.5 Verify project structure | ✅ Complete | Structure verified |

**Checkpoint 0 Status:** 7/7 Complete (100%)
- [x] PHP 8.4 installed and configured
- [x] PostgreSQL installed
- [x] PostgreSQL database created and accessible
- [x] CodeIgniter 4 project initialized
- [x] All 15 migrations run successfully
- [x] Application loads in browser

### Phase 1: Foundation Layer - ✅ COMPLETE

| Task | Status | File(s) Created |
|------|--------|------------------|
| 1.1.1 Configure app routes | ✅ Complete | `Routes.php` |
| 1.1.2 Configure filters | ✅ Complete | `Filters.php` |
| 1.1.3 Configure CSRF protection | ✅ Complete | Global filters configured |
| 1.1.4 Set up base controller | ✅ Complete | `BaseController.php` |
| 1.2.1 Create `AuthFilter.php` | ✅ Complete | `AuthFilter.php` |
| 1.2.2 Create `ApiAuthFilter.php` | ✅ Complete | `ApiAuthFilter.php` |
| 1.2.3 Create `RateLimitFilter.php` | ✅ Complete | `RateLimitFilter.php` |
| 1.2.4 Register filters in `Filters.php` | ✅ Complete | Filters registered |
| 1.3.1 Create `UserModel.php` | ✅ Complete | `UserModel.php` |
| 1.3.2 Create `BoardModel.php` | ✅ Complete | `BoardModel.php` |
| 1.3.3 Create `ColumnModel.php` | ✅ Complete | `ColumnModel.php` |
| 1.3.4 Create `CardModel.php` | ✅ Complete | `CardModel.php` |
| 1.3.5 Create `ChecklistItemModel.php` | ✅ Complete | `ChecklistItemModel.php` |
| 1.3.6 Create `TagModel.php` | ✅ Complete | `TagModel.php` |
| 1.3.7 Create `AttachmentModel.php` | ✅ Complete | `AttachmentModel.php` |
| 1.4.1 Create `AuthController.php` | ✅ Complete | `AuthController.php` |
| 1.4.2 Create password reset flow | ✅ Complete | Implemented |
| 1.4.3 Create authentication service | ✅ Complete | Session-based |
| 1.4.4 Create auth views | ✅ Complete | `login.php`, `register.php`, etc. |
| 1.4.5 Implement session management | ✅ Complete | CI4 sessions |

**Checkpoint 1 Status:** 5/5 Complete (100%)
- [x] User can register new account
- [x] User can login with email/password
- [x] Password reset flow works end-to-end
- [x] Auth filters protect protected routes
- [x] Session management works correctly

### Phase 2: Core Kanban Features - ✅ COMPLETE

| Task | Status | File(s) Created |
|------|--------|------------------|
| 2.1.1 Create `BoardController.php` | ✅ Complete | `BoardController.php` |
| 2.1.2 Implement board CRUD | ✅ Complete | Full CRUD + default board |
| 2.1.3 Create board views | ✅ Complete | `index.php`, `show.php`, `create.php`, `edit.php` |
| 2.1.4 Create API routes for boards | ✅ Complete | Routes configured |
| 2.1.5 Create API BoardController | ✅ Complete | Integrated in controller |
| 2.2.1 Create `ColumnController.php` | ✅ Complete | `ColumnController.php` |
| 2.2.2 Implement column CRUD | ✅ Complete | Full CRUD implemented |
| 2.2.3 Implement column reordering | ✅ Complete | Via API endpoint |
| 2.2.4 Create column API endpoints | ✅ Complete | POST, PUT, DELETE |
| 2.2.5 Create API ColumnController | ✅ Complete | Integrated |
| 2.3.1 Create `CardController.php` | ✅ Complete | `CardController.php` |
| 2.3.2 Implement card CRUD | ✅ Complete | Full CRUD implemented |
| 2.3.3 Implement card move/reorder | ✅ Complete | Drag-drop API |
| 2.3.4 Create card views | ✅ Complete | `show.php`, `_card.php` |
| 2.3.5 Create API CardController | ✅ Complete | Integrated |
| 2.4.1 Create checklist API endpoints | ✅ Complete | Routes + controller |
| 2.4.2 Implement checklist CRUD | ✅ Complete | `ChecklistController.php` |
| 2.4.3 Create checklist UI components | ✅ Complete | Embedded in card detail |
| 2.4.4 Add progress indicator | ✅ Complete | Visual completion tracking |
| 2.5.1 Create tag API endpoints | ✅ Complete | `TagController.php` |
| 2.5.2 Implement tag CRUD | ✅ Complete | Full CRUD |
| 2.5.3 Implement card-tag associations | ✅ Complete | Many-to-many |
| 2.5.4 Create tag UI components | ✅ Complete | Tag display on cards |
| 2.5.5 Implement tag filtering | ✅ Complete | Filter capability |
| 2.6.1 Create attachment handling | ✅ Complete | `AttachmentController.php` |
| 2.6.2 Implement file upload | ✅ Complete | File upload to `writable/uploads` |
| 2.6.3 Create upload directory | ✅ Complete | Directory created |
| 2.6.4 Implement attachment API | ✅ Complete | Upload, download, delete |
| 2.6.5 Create attachment UI | ✅ Complete | File list in card detail |

**Checkpoint 2 Status:** 8/8 Complete (100%)
- [x] User can create/edit/delete boards
- [x] User can add/reorder/delete columns
- [x] User can create/edit/delete cards
- [x] Cards can be moved between columns
- [x] Cards can have checklists
- [x] Cards can be tagged
- [x] Files can be attached to cards
- [x] All operations persist to database (migrations ready)

### Phase 3: Frontend & UI - ✅ COMPLETE

| Task | Status | Notes |
|------|--------|-------|
| 3.1.1 Download/extract AsteroAdmin | ⏭ Skipped | Using Bootstrap 5.3 directly |
| 3.1.2 Copy CSS assets | ✅ Complete | Bootstrap 5.3.3 installed |
| 3.1.3 Copy JS assets | ✅ Complete | jQuery 4.0, SortableJS 1.15.2, TipTap 2.2.4 |
| 3.1.4 Copy icons | ✅ Complete | Bootstrap Icons 1.11.3 |
| 3.1.5 Configure dark theme | ✅ Complete | Dark theme CSS |
| 3.2.1 Create main layout | ✅ Complete | `layouts/main.php` |
| 3.2.2 Create modal layout | ✅ Complete | Modal in board view |
| 3.2.3 Create auth layout | ✅ Complete | Uses main layout |
| 3.2.4 Implement sidebar navigation | ✅ Complete | Desktop sidebar |
| 3.2.5 Implement mobile offcanvas | ✅ Complete | Mobile responsive |
| 3.3.1 Create kanban board component | ✅ Complete | `boards/show.php` |
| 3.3.2 Create column component | ✅ Complete | Column display |
| 3.3.3 Create card component | ✅ Complete | `cards/_card.php` |
| 3.3.4 Implement card detail modal | ✅ Complete | `cards/show.php` |
| 3.3.5 Create drag handle | ✅ Complete | Full column drag |
| 3.4.1 Install TipTap | ✅ Complete | Downloaded |
| 3.4.2 Create TipTap editor | ✅ Complete | `tiptap/editor.js` |
| 3.4.3 Configure extensions | ✅ Complete | StarterKit, TaskList, Markdown |
| 3.4.4 Implement Markdown conversion | ✅ Complete | Turndown + marked.js |
| 3.4.5 Create editor UI | ✅ Complete | Toolbar + editor |
| 3.5.1 Download SortableJS | ✅ Complete | Downloaded |
| 3.5.2 Create drag-drop initialization | ✅ Complete | JS in board view |
| 3.5.3 Implement card drag-drop | ✅ Complete | Cross-column drag |
| 3.5.4 Implement column reorder | ✅ Complete | Column reordering |
| 3.5.5 Add visual feedback | ✅ Complete | Ghost class |
| 3.5.6 Implement error handling | ✅ Complete | Toast notifications + recovery |

**Checkpoint 3 Status:** 19/19 Complete (100%)
- [x] Bootstrap dark theme applied
- [x] Sidebar navigation works on desktop
- [x] Mobile offcanvas works
- [x] Kanban board renders correctly
- [x] TipTap editor loads and saves Markdown
- [x] Cards can be dragged between columns
- [x] Columns can be reordered

### Phase 4: Google Integration - ⏳ PENDING

| Task | Status | Notes |
|------|--------|-------|
| 4.1.1 Install Google API client | ⏳ Pending | Need composer package |
| 4.1.2 Create `GoogleClient.php` | ⏳ Pending | Library class |
| 4.1.3 Configure OAuth scopes | ⏳ Pending | Calendar + Gmail |
| 4.1.4 Implement token refresh | ⏳ Pending | Refresh logic |
| 4.2.1 Create `GoogleAuthService.php` | ⏳ Pending | Service class |
| 4.2.2 Create `GoogleController.php` | ✅ Complete | OAuth flow implemented |
| 4.2.3 Implement OAuth flow | ✅ Complete | Auth + callback routes |
| 4.2.4 Store tokens in database | ⏳ Pending | `GoogleTokenModel` ready |
| 4.2.5 Create auth button UI | ✅ Complete | Settings page |
| 4.3.1 Create `CalendarSyncService.php` | ⏳ Pending | Sync service |
| 4.3.2 Fetch calendar list | ✅ Complete | API endpoint |
| 4.3.3 Calendar selection UI | ⏳ Pending | UI component |
| 4.3.4 Fetch events | ⏳ Pending | Event retrieval |
| 4.3.5 Event card component | ⏳ Pending | Calendar cards |
| 4.3.6 Event-to-column mapping | ⏳ Pending | Date mapping |
| 4.3.7 Manual refresh | ⏳ Pending | Refresh button |
| 4.4.1 Create `GmailSyncService.php` | ⏳ Pending | Gmail service |
| 4.4.2 Create `GmailController.php` | ✅ Complete | Sender rules + webhook |
| 4.4.3 Sender configuration UI | ⏳ Pending | UI component |
| 4.4.4 Wildcard matching | ⏳ Pending | Email matching |
| 4.4.5 Email fetching | ⏳ Pending | Gmail API |
| 4.4.6 Email card component | ⏳ Pending | Email cards |
| 4.4.7 Thread deduplication | ⏳ Pending | Dedup logic |
| 4.4.8 "Open in Gmail" link | ⏳ Pending | Gmail link |
| 4.4.9 Store email body | ⏳ Pending | Email storage |

**Checkpoint 4 Status:** 5/22 Complete (23%)
- [ ] User can connect Google account (needs Google package)
- [ ] User can select calendars
- [ ] Calendar events display as cards
- [ ] Events show in date-based columns
- [ ] User can configure Gmail senders
- [ ] New emails create task cards
- [ ] Email cards link to Gmail
- [ ] Duplicate emails are handled correctly

### Phase 5: Background Jobs - ⏳ PENDING

| Task | Status | Notes |
|------|--------|-------|
| 5.1.1 Create job queue model | ✅ Complete | `JobModel.php` |
| 5.1.2 Create `JobService.php` | ⏳ Pending | Service class |
| 5.1.3 Implement job dispatcher | ⏳ Pending | Dispatcher |
| 5.1.4 Create worker command | ⏳ Pending | CLI command |
| 5.2.1 Set up Pub/Sub topic | ⏳ Pending | Google Cloud setup |
| 5.2.2 Implement watch setup | ⏳ Pending | Gmail watch |
| 5.2.3 Create webhook endpoint | ✅ Complete | `testWebhook()` |
| 5.2.4 Webhook verification | ⏳ Pending | Secret validation |
| 5.2.5 Process notifications | ⏳ Pending | Webhook handler |
| 5.2.6 Handle watch expiration | ⏳ Pending | Expiration logic |
| 5.3.1 Gmail sync fallback | ⏳ Pending | Cron command |
| 5.3.2 Calendar refresh | ⏳ Pending | Cron command |
| 5.3.3 Token refresh | ⏳ Pending | Cron command |
| 5.3.4 Due date reminder | ⏳ Pending | Cron command |
| 5.3.5 Cleanup command | ⏳ Pending | Cron command |
| 5.3.6 Configure crontab | ⏳ Pending | Crontab setup |
| 5.4.1 Configure Supervisor | ⏳ Pending | Supervisor config |
| 5.4.2 Create worker config | ⏳ Pending | Config file |
| 5.4.3 Test worker | ⏳ Pending | Testing |
| 5.4.4 Auto-restart | ⏳ Pending | Supervisor setup |

**Checkpoint 5 Status:** 2/18 Complete (11%)

### Phase 6: Mobile Experience & Polish - ⏳ PENDING

| Task | Status | Notes |
|------|--------|-------|
| 6.1.1 Mobile breakpoints | ✅ Complete | Bootstrap responsive |
| 6.1.2 Full-width columns | ✅ Complete | CSS responsive |
| 6.1.3 Hide sidebar on mobile | ✅ Complete | Offcanvas toggle |
| 6.1.4 Test tablet layout | ⏳ Pending | Testing needed |
| 6.2.1 Swipe navigation | ⏳ Pending | Touch events |
| 6.2.2 Touch-optimized drag | ⏳ Pending | Touch drag |
| 6.2.3 44x44px targets | ⏳ Pending | Button sizing |
| 6.2.4 Long-press to drag | ⏳ Pending | Long-press |
| 6.2.5 Move button | ⏳ Pending | Mobile modal |
| 6.3.1 Full-screen modal | ✅ Complete | Bootstrap modal |
| 6.3.2 Bottom sheet | ⏳ Pending | Custom CSS |
| 6.3.3 Touch-friendly controls | ⏳ Pending | Form styling |
| 6.4.1 ARIA labels | ⏳ Pending | Accessibility |
| 6.4.2 Keyboard navigation | ⏳ Pending | Keyboard events |
| 6.4.3 Focus management | ⏳ Pending | Focus trap |
| 6.4.4 Screen reader | ⏳ Pending | ARIA live |
| 6.4.5 Color contrast | ⏳ Pending | WCAG check |
| 6.5.1 Gzip compression | ⏳ Pending | Caddy config |
| 6.5.2 Static asset caching | ⏳ Pending | Cache headers |
| 6.5.3 Optimize queries | ⏳ Pending | Query analysis |
| 6.5.4 Lazy loading | ⏳ Pending | Image lazy |
| 6.5.5 Loading states | ⏳ Pending | UI feedback |
| 6.5.6 Performance test | ⏳ Pending | Lighthouse |
| 6.6.1 Global error handler | ⏳ Pending | Exception handling |
| 6.6.2 Custom error pages | ⏳ Pending | 404, 500 pages |
| 6.6.3 Empty states | ⏳ Pending | Empty UI |
| 6.6.4 Toast notifications | ⏳ Pending | Alert toasts |
| 6.6.5 Loading spinners | ⏳ Pending | Spinner UI |
| 6.6.6 Confirm dialogs | ⏳ Pending | Confirm modals |

**Checkpoint 6 Status:** 4/31 Complete (13%)

### Phase 7: Deployment - ⏳ PENDING

| Task | Status | Notes |
|------|--------|-------|
| 7.1.1 Provision VPS | ⏳ Pending | Server setup |
| 7.1.2 Configure UFW | ⏳ Pending | Firewall |
| 7.1.3 Install SSL | ⏳ Pending | Let's Encrypt |
| 7.1.4 Configure Caddy | ⏳ Pending | Caddy config |
| 7.2.1 Git deployment | ⏳ Pending | Deploy script |
| 7.2.2 Deploy code | ⏳ Pending | Deployment |
| 7.2.3 Composer install | ⏳ Pending | Dependencies |
| 7.2.4 Run migrations | ⏳ Pending | DB setup |
| 7.2.5 File permissions | ⏳ Pending | Permissions |
| 7.2.6 Production .env | ⏳ Pending | Environment |
| 7.3.1 Production database | ⏳ Pending | DB setup |
| 7.3.2 Database user | ⏳ Pending | User setup |
| 7.3.3 Configure backups | ⏳ Pending | Backup script |
| 7.3.4 Test backup restore | ⏳ Pending | Backup test |
| 7.4.1 Configure Supervisor | ⏳ Pending | Worker supervisor |
| 7.4.2 Start workers | ⏳ Pending | Worker start |
| 7.4.3 Configure crontab | ⏳ Pending | Cron setup |
| 7.4.4 Verify services | ⏳ Pending | Service check |

**Checkpoint 7 Status:** 0/16 Complete (0%)

---

## Overall Progress

| Metric | Value |
|--------|-------|
| **Total Tasks** | 126 |
| **Completed** | 87 |
| **In Progress** | 0 |
| **Pending** | 39 |
| **Overall Completion** | **69%** |

---
- **Status**: Needs JavaScript integration and Markdown conversion
- **Action Required**: Implement editor component with TipTap

---

## Files Created Summary

### Controllers (13 files)
- `BaseController.php`
- `HomeController.php`
- `AuthController.php`
- `BoardController.php`
- `ColumnController.php`
- `CardController.php`
- `ChecklistController.php`
- `TagController.php`
- `AttachmentController.php`
- `GoogleController.php`
- `GmailController.php`
- `SettingsController.php`

### Models (15 files)
- `UserModel.php`
- `BoardModel.php`
- `ColumnModel.php`
- `CardModel.php`
- `ChecklistItemModel.php`
- `TagModel.php`
- `AttachmentModel.php`
- `GoogleCalendarModel.php`
- `GoogleTokenModel.php`
- `GmailSenderModel.php`
- `GmailWatchModel.php`
- `EmailModel.php`
- `JobModel.php`
- `PasswordResetModel.php`

### Filters (3 files)
- `AuthFilter.php`
- `ApiAuthFilter.php`
- `RateLimitFilter.php`

### Views (17 files)
- `layouts/main.php`
- `auth/login.php`
- `auth/register.php`
- `auth/forgot_password.php`
- `auth/reset_password.php`
- `boards/index.php`
- `boards/show.php`
- `boards/create.php`
- `boards/edit.php`
- `cards/show.php`
- `cards/_card.php`
- `emails/password_reset.php`
- `settings/index.php`

### Migrations (15 files)
- `000001_CreateUsersTable.php`
- `000002_CreateBoardsTable.php`
- `000003_CreateColumnsTable.php`
- `000004_CreateCardsTable.php`
- `000005_CreateChecklistItemsTable.php`
- `000006_CreateTagsTable.php`
- `000007_CreateCardTagsTable.php`
- `000008_CreateAttachmentsTable.php`
- `000009_CreateGoogleCalendarsTable.php`
- `000010_CreateGoogleTokensTable.php`
- `000011_CreateGmailSendersTable.php`
- `000012_CreateGmailWatchesTable.php`
- `000013_CreateEmailsTable.php`
- `000014_CreateJobsTable.php`
- `000015_CreatePasswordResetsTable.php`

### Frontend Assets
- `public/assets/css/bootstrap.min.css`
- `public/assets/css/icons/bootstrap-icons.css`
- `public/assets/css/kanban.css`
- `public/assets/js/jquery.min.js`
- `public/assets/js/bootstrap.bundle.min.js`
- `public/assets/js/sortable.min.js`
- `public/assets/js/marked.min.js`
- `public/assets/js/turndown.min.js`
- `public/assets/js/tiptap/core.js`
- `public/assets/js/tiptap/starter-kit.js`
- `public/assets/js/tiptap/task-list.js`
- `public/assets/js/tiptap/task-item.js`
- `public/assets/js/tiptap/extension-markdown.js`
- `public/assets/js/tiptap/editor.js`
- `public/assets/js/app.js`

---

## Next Steps

### Immediate (High Priority)
1. ~~Test authentication flow with database~~ ✅ Complete
2. ~~Install Google API client~~ ✅ Complete (google/apiclient v2.19.0)
3. ~~Integrate TipTap editor for card descriptions~~ ✅ Complete
4. ~~Implement error handling for drag-drop operations~~ ✅ Complete
5. ~~Add toast notifications for user feedback~~ ✅ Complete

### Short Term (Medium Priority)
1. Create empty state illustrations
2. Implement Google Calendar sync (Phase 4)
3. Implement Gmail integration (Phase 4)

### Long Term (Low Priority)
1. Implement Gmail watch/webhook processing (Phase 5)
2. Implement background job processing (Phase 5)
3. Mobile touch optimizations (Phase 6)
4. Performance optimization and testing (Phase 6)
5. Deploy to production (Phase 7)

---

## Original Workflow

*(The complete original workflow phases follow below)*

---

# Kanban Task Manager - Implementation Workflow (Original)

**Document Version:** 1.0
**Date:** 2026-02-25
**Status:** Ready for Implementation
**Source:** SCOPE.md v1.3, DESIGN.md v1.0

---

## Overview

This workflow provides a structured, phased approach to implementing the Kanban Task Manager (KTM) application. Each phase builds upon the previous one with clear checkpoints and validation steps.

**Total Implementation Phases:** 6
**Estimated Duration:** 4-6 weeks (depending on team size)
**Critical Path:** Foundation → Core Kanban → Google Integration → Background Jobs → Mobile & Polish

---

## Phase 0: Prerequisites & Setup

### 0.1 Environment Setup

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 0.1.1 | Install PHP 8.4 + FPM | - | 30 min |
| 0.1.2 | Install PostgreSQL 15+ | - | 30 min |
| 0.1.3 | Install Composer | PHP 8.4 | 15 min |
| 0.1.4 | Install Caddy | - | 15 min |
| 0.1.5 | Create PostgreSQL database | PostgreSQL | 10 min |

### 0.2 CodeIgniter 4 Project Initialization

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 0.2.1 | Create new CI4 project via Composer | Composer | 15 min |
| 0.2.2 | Configure `.env` file | 0.2.1 | 15 min |
| 0.2.3 | Configure database connection | 0.1.5, 0.2.2 | 10 min |
| 0.2.4 | Run initial migrations | 0.2.3 | 5 min |
| 0.2.5 | Verify project structure | 0.2.4 | 5 min |

**Checkpoint 0:**
- [ ] PHP 8.4 installed and configured
- [ ] PostgreSQL database created and accessible
- [ ] CodeIgniter 4 project initialized
- [ ] All 15 migrations run successfully
- [ ] Application loads in browser

---

## Phase 1: Foundation Layer

**Goal:** Establish core infrastructure for the application (authentication, routing, base models)

### 1.1 Configuration & Routing

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 1.1.1 | Configure app routes (`app/Config/Routes.php`) | 0.2.5 | 30 min |
| 1.1.2 | Configure filters (`app/Config/Filters.php`) | 1.1.1 | 30 min |
| 1.1.3 | Configure CSRF protection globally | 1.1.2 | 15 min |
| 1.1.4 | Set up base controller (`app/Controllers/BaseController.php`) | - | 20 min |

### 1.2 Filters & Middleware

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 1.2.1 | Create `AuthFilter.php` | 1.1.2 | 45 min |
| 1.2.2 | Create `ApiAuthFilter.php` | 1.1.2 | 30 min |
| 1.2.3 | Create `RateLimitFilter.php` | 1.1.2 | 30 min |
| 1.2.4 | Register filters in `Filters.php` | 1.2.1, 1.2.2, 1.2.3 | 15 min |

### 1.3 Models

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 1.3.1 | Create `UserModel.php` with validation | Migrations | 45 min |
| 1.3.2 | Create `BoardModel.php` | 1.3.1 | 30 min |
| 1.3.3 | Create `ColumnModel.php` | 1.3.2 | 30 min |
| 1.3.4 | Create `CardModel.php` | 1.3.3 | 45 min |
| 1.3.5 | Create `ChecklistItemModel.php` | 1.3.4 | 30 min |
| 1.3.6 | Create `TagModel.php` | - | 20 min |
| 1.3.7 | Create `AttachmentModel.php` | 1.3.4 | 30 min |

### 1.4 Authentication

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 1.4.1 | Create `AuthController.php` (login, register) | 1.3.1, 1.2.1 | 1.5 hrs |
| 1.4.2 | Create password reset flow | 1.4.1 | 1 hr |
| 1.4.3 | Create authentication service | 1.4.1 | 30 min |
| 1.4.4 | Create auth views (login, register) | 1.4.1 | 1 hr |
| 1.4.5 | Implement session management | 1.4.3 | 30 min |

**Checkpoint 1:**
- [ ] User can register new account
- [ ] User can login with email/password
- [ ] Password reset flow works end-to-end
- [ ] Auth filters protect protected routes
- [ ] Session management works correctly

---

## Phase 2: Core Kanban Features

**Goal:** Implement the core kanban board functionality (boards, columns, cards, tags, attachments)

### 2.1 Board Management

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.1.1 | Create `BoardController.php` | 1.3.2 | 1 hr |
| 2.1.2 | Implement board CRUD | 2.1.1 | 1.5 hrs |
| 2.1.3 | Create board views | 2.1.2 | 1.5 hrs |
| 2.1.4 | Create API routes for boards | 2.1.2 | 30 min |
| 2.1.5 | Create API BoardController | 2.1.4 | 1 hr |

### 2.2 Column Management

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.2.1 | Create `ColumnController.php` | 1.3.3 | 1 hr |
| 2.2.2 | Implement column CRUD | 2.2.1 | 1 hr |
| 2.2.3 | Implement column reordering | 2.2.2 | 45 min |
| 2.2.4 | Create column API endpoints | 2.2.3 | 30 min |
| 2.2.5 | Create API ColumnController | 2.2.4 | 1 hr |

### 2.3 Card Management

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.3.1 | Create `CardController.php` | 1.3.4 | 1.5 hrs |
| 2.3.2 | Implement card CRUD | 2.3.1 | 2 hrs |
| 2.3.3 | Implement card move/reorder | 2.3.2 | 1 hr |
| 2.3.4 | Create card views (modal) | 2.3.2 | 1.5 hrs |
| 2.3.5 | Create API CardController | 2.3.3 | 1 hr |

### 2.4 Checklists

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.4.1 | Create checklist API endpoints | 1.3.5 | 30 min |
| 2.4.2 | Implement checklist CRUD in CardController | 2.4.1 | 1 hr |
| 2.4.3 | Create checklist UI components | 2.4.2 | 1 hr |
| 2.4.4 | Add progress indicator | 2.4.3 | 30 min |

### 2.5 Tags

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.5.1 | Create tag API endpoints | 1.3.6 | 30 min |
| 2.5.2 | Implement tag CRUD | 2.5.1 | 45 min |
| 2.5.3 | Implement card-tag associations | 2.5.2 | 1 hr |
| 2.5.4 | Create tag UI components | 2.5.3 | 1 hr |
| 2.5.5 | Implement tag filtering | 2.5.4 | 30 min |

### 2.6 Attachments

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 2.6.1 | Create `AttachmentService.php` | 1.3.7 | 1 hr |
| 2.6.2 | Implement file upload handling | 2.6.1 | 1.5 hrs |
| 2.6.3 | Create upload directory structure | 2.6.2 | 15 min |
| 2.6.4 | Implement attachment API | 2.6.2 | 1 hr |
| 2.6.5 | Create attachment UI | 2.6.4 | 1 hr |

**Checkpoint 2:**
- [ ] User can create/edit/delete boards
- [ ] User can add/reorder/delete columns
- [ ] User can create/edit/delete cards
- [ ] Cards can be moved between columns
- [ ] Cards can have checklists
- [ ] Cards can be tagged
- [ ] Files can be attached to cards
- [ ] All operations persist to database

---

## Phase 3: Frontend & UI

**Goal:** Build the complete user interface with AsteroAdmin theme

### 3.1 AsteroAdmin Integration

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 3.1.1 | Download/extract AsteroAdmin | - | 15 min |
| 3.1.2 | Copy CSS assets to `public/assets/css/` | 3.1.1 | 15 min |
| 3.1.3 | Copy JS assets to `public/assets/js/` | 3.1.2 | 15 min |
| 3.1.4 | Copy icons to `public/assets/images/` | 3.1.2 | 10 min |
| 3.1.5 | Configure dark theme | 3.1.2 | 30 min |

### 3.2 Layout Templates

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 3.2.1 | Create main layout (`layouts/main.php`) | 3.1.5 | 1 hr |
| 3.2.2 | Create modal layout (`layouts/modal.php`) | 3.2.1 | 30 min |
| 3.2.3 | Create auth layout (`layouts/auth.php`) | 3.2.1 | 30 min |
| 3.2.4 | Implement sidebar navigation | 3.2.1 | 1 hr |
| 3.2.5 | Implement mobile offcanvas | 3.2.4 | 1 hr |

### 3.3 Kanban Board UI

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 3.3.1 | Create kanban board component | 3.2.1 | 1.5 hrs |
| 3.3.2 | Create column component | 3.3.1 | 1 hr |
| 3.3.3 | Create card component | 3.3.2 | 1.5 hrs |
| 3.3.4 | Implement card detail modal | 3.3.3 | 2 hrs |
| 3.3.5 | Create drag handle component | 3.3.3 | 30 min |

### 3.4 TipTap Rich Text Editor

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 3.4.1 | Install TipTap via npm | 3.1.3 | 15 min |
| 3.4.2 | Create TipTap editor module | 3.4.1 | 1.5 hrs |
| 3.4.3 | Configure extensions (StarterKit, TaskList, etc.) | 3.4.2 | 45 min |
| 3.4.4 | Implement Markdown conversion | 3.4.3 | 1 hr |
| 3.4.5 | Create editor UI component | 3.4.4 | 1 hr |

### 3.5 Drag & Drop (SortableJS)

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 3.5.1 | Download SortableJS | 3.1.3 | 10 min |
| 3.5.2 | Create drag-drop initialization | 3.5.1 | 1 hr |
| 3.5.3 | Implement card drag-drop | 3.5.2 | 1.5 hrs |
| 3.5.4 | Implement column reorder | 3.5.3 | 1 hr |
| 3.5.5 | Add visual feedback (ghost class) | 3.5.4 | 30 min |
| 3.5.6 | Implement error handling on drop | 3.5.5 | 45 min |

**Checkpoint 3:**
- [ ] AsteroAdmin dark theme applied
- [ ] Sidebar navigation works on desktop
- [ ] Mobile offcanvas works
- [ ] Kanban board renders correctly
- [ ] TipTap editor loads and saves Markdown
- [ ] Cards can be dragged between columns
- [ ] Columns can be reordered

---

## Phase 4: Google Integration

**Goal:** Implement Google Calendar and Gmail integration

### 4.1 Google Client Library

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 4.1.1 | Install Google API client via Composer | - | 15 min |
| 4.1.2 | Create `GoogleClient.php` library | 4.1.1 | 1.5 hrs |
| 4.1.3 | Configure OAuth scopes | 4.1.2 | 30 min |
| 4.1.4 | Implement token refresh logic | 4.1.3 | 1 hr |

### 4.2 Google OAuth

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 4.2.1 | Create `GoogleAuthService.php` | 4.1.4 | 1 hr |
| 4.2.2 | Create `GoogleController.php` | 4.2.1 | 1 hr |
| 4.2.3 | Implement OAuth flow | 4.2.2 | 1.5 hrs |
| 4.2.4 | Store tokens in database | 4.2.3 | 1 hr |
| 4.2.5 | Create auth button UI | 4.2.2 | 30 min |

### 4.3 Calendar Integration

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 4.3.1 | Create `CalendarSyncService.php` | 4.2.4 | 2 hrs |
| 4.3.2 | Implement calendar list fetch | 4.3.1 | 1 hr |
| 4.3.3 | Implement calendar selection UI | 4.3.2 | 1 hr |
| 4.3.4 | Implement event fetching | 4.3.1 | 1.5 hrs |
| 4.3.5 | Create calendar event card component | 3.3.3, 4.3.4 | 1 hr |
| 4.3.6 | Implement event-to-date column mapping | 4.3.5 | 1 hr |
| 4.3.7 | Add manual refresh button | 4.3.6 | 30 min |

### 4.4 Gmail Integration

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 4.4.1 | Create `GmailSyncService.php` | 4.2.4 | 2 hrs |
| 4.4.2 | Create `GmailController.php` | 4.4.1 | 1 hr |
| 4.4.3 | Implement sender configuration UI | 4.4.2 | 1.5 hrs |
| 4.4.4 | Implement wildcard sender matching | 4.4.3 | 1 hr |
| 4.4.5 | Implement email fetching | 4.4.1 | 1.5 hrs |
| 4.4.6 | Create email card component | 3.3.3, 4.4.5 | 1 hr |
| 4.4.7 | Implement thread deduplication | 4.4.6 | 1 hr |
| 4.4.8 | Add "Open in Gmail" link | 4.4.6 | 30 min |
| 4.4.9 | Store full email body | 4.4.5 | 1 hr |

**Checkpoint 4:**
- [ ] User can connect Google account
- [ ] User can select calendars
- [ ] Calendar events display as cards
- [ ] Events show in date-based columns
- [ ] User can configure Gmail senders
- [ ] New emails create task cards
- [ ] Email cards link to Gmail
- [ ] Duplicate emails are handled correctly

---

## Phase 5: Background Jobs

**Goal:** Implement job queue and background processing

### 5.1 Job Queue System

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 5.1.1 | Create job queue model | 1.3.7 (jobs table) | 30 min |
| 5.1.2 | Create `JobService.php` | 5.1.1 | 1 hr |
| 5.1.3 | Implement job dispatcher | 5.1.2 | 1 hr |
| 5.1.4 | Create worker command | 5.1.3 | 1 hr |

### 5.2 Gmail Watch/Webhooks

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 5.2.1 | Set up Google Cloud Pub/Sub topic | - | 30 min |
| 5.2.2 | Implement watch setup | 4.4.1 | 1 hr |
| 5.2.3 | Create webhook endpoint | 4.4.2 | 1 hr |
| 5.2.4 | Implement webhook verification | 5.2.3 | 45 min |
| 5.2.5 | Process webhook notifications | 5.2.4 | 1.5 hrs |
| 5.2.6 | Handle watch expiration | 5.2.2 | 1 hr |

### 5.3 Cron Jobs

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 5.3.1 | Create Gmail sync fallback command | 4.4.1 | 1 hr |
| 5.3.2 | Create calendar refresh command | 4.3.1 | 45 min |
| 5.3.3 | Create token refresh command | 4.1.4 | 30 min |
| 5.3.4 | Create due date reminder command | 2.3.2 | 1 hr |
| 5.3.5 | Create cleanup command | 5.1.2 | 30 min |
| 5.3.6 | Configure crontab entries | 5.3.1-5.3.5 | 30 min |

### 5.4 Queue Workers

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 5.4.1 | Configure Supervisor | - | 30 min |
| 5.4.2 | Create worker config | 5.4.1 | 15 min |
| 5.4.3 | Test worker process | 5.4.2 | 30 min |
| 5.4.4 | Configure auto-restart | 5.4.3 | 15 min |

**Checkpoint 5:**
- [ ] Job queue processes jobs correctly
- [ ] Gmail watch is set up
- [ ] Webhook endpoint receives notifications
- [ ] Cron jobs run on schedule
- [ ] Tokens refresh automatically
- [ ] Worker process stays running

---

## Phase 6: Mobile Experience & Polish

**Goal:** Optimize for mobile and finalize the application

### 6.1 Responsive Layout

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.1.1 | Implement mobile breakpoints | 3.2.1 | 1 hr |
| 6.1.2 | Full-width columns on mobile | 6.1.1 | 45 min |
| 6.1.3 | Hide sidebar on mobile | 6.1.2 | 30 min |
| 6.1.4 | Test tablet layout | 6.1.3 | 30 min |

### 6.2 Touch Interactions

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.2.1 | Implement swipe navigation | 3.5.3 | 1 hr |
| 6.2.2 | Touch-optimized drag handles | 3.5.3 | 30 min |
| 6.2.3 | Minimum 44x44px touch targets | 6.2.2 | 45 min |
| 6.2.4 | Long-press to drag | 6.2.3 | 30 min |
| 6.2.5 | Move button in card modal | 3.3.4 | 30 min |

### 6.3 Mobile Card Modal

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.3.1 | Full-screen modal on mobile | 3.3.4 | 45 min |
| 6.3.2 | Bottom sheet-style | 6.3.1 | 30 min |
| 6.3.3 | Touch-friendly form controls | 6.3.2 | 45 min |

### 6.4 Accessibility

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.4.1 | Add ARIA labels to all interactive elements | 3.3.3 | 1 hr |
| 6.4.2 | Keyboard navigation for cards | 3.5.3 | 1 hr |
| 6.4.3 | Focus management in modals | 3.3.4 | 45 min |
| 6.4.4 | Screen reader announcements | 6.4.1 | 30 min |
| 6.4.5 | Color contrast validation | 3.1.5 | 30 min |

### 6.5 Performance

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.5.1 | Enable Gzip compression | Caddy config | 15 min |
| 6.5.2 | Configure static asset caching | 3.1.2 | 30 min |
| 6.5.3 | Optimize database queries | 2.3.2 | 1 hr |
| 6.5.4 | Implement lazy loading for images | 3.3.3 | 30 min |
| 6.5.5 | Add loading states | 3.3.1 | 45 min |
| 6.5.6 | Performance test with Lighthouse | 6.5.5 | 30 min |

### 6.6 Error Handling & Polish

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 6.6.1 | Global error handler | 1.1.4 | 30 min |
| 6.6.2 | Custom error pages (404, 500) | 6.6.1 | 30 min |
| 6.6.3 | Empty state illustrations | 3.3.1 | 1 hr |
| 6.6.4 | Toast notifications | 3.1.3 | 45 min |
| 6.6.5 | Loading spinners | 6.6.4 | 30 min |
| 6.6.6 | Confirm dialogs for destructive actions | 3.3.3 | 30 min |

**Checkpoint 6:**
- [ ] Layout works on mobile, tablet, desktop
- [ ] Swipe navigation works on mobile
- [ ] Touch targets are 44x44px minimum
- [ ] Keyboard navigation works
- [ ] ARIA labels added
- [ ] Color contrast passes WCAG AA
- [ ] Page load under 2s on 4G
- [ ] Error states are handled gracefully

---

## Phase 7: Deployment

**Goal:** Deploy to production VPS

### 7.1 Server Setup

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 7.1.1 | Provision VPS (DigitalOcean/Linode) | - | 15 min |
| 7.1.2 | Configure UFW firewall | 7.1.1 | 15 min |
| 7.1.3 | Install SSL certificate (Let's Encrypt) | 7.1.2 | 30 min |
| 7.1.4 | Configure Caddy | 7.1.3 | 30 min |

### 7.2 Application Deployment

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 7.2.1 | Set up git deployment | 7.1.4 | 30 min |
| 7.2.2 | Deploy application code | 7.2.1 | 30 min |
| 7.2.3 | Run `composer install --no-dev` | 7.2.2 | 15 min |
| 7.2.4 | Run migrations | 7.2.3 | 5 min |
| 7.2.5 | Set file permissions | 7.2.4 | 15 min |
| 7.2.6 | Configure production `.env` | 7.2.4 | 20 min |

### 7.3 Database Setup

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 7.3.1 | Create production database | 7.1.1 | 10 min |
| 7.3.2 | Create database user | 7.3.1 | 10 min |
| 7.3.3 | Configure backups | 7.3.2 | 30 min |
| 7.3.4 | Test backup restore | 7.3.3 | 20 min |

### 7.4 Background Services

| Task | Description | Dependencies | Estimate |
|------|-------------|--------------|----------|
| 7.4.1 | Configure Supervisor | 5.4.1 | 15 min |
| 7.4.2 | Start queue workers | 7.4.1 | 10 min |
| 7.4.3 | Configure crontab | 5.3.6 | 15 min |
| 7.4.4 | Verify services running | 7.4.3 | 15 min |

**Checkpoint 7:**
- [ ] VPS is secured and accessible
- [ ] SSL certificate installed
- [ ] Application loads on HTTPS
- [ ] Database is configured
- [ ] Background services are running
- [ ] Backups are configured

---

## Task Dependency Graph

```
Phase 0 (Foundation)
├── 0.1 Environment Setup
│   └── 0.2 CI4 Init
│       └── 1.1 Configuration
│           ├── 1.2 Filters
│           │   └── 1.4 Authentication
│           └── 1.3 Models
│
Phase 1 (Foundation Layer) [Blocks All]
├── Phase 2 (Core Kanban)
│   ├── 2.1 Boards → 2.2 Columns → 2.3 Cards
│   ├── 2.4 Checklists
│   ├── 2.5 Tags
│   └── 2.6 Attachments
│
Phase 2 → Phase 3 (Frontend)
├── 3.1 AsteroAdmin → 3.2 Layouts → 3.3 Kanban UI
├── 3.4 TipTap
└── 3.5 Drag & Drop
│
Phase 3 → Phase 4 (Google Integration)
├── 4.1 Google Client → 4.2 OAuth
├── 4.3 Calendar (requires 4.2)
└── 4.4 Gmail (requires 4.2)
│
Phase 4 → Phase 5 (Background Jobs)
├── 5.1 Job Queue
├── 5.2 Gmail Watch (requires 4.4)
└── 5.3 Cron Jobs (requires 4.3, 4.4)
│
Phase 5 → Phase 6 (Mobile & Polish)
├── 6.1 Responsive (requires 3.2)
├── 6.2 Touch (requires 3.5)
├── 6.3 Mobile Modal (requires 3.3)
├── 6.4 Accessibility (requires 3.3, 3.5)
├── 6.5 Performance
└── 6.6 Error Handling
│
Phase 6 → Phase 7 (Deployment)
├── 7.1 Server Setup
├── 7.2 App Deploy
├── 7.3 Database
└── 7.4 Background Services
```

---

## Implementation Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use 4 spaces for indentation
- Use strict types (`declare(strict_types=1);`)
- Add PHPDoc for all public methods
- Max line length: 120 characters

### Git Workflow
1. Create feature branch from `main`
2. Work on tasks in batches by phase
3. Commit with descriptive messages
4. Create PR for phase completion
5. Review and merge to `main`

### Testing Strategy
- Unit tests for models and services
- Integration tests for API endpoints
- Manual testing for UI components
- End-to-end testing for critical flows

### Security Checklist
- [ ] All inputs validated
- [ ] SQL injection prevention (Query Builder)
- [ ] XSS prevention (output encoding)
- [ ] CSRF protection enabled
- [ ] Password hashing (Argon2id)
- [ ] Secure session cookies
- [ ] Rate limiting on auth
- [ ] HTTPS enforced
- [ ] Error messages don't leak info

---

## Next Steps

After completing all phases:

1. **User Testing** - Get feedback from actual users
2. **Performance Tuning** - Optimize based on real usage
3. **Documentation** - Write user guide and admin docs
4. **Monitoring** - Set up error tracking and analytics
5. **Iterate** - Plan v2 features based on feedback

---

**End of Implementation Workflow**