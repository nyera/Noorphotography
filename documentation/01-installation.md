# Installation

## Requirements

- WordPress 6.0 or later (theme uses `wp_body_open()`, `theme_page_templates`
  filter, `should_load_remote_block_patterns` — all available since 5.2–6.0;
  6.0+ recommended for the full block-pattern experience).
- PHP 7.4 or later (theme uses arrow-free closures, `??`/`?:`, `array_filter`
  callbacks — nothing beyond 7.4 syntax).
- MySQL 5.7+ / MariaDB 10.3+ (standard WordPress requirement).
- HTTPS recommended (not required) for the Google Fonts preconnect and any
  contact-form mail sending.

## Recommended hosting environment

- PHP `memory_limit` of at least 128M (256M if you plan large portfolio
  galleries with many high-resolution images).
- An outbound-mail-capable host, or an SMTP plugin (WP Mail SMTP, FluentSMTP)
  installed — the Contact and Booking forms call `wp_mail()` for the email
  notification. **Submissions are also always saved as records in wp-admin**
  (Booking Requests / Contact Messages), so a submission is never lost even
  if your host's default mail delivery is unreliable — but you'll want real
  SMTP for the email side to actually work.

## Installing the theme

1. In wp-admin, go to **Appearance → Themes → Add New → Upload Theme**.
2. Choose the `noor-photography.zip` file and click **Install Now**.
3. Click **Activate**.

(Or, for manual/FTP installs: upload the extracted `noor-photography` folder
into `/wp-content/themes/`, then activate it from Appearance → Themes.)

## After activation

1. Go to **Settings → Permalinks** and click **Save Changes** once — this
   flushes rewrite rules so the `/portfolio/` and `/services/` archives
   resolve correctly.
2. Go to **إعدادات نُور** (a dedicated top-level menu item, gear icon, near
   the top of the admin sidebar) and fill in your brand details.
3. Continue to `02-initial-setup.md`.
