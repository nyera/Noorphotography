# Troubleshooting

## Portfolio/Service archive pages 404

Go to **Settings → Permalinks** and click **Save Changes** — this
regenerates rewrite rules for the `portfolio` and `service` post types.
Needed once after theme activation, and again any time you change the
Permalinks structure.

## Contact/Booking form always shows an error

The forms are validated server-side, so a generic error can come from more
than one cause:
- **Rate limit** — resubmitting within 20 seconds of a previous submission
  from the same IP is intentionally blocked (spam protection). Wait and
  retry.
- **Nonce expired** — if the page was left open for a long time before
  submitting (WordPress nonces expire after ~24 hours by default), reload
  the page and try again.
- **Validation** — check the specific required fields (name, valid email,
  for Booking also phone/date/an actual Service selected).

## Form submissions aren't being emailed

Submissions are always saved as records under **طلبات الحجز** (Booking
Requests) or **رسائل التواصل** (Contact Messages) in wp-admin regardless of
email delivery — check there first; nothing is lost even if email fails.
For the email side specifically: most hosts' default PHP `mail()` function
(what `wp_mail()` falls back to) is unreliable or blocked outright. Install
an SMTP plugin (WP Mail SMTP, FluentSMTP, Post SMTP) and connect it to a
real mail provider (your host's SMTP, SendGrid, Mailgun, Google
Workspace, etc.).

## The site renders left-to-right instead of RTL

This shouldn't happen — the theme forces `dir="rtl"` at the theme level
regardless of the WordPress site language setting
(`noor_force_rtl_language_attributes()` in `inc/setup.php`). If you see LTR
rendering, check whether a caching plugin/CDN is serving a stale cached
page from before the theme was activated; purge all caches.

## Horizontal scrollbar / content cut off on mobile

Known Chromium behavior that this theme already works around: `position:
fixed` elements can mis-size against the document's scrollable width
instead of the true viewport when `dir="rtl"` and any horizontal overflow
coexist. The theme pins its fixed panels (header, mobile menu, search
overlay, cookie notice) to `100vw` explicitly to prevent this
(`assets/css/main.css`). If you add your **own** `position: fixed` element
via a child theme or plugin, apply the same `width: 100vw; max-width:
100%;` rule to avoid reintroducing the bug.

## Demo import doesn't add photos

By design — see `06-licensing.md` and `03-demo-import.md`. Add your own
Featured Images after import.

## A meta box / theme setting doesn't seem to do anything

Every option in this theme is wired to real output — there are no
decorative/fake settings. If something genuinely isn't reflected on the
front end, search the relevant `inc/*.php` file for the field's meta key or
option key to confirm where it's consumed, and check the browser console /
your host's PHP error log for a fatal error that might be interrupting page
rendering before that section outputs.

## White screen / fatal error after activating the theme

Check your host's PHP error log (or enable `WP_DEBUG_LOG` in
`wp-config.php`) — the most common causes are a PHP version below 7.4, or a
conflicting plugin also registering a function/class name that collides
with one of the theme's `noor_*`-prefixed functions (all theme functions
are consistently prefixed specifically to avoid this).
