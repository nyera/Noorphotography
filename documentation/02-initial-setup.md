# Initial Setup

## Site identity

Go to **Appearance → Customize → Site Identity** to set:
- Site Title / Tagline (used as a fallback anywhere برand name isn't set
  in إعدادات نُور).
- Logo (shown in the header and footer; falls back to a text wordmark —
  "نُور NOOR" plus a small diamond mark — if no logo is uploaded).
- Site Icon (the browser tab favicon).

## Menus

Go to **Appearance → Menus**.

1. Create a menu (or use the one created automatically by the demo
   importer, named "القائمة الرئيسية").
2. Add the pages/links you want in the main navigation.
3. Under **Menu Settings**, assign it to the **القائمة الرئيسية** location
   (registered as `primary`). This menu renders in both the desktop header
   navigation and the fullscreen mobile menu.
4. Optionally create a second menu and assign it to the **قائمة الفوتر**
   location (`footer`) for links like Privacy Policy / Terms.

## Homepage

The theme's homepage layout (`front-page.php`) only activates when
**Settings → Reading → "Your homepage displays"** is set to **A static
page**, with that page selected as the Homepage. The demo importer creates
and assigns a page titled "الرئيسية" automatically.

If you're not using the demo importer:
1. Create a new Page.
2. Give it a title (this becomes the large hero heading).
3. Write a short paragraph in the page content (this becomes the hero
   description under the heading).
4. Set a Featured Image (this becomes the hero background photo).
5. Fill in the **"محتوى الصفحة الرئيسية"** meta box that appears on this
   page's edit screen (only shown once the page is set as your site's front
   page) — see `04-homepage.md` for what each field controls.
6. Go to Settings → Reading and select this page as your static homepage.

## Permalinks

Go to **Settings → Permalinks**. Any structure works, but "Post name" is
recommended to match the design's clean URLs (e.g. `/portfolio/layl-noor/`).
Click **Save Changes** once after activating the theme, even if you don't
change anything — this registers the Portfolio/Service archive rewrite
rules.
