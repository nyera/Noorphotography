# Customization: Gutenberg, Elementor, WooCommerce

## Gutenberg / Block Editor

Fully supported for every page **except** the homepage, which is one
hand-coded PHP template deliberately kept out of the block editor (see
`04-homepage.md` for why).

On any other page:
- The editor canvas loads in RTL and uses a color/font-size palette matched
  to the theme's design tokens (`inc/block-editor.php`,
  `assets/css/editor-style.css`), so what you see while writing is close to
  the live result.
- `add_theme_support( 'align-wide' )` is enabled — wide/full-width block
  alignment works.
- Seven **block patterns** are registered under a "نُور" category in the
  block inserter, each reusing a section from the homepage design so you
  can build similarly-styled sections on other pages with core blocks only:
  - **نُور — غلاف رئيسي** — full-bleed cover image, large heading, CTA button.
  - **نُور — فقرة فلسفة** — the two-column eyebrow + large quote layout.
  - **نُور — شريط إحصائيات داكن** — the dark stat-counter band.
  - **نُور — شبكة أعمال** — a live Query Loop block pulling from the
    Portfolio post type (genuinely dynamic — new projects appear
    automatically, nothing hardcoded).
  - **نُور — قائمة خدمات** — a live Query Loop pulling from Services.
  - **نُور — شبكة آراء عملاء** — a live Query Loop pulling from Testimonials.
  - **نُور — دعوة ختامية على صورة** — the full-bleed cover image + CTA
    buttons banner.

These are starting points, not locked components — edit the text, swap the
image, add/remove columns, exactly like any other block content.

## Elementor (optional)

Elementor is **not required**; the theme works completely without it. If
you install it:

- Build any regular Page with Elementor as normal.
- The theme detects Elementor-built pages (`noor_is_elementor_page()` in
  `inc/compat.php`) and skips its own page-header/breadcrumb block and the
  `.shell` max-width wrapper on those pages, so Elementor's own
  container/width settings apply exactly as it expects — no fighting for
  control of the layout.
- The fixed header still overlays every page (by design, matching the
  reference site); a small top-padding rule (`.noor-elementor-page #main`)
  keeps Elementor content from rendering underneath it.
- Elementor Pro's Theme Builder (custom header/footer) is untested but
  should work like on any standard theme, since this theme doesn't hook
  into `wp_head`/`wp_footer` in a way that would conflict.

## WooCommerce (optional)

This is a photography **portfolio** theme, not a shop theme, and
WooCommerce is not required or bundled. If you activate it anyway (e.g. to
sell prints, presets, or gift cards):

- `add_theme_support( 'woocommerce' )` and gallery-zoom/lightbox/slider
  support are declared, so WooCommerce's own templates render without
  triggers the "theme doesn't declare support" admin notice.
- Default WooCommerce templates (shop, single product, cart, checkout) are
  wrapped in the theme's `.shell` container and inherit its typography, so
  they don't look visually orphaned from the rest of the site — but no
  custom shop templates are provided. For a serious shop, expect to invest
  separate design/dev time; this theme does not attempt to be a WooCommerce
  theme.

## SEO plugins (Yoast SEO / Rank Math)

The theme outputs its own fallback meta description, canonical URL, Open
Graph tags, and basic JSON-LD (`inc/seo.php`) **only when neither Yoast SEO
nor Rank Math is active** (checked via `noor_seo_plugin_active()`). Install
either plugin and its output takes over completely — nothing needs
disabling on the theme side, and no tags are ever duplicated.
