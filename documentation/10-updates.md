# Updates

This theme has no auto-update mechanism (that requires either a
marketplace-specific updater like Envato Market, or a licensing/update
server, neither of which exists yet for this product).

## Updating safely

1. **Back up first** — your database (all your Portfolio/Service/
   Testimonial/Package content lives there, not in theme files) and your
   `/wp-content/uploads/` folder (your media).
2. **Never edit theme files directly** if you want to receive updates
   without losing your changes. If you need to customize PHP/CSS beyond
   what Theme Settings and page-specific fields expose, do it in a child
   theme (create `/wp-content/themes/noor-photography-child/` with its own
   `style.css` declaring `Template: noor-photography`, and a `functions.php`
   that enqueues the parent stylesheet before your own overrides).
3. Deactivate the old theme version, delete its folder, upload the new
   version, reactivate.
4. Because all content lives in the database (posts, options, menus), a
   clean theme-file swap does not touch your content, images, or settings.

## Checking what changed

Read `changelog.txt` before updating — it lists every user-facing and
structural change per version.
