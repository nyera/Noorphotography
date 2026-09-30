# Demo Import

## Why this isn't the "One Click Demo Import" plugin

The theme ships its own lightweight importer instead of requiring the
**One Click Demo Import** plugin, for one reason: the plugin's standard
workflow imports a WXR content file plus a widgets/customizer export — but
it has no mechanism for regenerating custom-field-driven content in a way
that stays reviewable/editable line by line, and more importantly it
provides no place to enforce the image-licensing policy described in
`06-licensing.md` (skip missing images gracefully instead of importing
placeholder photos of unclear origin). The theme's own importer does both.
If you prefer the OCDI workflow, an `install_plugins_tab_ocdi` extension is
a template repo you can adapt — the demo importer's source is in
`inc/demo-content.php` if you need a WXR file to align with it.

## What gets imported

Go to **إعدادات نُور**, scroll to the **"محتوى تجريبي"** section at the
bottom, and click **"استيراد المحتوى التجريبي"**. This creates:

- 6 Portfolio projects across all 6 categories (Weddings, Portrait,
  Fashion, Commercial, Products, Events), each with client/location/year
  metadata and a 3–4 image gallery.
- 6 Services with pricing, features, "what's included" lists, and FAQ.
- 3 Testimonials.
- 3 Pricing Packages (Basic / Featured / Studio) with a full comparison
  table.
- The Home, About, Pricing, Contact, and Booking pages, each with their
  page-specific content fields filled in.
- The primary navigation menu, assigned to the header/mobile menu location.
- All Theme Settings fields (brand info, contact info, social links, stats,
  photographer bio, Instagram handle).

## Images

**No demo photographs are bundled with this theme** (see
`06-licensing.md`). If the theme's `/assets/images/` folder is empty (the
normal state of a purchased/downloaded package), the importer runs exactly
as above but skips every featured-image/gallery assignment — every post is
created with the right text content, structure, and metadata, just no
photo yet. An admin notice on the same screen tells you this before you
click Import.

After importing, open each Portfolio project / Service / Testimonial /
the Home page and set its own Featured Image (and, for Portfolio, its
gallery field) from your own licensed photography via the normal Media
Library uploader.

## Troubleshooting

- **"استيراد المحتوى التجريبي" does nothing / redirects with no visible
  change** — check your browser console and PHP error log; the importer
  runs entirely server-side with a nonce check, so a stale page (cached
  admin screen) is the most common cause. Reload إعدادات نُور and retry.
- **Running it twice creates duplicate content** — it doesn't: every post
  type lookup uses `get_page_by_title()` before inserting, so re-running
  updates the existing demo posts in place rather than duplicating them.
- **Menu didn't get assigned** — only happens automatically the *first*
  time (if the "القائمة الرئيسية" menu doesn't exist yet with items in it).
  Assign it manually under Appearance → Menus if needed.
