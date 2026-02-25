---
name: frontend-design
description: Kanban Board Frontend Design - Build distinctive kanban interfaces using Bootstrap 5.3 (AsteroAdmin), jQuery 4, TipTap, and SortableJS. Dark theme only, responsive, mobile-first design with sidebar/offcanvas navigation.
license: Complete terms in LICENSE.txt
---

This skill guides frontend development for the Kanban Board with Google Calendar & Gmail Integration project. All UI must follow the established dark theme, AsteroAdmin-based design system with Bootstrap 5.3, jQuery 4, TipTap editor, and SortableJS for drag-drop functionality.

## Project Context

**Application**: Personal kanban task management with Google Calendar and Gmail integration
**Target Users**: Individuals managing tasks, appointments, and email-triggered tasks
**Platform**: Web (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+, Mobile)

## Technology Stack

| Component | Technology | Purpose |
|-----------|-----------|---------|
| UI Framework | Bootstrap 5.3.3 | Base styling, responsive layout, components |
| Admin Template | AsteroAdmin | Dark theme, sidebar, offcanvas, modals, cards |
| Frontend | jQuery 4 | DOM manipulation, AJAX, form handling |
| Rich Text Editor | TipTap | WYSIWYG markdown editor for card descriptions |
| Icons | Bootstrap Icons | Iconography throughout the interface |
| Drag-Drop | SortableJS | Card and column reordering with touch support |
| Backend | CodeIgniter 4 + PHP 8.4 | REST API backend |
| Database | PostgreSQL 15+ | Data persistence |

## Layout Architecture

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

### Responsive Breakpoints

| Breakpoint | Layout | Navigation |
|------------|--------|------------|
| Desktop (≥992px) | Sidebar + Main Content | Click sidebar items |
| Tablet (768-991px) | Collapsible Sidebar | Click to expand/collapse |
| Mobile (<768px) | Offcanvas Drawer | Swipe/hamburger to open |

## Design Requirements

### Theme
- **Dark mode ONLY** - No light mode toggle or support
- Base: AsteroAdmin dark theme variables
- WCAG AA contrast ratios required for accessibility

### Typography
- Use Bootstrap's default typography stack (matches AsteroAdmin)
- Headings: Bootstrap h1-h6 classes
- Body: Bootstrap base font-size and line-height
- Monospace: `font-monospace` class for code blocks

### Color System

Use AsteroAdmin dark theme CSS variables as base. Override with project-specific colors for kanban elements:

| Purpose | CSS Variable | Value (Dark Theme) |
|---------|--------------|-------------------|
| Background | `--bs-body-bg` | Dark gray/black (AsteroAdmin default) |
| Card Background | `--bs-card-bg` | Slightly lighter than body |
| Border | `--bs-border-color` | Subtle border color |
| Primary | `--bs-primary` | Accent color (brand color) |
| Success | `--bs-success` | Green for positive states |
| Danger | `--bs-danger` | Red for delete/error |
| Warning | `--bs-warning` | Yellow/orange for due dates |
| Info | `--bs-info` | Blue for informational |

### Card Color Coding
Cards support user-defined background colors. Use Bootstrap color classes or custom hex values:
- Red (`bg-danger`) - High priority/urgent
- Yellow (`bg-warning`) - Medium priority
- Green (`bg-success`) - Low priority/completed
- Blue (`bg-info`) - Informational
- Purple (`bg-primary` or custom) - Default task
- Gray (`bg-secondary`) - Neutral

## Component Patterns

### AsteroAdmin Components to Use

| Component | Kanban Usage |
|-----------|--------------|
| Sidebar/Aside | Board list navigation (left side on desktop) |
| Offcanvas | Mobile board drawer (slides in from left) |
| Modal | Card detail, column edit, settings dialogs |
| Dropdown | Filters, column selector, actions menu |
| Card/Panel | Kanban card styling base |
| Buttons | New card, new column, refresh, etc. |
| Form Controls | Login, registration, card editing |
| Toast/Alert | Success/error notifications |

### Custom Components (Not from AsteroAdmin)

| Component | Implementation Details |
|-----------|------------------------|
| Kanban Columns | CSS Grid + horizontal scroll, SortableJS for reordering |
| Drag Handles | Custom `.drag-handle` element on cards, SortableJS handle option |
| Card Drag-Drop | SortableJS with `animation: 150`, touch support enabled |
| WYSIWYG Editor | TipTap headless editor, Markdown storage |
| Calendar Event Cards | Special styling, locked icon, distinct border color |
| Email Task Cards | Gmail icon, "Open in Gmail" button, snippet preview |
| Tag/Label | Bootstrap badges with custom colors |

## Design Thinking for Kanban

Before coding, consider:

**Purpose**: Users need to quickly scan tasks, move them between stages, and access details. Drag-drop must be smooth and intuitive.

**Card Visibility**: Each card should show:
- Title (truncated if too long)
- Priority indicator (color/badge)
- Due date (if set, with visual cue for overdue)
- Tags (first 2-3 visible, "+X" for more)
- Drag handle (always visible)
- Attachment count (icon with number if >0)

**Column Layout**: Horizontal scroll for columns on desktop, full-width single column on mobile with swipe navigation.

**Visual Hierarchy**:
1. Active card being dragged (elevated, shadow)
2. Overdue cards (red accent)
3. High priority (red background/badge)
4. Today's calendar events (distinct border, calendar icon)
5. Email cards (gmail icon, different background)

## Frontend Implementation Guidelines

### CSS Organization
```
public/
├── css/
│   ├── bootstrap.min.css          # Bootstrap 5.3.3 (AsteroAdmin)
│   ├── theme.css                  # AsteroAdmin dark theme overrides
│   └── kanban.css                 # Project-specific styles
```

### JavaScript Organization
```
public/
├── js/
│   ├── bootstrap.bundle.min.js    # Bootstrap JS (includes Popper)
│   ├── sortablejs.min.js          # SortableJS library
│   ├── tiptap.min.js              # TipTap editor
│   └── kanban.js                  # Project-specific jQuery code
```

### jQuery Pattern
```javascript
$(document).ready(function() {
    // Initialize SortableJS for columns
    $('.kanban-columns').each(function() {
        new Sortable(this, {
            animation: 150,
            handle: '.column-header',
            ghostClass: 'sortable-ghost'
        });
    });

    // Initialize SortableJS for cards within each column
    $('.kanban-column').each(function() {
        new Sortable(this, {
            animation: 150,
            handle: '.drag-handle',
            group: 'kanban-cards',
            onEnd: function(evt) {
                // Save new order via AJAX
                saveCardOrder(evt);
            }
        });
    });
});
```

### TipTap Integration
```javascript
const editor = new Editor({
    element: document.querySelector('#card-description'),
    extensions: [
        StarterKit,
        Placeholder.configure({ placeholder: 'Add description...' }),
        // Add TipTap extensions as needed
    ],
    content: initialMarkdown,
    editorProps: {
        attributes: { class: 'form-control bg-dark text-light' }
    }
});
```

## Accessibility Requirements

- **Keyboard Navigation**: All features accessible via keyboard (Tab, Enter, Space, Arrow keys)
- **ARIA Labels**: All interactive elements must have aria-label or aria-labelledby
- **Touch Targets**: Minimum 44x44px for all touch controls (FR-093)
- **Color Contrast**: WCAG AA contrast ratios in dark theme (NFR-022)
- **Focus Indicators**: Visible focus states on all interactive elements

## Performance Requirements

- **Drag Performance**: Smooth 60fps drag animations (NFR-002)
- **Initial Load**: Page load under 2 seconds on 4G (NFR-001)
- Use pre-built AsteroAdmin assets (no build step required)
- Lazy load images in card descriptions
- Debounce search/filter inputs

## Mobile Specifics

| Requirement | Implementation |
|-------------|----------------|
| Swipe Navigation | Left/right swipe to move between columns (FR-090) |
| Full-Width Columns | Single column view, horizontal pagination (FR-091) |
| Bottom Navigation | Primary actions via bottom nav (FR-094) |
| Touch Targets | All controls min 44x44px (FR-093) |
| Long-Press Drag | Long-press anywhere on card initiates drag (FR-083) |

## Special Card Types

### Calendar Event Cards (Read-Only)
- Distinct background (slightly different from task cards)
- Calendar icon badge
- Locked/padlock icon indicating read-only
- Event time prominently displayed
- "View Event" button opens full event details modal
- Cannot be edited, deleted, or moved

### Email Task Cards
- Gmail icon badge
- Subject as card title
- Sender name displayed
- Snippet preview (truncated)
- "Open in Gmail" button/link
- Can be moved between columns
- Can be marked as done (dismiss)

## Code Examples

### Card HTML Structure
```html
<div class="card kanban-card mb-2" data-card-id="123" data-color="danger">
    <div class="card-header d-flex align-items-center p-2">
        <div class="drag-handle me-2" aria-label="Drag to reorder">
            <i class="bi bi-grip-vertical"></i>
        </div>
        <h6 class="card-title mb-0 flex-grow-1 text-truncate">Card Title</h6>
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-light" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
            </button>
            <!-- Dropdown menu -->
        </div>
    </div>
    <div class="card-body p-2">
        <div class="badges mb-2">
            <span class="badge bg-danger">High</span>
            <span class="badge bg-primary">Work</span>
        </div>
        <div class="due-date text-warning small">
            <i class="bi bi-clock"></i> Today
        </div>
    </div>
</div>
```

### Column HTML Structure
```html
<div class="kanban-column col" data-column-id="1">
    <div class="card h-100 bg-dark border-secondary">
        <div class="card-header d-flex justify-content-between align-items-center column-header">
            <h5 class="mb-0">To Do</h5>
            <div class="column-actions">
                <button class="btn btn-sm btn-outline-light add-card-btn">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <!-- Cards go here -->
        </div>
    </div>
</div>
```

## Never Do

- Do NOT add light mode support (dark only)
- Do NOT use Inter, Roboto, or Arial as primary fonts (use Bootstrap defaults)
- Do NOT use purple gradients on white (AI slop)
- Do NOT use generic "card" class from AsteroAdmin without kanban-specific customization
- Do NOT use JavaScript frameworks like React/Vue (use jQuery 4)
- Do NOT skip drag handles (must be visible for precise control)
- Do NOT ignore mobile swipe navigation (FR-090)
- Do NOT use build tools (copy pre-built AsteroAdmin assets only)
- Do NOT use FontAwesome (use Bootstrap Icons)

## Always Do

- Always use AsteroAdmin dark theme as base
- Always ensure 60fps drag animations
- Always add drag handles to cards
- Always show visual feedback for async operations
- Always use SortableJS for drag-drop
- Always use TipTap for rich text editing
- Always use Bootstrap Icons
- Always ensure WCAG AA contrast
- Always test on mobile breakpoint (<768px)
- Always include ARIA labels for accessibility

## Page Templates to Create

1. **Login/Register Page** - Auth forms using AsteroAdmin card styling
2. **Main Dashboard** - Sidebar + kanban board layout
3. **Board Settings Modal** - Column management, Google Calendar config, Gmail config
4. **Card Detail Modal** - Full card editor with TipTap, attachments, checklist
5. **Calendar Event Modal** - Read-only event details
6. **Profile Settings Page** - User account management
7. **Empty States** - Helpful messages for empty boards/columns

---

**Remember**: This is a productivity tool, not an art piece. Design should enhance usability first—clarity, speed, and reliability are paramount. The aesthetic serves the function: helping users organize tasks efficiently.