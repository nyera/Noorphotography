# Image, Font & Asset Licensing Audit

This document exists because a commercial WordPress theme must not bundle
assets whose redistribution rights are unclear. Full findings:

## Demo photography — EXCLUDED from the package

The original design reference (noor-portfolio-craft) was generated with
Lovable, an AI prototyping tool, from a text specification. The repository
contains no LICENSE file and no attribution/sourcing information for the
demo photographs (`hero-wedding.jpg`, `portrait-1.jpg`, `fashion-1.jpg`,
`product-1.jpg`, `event-1.jpg`, `commercial-1.jpg`, `photographer.jpg`,
`detail-1.jpg`). There is no way to verify these images carry a license
permitting commercial redistribution as part of a paid product.

**Decision:** these images are not included in this theme's distributable
package (excluded via `.gitignore`, never committed). The theme's demo
importer (`inc/demo-content.php`) is written to degrade gracefully when
they're absent — every demo post is still created with full text content
and metadata, just without a featured image, and an admin notice on the
Theme Settings screen makes this explicit before import.

**Action required before commercial use:** source your own demo photography
— either your own photography portfolio (ideal for a photography theme), or
stock images under a license that explicitly covers your use case (a theme
demo screenshot / a live client site / marketplace preview images each have
different licensing requirements — read the stock provider's terms
carefully; "personal use" or unmodified "editorial" licenses are
insufficient).

## Fonts — cleared for commercial use

| Font | License | Bundled? |
|---|---|---|
| Amiri | SIL Open Font License 1.1 | No — loaded from Google Fonts at runtime |
| IBM Plex Sans Arabic | SIL Open Font License 1.1 | No — loaded from Google Fonts at runtime |

Both licenses explicitly permit commercial use, embedding, and
redistribution. Loading from Google Fonts avoids bundling font binary files
in the package entirely; if you need the theme to work with Google Fonts
blocked (e.g. some enterprise/government hosting), self-host both families
under their existing OFL license — no new licensing action needed.

## Icons / SVGs / illustrations

None are used. All iconography in the design (the diamond logo mark, the
"+"/rotate accordion indicator, the small rotated-square bullet dots) is
drawn with plain CSS borders and transforms — zero icon font or SVG library
dependency, zero licensing surface.

## JavaScript libraries

None are bundled. `assets/js/main.js` and `assets/js/lightbox.js` are
original vanilla JavaScript written specifically for this theme — no
jQuery, no third-party lightbox/slider library, no licensing obligation.

## Third-party plugins (WooCommerce, Yoast, Elementor, etc.)

Not bundled, not required. The theme has compatibility hooks that only
activate if you separately install and license these plugins yourself
under their own terms (see `inc/compat.php`).
