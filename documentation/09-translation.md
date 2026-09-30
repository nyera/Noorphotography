# Translation

The theme's own UI copy is written in Arabic as the source language (this
is an Arabic-first product, matching the design reference), and every
user-facing string is wrapped in WordPress's localization functions
(`__()`, `_e()`, `esc_html__()`, etc.) under the consistent text domain
`noor-photography`.

## Translating to another language

1. `languages/noor-photography.pot` is the translation template — it
   contains every translatable string in the theme (404 entries as of
   1.0.0), each with a comment pointing at the file/line it came from.
2. Open it with a PO editor (Poedit, Loco Translate inside wp-admin, or
   GlotPress) and translate into your target language.
3. Save as `noor-photography-{locale}.po` / `.mo` — e.g.
   `noor-photography-en_US.po` for English — inside the theme's
   `/languages/` folder, or via Loco Translate's own storage location.
4. Set the site language under **Settings → General → Site Language** (or
   per-user under Users → your profile, if using a multilingual plugin).

## Regenerating the .pot file after customizing the theme

If you add/edit PHP files with new translatable strings, regenerate the POT
file. If you have WP-CLI available:

```bash
wp i18n make-pot . languages/noor-photography.pot --domain=noor-photography
```

(This environment didn't have WP-CLI available during development, so
`languages/noor-photography.pot` was generated with a small static-analysis
script instead — functionally equivalent output, same standard `.pot`
format.)

## RTL note

This theme forces `dir="rtl"` at the `<html>` level regardless of the
site's WordPress language setting (see `noor_force_rtl_language_attributes()`
in `inc/setup.php`) — because 100% of the design and copy is Arabic RTL,
with no LTR layout variant. If you translate the theme into an LTR
language, you'll need to remove or adapt that filter, since the layout
itself (logo position, button order, form field alignment, etc.) is built
assuming right-to-left reading order.

## WPML / Polylang

Not tested in this release. The custom post types (Portfolio, Service,
Testimonial, Package) and custom fields use standard `register_post_type()`
and `update_post_meta()` calls with no non-standard storage, so both
plugins' standard "translate this post type" workflow should apply
normally — but this has not been verified end-to-end and should be QA'd
before advertising multilingual support.
