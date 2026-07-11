# CLAUDE.md

This file provides guidance to Claude Code when working with code in this repository.

## Language rules — critical

- **All communication with the developer is in English, always.** Never respond in Slovenian, regardless of what language appears in the codebase.
- **Code comments in the existing codebase are in Slovenian** — this is intentional and must be preserved. When editing existing Slovenian comments, keep them in Slovenian. When adding new comments to existing Slovenian-commented files, match the existing language for consistency.
- **Front-end user-facing labels/strings are in Slovenian** (e.g. "ČAS", "IZOBRAŽEVANJE", "Nalagam podatke …") — this is correct and intentional, the plugin displays on a school's TV screens for Slovenian users. Do not translate these to English.
- **New code you write**: variable names, function names, class names in English as per WordPress/PHP convention. Only user-facing display strings and comments follow the Slovenian pattern already established in the file you're editing.
- If unsure whether something should be Slovenian or English, ask rather than guess.

## What this plugin is

SNIPI Ekrani displays live lecture/course schedules from the Snipi CRM API on a smart TV screen in the school hallway (upi.si / Šola). Non-interactive display — the TV cannot be clicked, so all content must be either fully automated (live schedule data) or static (admin-configured promo content).

## Stack

- WordPress 6.7+, PHP 8.1+
- Custom Post Type based — each "screen" is a post with meta fields
- Meta Box plugin used for CPT custom fields (not raw WP meta boxes)
- Vanilla JS front-end (no build step, no framework) — front.js, tv.js, admin.js, admin-styling.js
- REST API: custom `snipi/v1` namespace

## Architecture

```
includes/
├── Admin/          → CPT admin screens, settings tabs, styling GUI, meta fields
├── Api/            → Snipi API integration (class-data-service.php) 
│                     + REST endpoints (class-rest-controller.php)
└── Front/          → Front-end rendering (class-renderer.php) 
                      + shortcode (class-shortcode.php)

assets/
├── css/            → front.css (display), admin.css, admin-styling.css, tv.css
└── js/             → front.js (pagination/display logic), tv.js (TV detection), 
                      admin.js, admin-styling.js
```

### Data flow
```
Snipi API (upi.snipi.si/api/Scheduler/GetTimeSlots)
    ↓
SNIPI_Data_Service::get_timeslots()
  - normalizes timestamps to Europe/Ljubljana timezone
  - filters out events that already ended
  - sorts by start time ascending
    ↓
SNIPI_REST_Controller (wp-json/snipi/v1/ekrani/timeslots)
  - additional server-side date range filtering
  - returns items + display config (logo, bottom row, pagination settings)
    ↓
front.js polls this endpoint, renders table, handles pagination/autoplay
```

### CSS scoping
Each screen gets `.snipi--screen-{post_id}` wrapper class so multiple screens on the same site can have independent styling without conflicts. Styling is admin-configurable via a GUI (class-admin-styling-tab.php) and compiled to CSS by `SNIPI_Renderer::generate_styling_css()`.

### TV detection
`tv.js` auto-detects smart TVs via user agent + screen resolution, applies TV-optimized column/font/card sizing via CSS custom properties. Has manual override (auto/tv/desktop) and a confirm dialog with localStorage persistence per screen ID.

### Key existing settings (post meta)
```
_snipi_api_key              → Snipi API key for this screen
_snipi_future_days           → 0-30 day window for upcoming events
_snipi_weekend_mode          → extends Fri-Sun window to next Tuesday
_snipi_rows_per_page         → pagination page size
_snipi_autoplay_interval     → seconds per page in rotation
_snipi_show_program_column   → toggle program column
_snipi_logo_id               → header logo attachment ID
_snipi_display_bottom        → toggle bottom info row
_snipi_bottom_row            → WYSIWYG content for bottom row
_snipi_styling_data          → JSON blob for styling GUI (header/table/footer)
_snipi_custom_css            → raw custom CSS override
```

## Coding standard — mandatory (WordPress Holy Trinity)

- **Validate** all input
- **Sanitize** all input (sanitize_text_field, absint, intval, etc.)
- **Escape** all output (esc_html, esc_attr, esc_url, wp_kses_post)
- **Every user-facing string translatable** — wrapped in `__()` / `_e()` with `snipi-ekrani` text domain, even though current strings are hardcoded Slovenian in some places (existing technical debt, don't fix unless asked)
- Timezone-aware: always `Europe/Ljubljana` for date/time logic, never server default
- Follow existing class structure: one class per file, `class-name-with-dashes.php` naming

## Working conventions

- Show a plan before writing any code
- Show diff before editing files
- One git branch per task: fix/description or feature/description
- Commit after every meaningful change
- One-line commit message summarising what changed, suggested after every edit
- Update CHANGELOG.md at end of every session (follow existing format — see CHANGELOG.md for the pattern: version heading, date, categorized bullet points)
- Do not add co-author attribution to commit messages
- Never push to production without explicit instruction

## Deployment to dev.upi.si

The plugin directory on the server is a git repo tracking `origin/dev`:
- Server path: `/var/www/dev.upi.si/wp-content/plugins/SNIPI-Ekrani`
- Remote: `https://github.com/Squarebow/SNIPI-Ekrani.git`
- Always deploys the `dev` branch — never `main`
- Deployment is **always explicit** — never automatic on push

**To deploy:** the developer says `"deploy to dev"`. Run `git pull origin dev` on the server via `novamira/execute-php` (Novamira MCP, server `novamira-dev-upi-si`):

```php
$plugin_dir = '/var/www/dev.upi.si/wp-content/plugins/SNIPI-Ekrani';
$out = []; $rc = 0;
exec('git -C ' . escapeshellarg($plugin_dir) . ' pull origin dev 2>&1', $out, $rc);
return ['rc' => $rc, 'output' => $out];
```

Report what was pulled or confirm "already up to date".

## Current focus

Planning a new "Promo Slide" feature — a static 3-column content block (image + heading + text per column) that displays:
1. As an extra slide after the last pagination page when events are running
2. As the sole display when no events remain for the day, replacing the table entirely until next event day

This requires new logic in front.js's pagination/autoplay engine plus new Meta Box fields for the 3 columns of content. Not yet implemented — architecture review of front.js pagination logic still needed before building.

## Test URLs
- Stable/production reference: https://dev.upi.si/ekran (do not modify this post's settings during testing)
- Development/testing: https://dev.upi.si/ekran-promo (safe to test new features here)

## Known constraints

- TV display is non-interactive — no click/tap interactions possible, everything must be either automated or static admin-configured content
- Must work reliably unattended for long periods (it's a wall-mounted display, not actively monitored)
- Multiple screens can exist on the same site simultaneously — always test that changes don't leak between screens (check CSS scoping, check REST calls use correct post_id)