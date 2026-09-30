# Homepage

The homepage (`front-page.php`) only renders when the page you designate in
**Settings → Reading** is your static front page (see `02-initial-setup.md`).
Every section below is optional in the sense that it simply doesn't render
if its content source is empty — you never end up with an empty box or a
placeholder string on the live site.

Open your homepage's edit screen and look for the **"محتوى الصفحة
الرئيسية"** meta box (only visible on the page currently set as your front
page):

| Section | Source |
|---|---|
| Hero eyebrow (small line above the headline) | `_noor_hero_eyebrow` field |
| Hero headline | The page's **Title** |
| Hero description paragraph | The page's **content editor** |
| Hero background photo | The page's **Featured Image** |
| Hero bottom-right caption | `_noor_hero_caption` field |
| Manifesto / philosophy quote | `_noor_manifesto` field |
| Featured Portfolio grid + category filter | Latest 9 published **Portfolio** posts + **Portfolio Category** terms — see `05-portfolio-services-testimonials.md` |
| About preview (photographer photo/bio/stats) | إعدادات نُور → "المصوّر / عن الاستوديو" section |
| Services list | Latest 6 published **Service** posts, ordered by their Page Attributes → Order |
| Stats band (480+ مشروع, etc.) | إعدادات نُور → "الإحصائيات" field |
| Testimonials | Latest 3 published **Testimonial** posts |
| Instagram grid | إعدادات نُور → "إنستغرام" section |
| Final CTA banner | `_noor_cta_heading` + `_noor_cta_image` fields |

Each section is individually conditional — e.g. if you haven't added any
Testimonial posts yet, that section simply doesn't output, no gap or
placeholder text appears.

## Rearranging sections

The homepage is intentionally implemented as one hand-coded PHP template
(`front-page.php`) rather than as Gutenberg blocks — it's a pixel-accurate
match of the original design reference, and every section already queries
live WordPress data, so there is no hardcoded content to "unlock" by
rebuilding it as blocks. If you want to reorder or remove entire sections,
that requires editing `front-page.php` directly (each section is a clearly
commented `<section>` block).

For pages *other than* the homepage, the block editor is fully supported —
see `08-customization.md` for the included block patterns that reuse the
same visual language (manifesto quote layout, stats band, final CTA banner)
so you can assemble similar sections on any other page without touching
PHP.
