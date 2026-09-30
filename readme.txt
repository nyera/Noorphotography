=== Noor Photography ===
Contributors: Noor Studio
Tags: photography, portfolio, rtl, arabic, custom-post-type, one-column, right-sidebar, translation-ready, block-patterns
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Version: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A production WordPress theme for Arabic-first (RTL) photography studios,
converted from the noor-portfolio-craft design reference. Fully dynamic:
portfolio, services, testimonials, and pricing packages are all custom post
types managed from wp-admin; no theme file ever needs to be edited to change
site content.

== Description ==

Noor Photography is a single-purpose, RTL-first WordPress theme for
photography studios (weddings, portrait, fashion, product, commercial,
events). Every piece of visible content — hero copy, portfolio projects,
services, testimonials, pricing packages, contact details, social links —
is editable from the WordPress dashboard. The theme code controls layout,
behavior, and presentation only.

Key features:

* Portfolio custom post type with a Portfolio Category taxonomy, filterable
  archive (masonry/grid/full-width layouts), and a single-project template
  with a fullscreen lightbox gallery.
* Service, Testimonial, and Package custom post types.
* Booking and Contact forms that store submissions as manageable records in
  wp-admin (Booking Requests / Contact Messages), in addition to an email
  notification — so a submission is never lost even if outbound mail fails.
* One global Theme Settings screen for brand identity, contact info, social
  links, homepage stats, photographer bio, and the Instagram grid.
* One-click demo content import.
* RTL enforced at the theme level, independent of the WordPress site
  language setting.
* Gutenberg block editor support with a matching color/typography palette
  and reusable block patterns.
* Optional Elementor compatibility (not a dependency).
* Optional WooCommerce compatibility hooks (not a dependency — this is not
  a shop theme).
* SEO fallback tags that automatically defer to Yoast SEO / Rank Math when
  either is active.
* Translation-ready (`noor-photography` text domain, languages/noor-photography.pot included).

== Installation ==

See documentation/01-installation.md for the full walkthrough. Summary:

1. Upload the theme folder to `/wp-content/themes/`, or upload the zip via
   Appearance → Themes → Add New → Upload Theme.
2. Activate the theme.
3. Go to Settings → Permalinks and click Save (refreshes rewrite rules for
   the Portfolio/Service archives).
4. Go to إعدادات نُور (top of the admin menu) and fill in your brand info.
5. Optionally click "استيراد المحتوى التجريبي" to import demo content, then
   replace the demo images with your own licensed photography.
6. Under Settings → Reading, confirm "الرئيسية" is set as your static
   front page.
7. Under Appearance → Menus, confirm "القونة الرئيسية" is assigned to the
   Primary Menu location (the demo importer does this automatically).

== Frequently Asked Questions ==

= Does this theme require ACF or a page builder? =

No. All custom fields are hand-built into the theme (no ACF dependency),
and the core design does not require Elementor or any other page builder.
Elementor support is available for building *additional* pages if you
choose to install it, but it's entirely optional.

= Where do demo photos come from? =

They don't ship in this package — see license.txt and
documentation/06-licensing.md. You must supply your own licensed
photography.

= Is the theme translation-ready? =

Yes. Every user-facing string uses WordPress's localization functions under
the `noor-photography` text domain, and languages/noor-photography.pot is
included and current.

== Changelog ==

See changelog.txt.

== Documentation ==

Full documentation is in the /documentation folder included with this
theme, covering installation, initial setup, demo import, homepage editing,
the portfolio/services/testimonials systems, theme settings, Gutenberg and
Elementor support, translation, updates, and troubleshooting.
