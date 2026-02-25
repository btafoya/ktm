# Kanban Task Manager (`github.com/btafoya/ktm`)

## Project References

- **SCOPE.md** - Complete requirements specification (FRs, NFRs, user stories, tech stack)
- **.claude/skills/** - Specialized skills for frontend design, testing, and workflow orchestration
- **PR.md** - Pull request focus and open issues

**Always consult SCOPE.md before making implementation decisions.**

## Autonomous Work Mode

**Claude Code is authorized to work autonomously on this project.** When given a task:

1. **Proceed without asking for confirmation** - Execute the full task from start to finish
2. **Make reasonable decisions** - Use best judgment for implementation details
3. **Follow established patterns** - Match existing code style and project conventions
4. **Complete the work** - Don't stop mid-task or leave partial implementations
5. **Report results** - Summarize what was done when complete
6. **Compact conversation** - When you are using compact, please focus on test output and code changes

### When to Ask Questions
- Requirements are genuinely ambiguous with multiple valid interpretations
- Security implications require explicit user approval
- Destructive operations (deleting data, force push) need confirmation
- The task fundamentally contradicts project requirements

### When NOT to Ask Questions
- Implementation details (which pattern to use, naming conventions)
- File organization decisions that follow existing patterns
- Code style choices that match the codebase
- Standard software engineering decisions

## Guidelines

### ❌ Do NOT Include:
- "Generated with Claude Code" in commit messages
- "Co-Authored-By: Claude Sonnet" in commits
- AI attribution in code comments
- References to Claude in documentation footer/header

### ✅ DO Include:
- Your name and email as the commit author
- Professional commit messages describing WHAT changed
- Standard documentation without AI tool references
- Human authorship for all contributions

## Commit Message Standards

**Good Commit Messages:**
```
Add payment gateway integration with Stripe and PayPal
Update RBAC schema to support multi-agency isolation
Implement webhook handlers for automatic payment confirmation
```

**Bad Commit Messages:**
```
Generated with Claude Code
Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>
Add feature (created with AI assistance)
```

## Rationale

- **Professionalism**: Code should reflect human authorship
- **Clarity**: Commit history should describe changes, not tools used
- **Standards**: Follow industry-standard Git practices
- **Ownership**: Maintain clear project ownership and responsibility

## Anti-Slop Guidelines

Avoid "AI slop" patterns - telltale signs of generic, low-quality AI-generated content.

### Natural Language Quality

**Avoid these high-risk phrases:**
- "delve into" / "dive deep into"
- "navigate the complexities"
- "in the ever-evolving landscape"
- "in today's fast-paced world"
- "in today's digital age"
- "at the end of the day"
- "it's important to note that"
- "it's worth noting that"

**Also minimize:**
- Meta-commentary about what the response will cover
- Excessive hedging ("may or may not", "could potentially")
- Corporate buzzwords ("leverage", "synergistic", "paradigm shift")
- Redundant qualifiers ("completely finish", "absolutely essential")
- Unnecessary intensifiers ("very unique", "really important")

**Quality principles:**
- Be direct: Skip preambles, lead with the actual point
- Be specific: Use concrete terms instead of generic placeholders
- Be authentic: Vary structure, use active voice, match context
- Be concise: Replace wordy phrases with simple alternatives

**Examples:**
- "in order to" → "to"
- "due to the fact that" → "because"
- "has the ability to" → "can"
- "delve into" → just say the thing directly
- "it's important to note that X" → just state X

### Code Quality

**Naming:**
- Avoid generic names: `data`, `result`, `temp`, `value`, `item`, `thing`
- Use specific, descriptive names that indicate purpose
- Keep names concise but meaningful

**Comments:**
- Avoid obvious comments that restate code
- Document "why" not "what"
- Skip comments for self-documenting code
- Focus documentation on complex logic and public APIs

**Structure:**
- Avoid unnecessary abstraction layers
- Don't apply design patterns without clear need
- Prefer simple solutions over complex ones
- Only optimize after profiling shows need

**Documentation:**
- Avoid generic docstrings that add no information
- Document behavior, edge cases, and assumptions
- Skip exhaustive docs for internal helpers
- Focus on what users/maintainers need to know

### Design Quality (UI/Artifacts)

**Visual elements:**
- Avoid default purple/pink/cyan gradient schemes
- Don't overuse glassmorphism, neumorphism, or floating 3D shapes
- Use effects purposefully, not decoratively
- Create hierarchy through intentional design choices

**Layout:**
- Design around actual content needs, not templates
- Vary visual treatment based on importance
- Use spacing to create meaningful groupings
- Consider alternatives to card-based layouts

**Copy:**
- Avoid generic marketing phrases ("Empower your business", "Transform your workflow")
- Use specific, action-oriented CTAs
- Match brand voice and tone
- Be concrete about value proposition

### Proactive Anti-Slop

Apply these principles proactively when:

1. **Creating substantial content** - For longer pieces (>500 words, >100 lines of code, full designs)
2. **Code review** - When reviewing or improving existing code
3. **Documentation** - When writing user-facing text
4. **UI design** - When creating interfaces, presentations, or artifacts

**Note:** Don't mention "anti-slop" terminology to users. Frame improvements as "clarity", "specificity", "directness", or "authenticity" improvements.

## Tool Usage

Claude Code is a development assistant tool, like an IDE or linter. You wouldn't attribute your IDE in commits, and the same applies to AI coding assistants.

Use Claude Code to:
- Generate code snippets and boilerplate
- Review and improve code quality
- Write documentation and specs
- Debug and troubleshoot issues

But always commit and sign work as **btafoya**.

## MCP Tools to use

- MemoryGraph MCP for memory
- Serena MCP for memory and tools
- Context7 MCP for library and implementation reference
- Playwright MCP for Browser testing

## Library Documentation Reference

**ALWAYS use the `context7` skill before writing code for any library or framework.** This prevents syntax errors and ensures compatibility with the correct library version.

### When to Use Context7

- Before implementing any CodeIgniter 4.x features (verify v4.7.0 syntax)
- Before using any PHP library API (verify current function signatures)
- Before implementing JavaScript framework code (check latest API changes)
- When unsure about correct method names, parameters, or return types
- Before applying code snippets that may be outdated

### CodeIgniter 4.7.0 Specifics

CodeIgniter 4.7.0 introduced breaking changes. Reference:
- Official changelog: https://github.com/codeigniter4/CodeIgniter4/blob/develop/CHANGELOG.md
- Always verify syntax against v4.7.0 documentation via context7

### How to Use

Invoke the context7 skill via the `/context7` command or Skill tool:

```
/context7 codeigniter4 models
```

This retrieves current documentation for the specified topic, preventing implementation errors due to outdated API knowledge.

### Workflow

1. Identify the library and topic (e.g., "codeigniter4 route filters")
2. Use context7 skill to fetch current documentation
3. Verify syntax matches the library version in use
4. Write implementation using confirmed API

## Project Skills

When working on this project, leverage the specialized skills in `.claude/skills/`:

| Skill | Purpose |
|-------|---------|
| `context7` | Retrieve up-to-date library documentation via Context7 API |
| `anti-slop` | Detect and eliminate generic AI patterns in code and content |
| `humanizer` | Remove AI writing patterns to make text sound natural |
| `agent-browser` | Browser automation for testing web applications |
| `frontend-design` | Build kanban interfaces using Bootstrap 5.3, jQuery, TipTap, SortableJS |
| `sc:*` | SuperClaude orchestration commands for workflows, design, implementation |

### Skill Activation Rules

- **Library documentation** - Use `context7` before writing code for any library (especially CodeIgniter 4.7.0)
- **Frontend tasks** - Use `frontend-design` skill for all kanban board UI work
- **Code quality** - Apply `anti-slop` before finalizing implementations
- **Documentation** - Run `humanizer` on user-facing text
- **Testing** - Use `agent-browser` for browser/functional testing
- **Complex workflows** - Leverage `sc:workflow` for multi-step implementation planning

### Operating Principles
- Work **autonomously**: do not ask for human confirmation unless the issue is ambiguous or lacks required information to proceed safely.
- Handle **one issue at a time** from start to finish before moving to the next.
- Prefer **minimal, correct changes** that align with KTM’s architecture and style.
- If a proposed fix affects public behavior or compatibility, document the impact explicitly in the issue file and ensure tests cover it.

### Safety & Scope
- Avoid configuration-breaking changes unless the issue explicitly requires it; if unavoidable, document migration steps.

## Documentation

### SCOPE.md

**This is the single source of truth for project requirements.** Before beginning any implementation:

1. **Read the relevant sections** of `SCOPE.md` to understand the requirement
2. **Map the requirement** to its FR (Functional Requirement) or NFR (Non-Functional Requirement) ID
3. **Verify technology stack** decisions in Section 3.1 before choosing libraries/tools
4. **Check resolved decisions** in Section 5.1 before making architectural choices

### Technology Stack Constraints

| Component | Required Technology | Notes |
|-----------|---------------------|-------|
| Backend | PHP 8.4 | CodeIgniter 4.x framework |
| Database | PostgreSQL 15+ | Primary data store |
| UI | Bootstrap 5.3 | Via AsteroAdmin theme, dark mode only |
| JS | jQuery 4 | For DOM manipulation |
| Editor | TipTap | Rich text WYSIWYG with Markdown storage |
| Drag-Drop | SortableJS | Columns and cards |
| Icons | Bootstrap Icons | Matches Bootstrap theme |

### Issues

Document all issues and their status using markdown with the naming format: `ISSUE{number}.md`

## File Creation Quality Standards

When creating files, ensure high-quality, authentic content:

### For documents (markdown, reports):
- Lead with actual content, not meta-commentary
- Use specific, concrete language
- Avoid buzzword-heavy corporate speak
- Create clear hierarchy through structure, not just formatting

### For code files:
- Use descriptive, specific variable and function names
- Avoid obvious comments that restate code
- Implement solutions appropriate to complexity
- Document behavior and edge cases, not syntax
- Prefer clarity over cleverness

### For HTML/UI artifacts:
- Design around user needs and content
- Avoid generic gradient backgrounds and cookie-cutter layouts
- Use specific copy instead of placeholder buzzwords
- Create intentional visual hierarchy
- Ensure accessibility through contrast and clear structure
