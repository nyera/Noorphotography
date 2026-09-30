# Theme Settings (إعدادات نُور)

A single top-level admin screen (Settings API — no third-party
options-framework dependency) holding everything reused across more than
one template, so it doesn't need duplicating per-page.

| Section | Fields | Used on |
|---|---|---|
| الهوية والعلامة | اسم العلامة (عربي/إنجليزي), الشعار الفرعي, وصف مختصر | Header logo fallback, footer description |
| التواصل | البريد الإلكتروني, رقم الهاتف, العنوان, ساعات العمل, روابط التواصل الاجتماعي | Footer, Contact page, form notification recipient |
| الإحصائيات | 4 lines: `القيمة :: اللاحقة :: التسمية` | Homepage + About page stats band |
| المصوّر / عن الاستوديو | الاسم, نبذة, سنوات الخبرة, عدد الجوائز, صورة | Homepage "about preview", About page |
| إنستغرام | المعرّف, شبكة الصور (image picker) | Homepage Instagram grid |
| التخطيط | نمط الترويسة على الصفحة الرئيسية (شفافة فوق الغلاف / صلبة دائمًا) | Front page header |
| الفوتر | نص حقوق النشر (supports `%year%`), نص دعوة الاشتراك بالنشرة | Footer |

Every field here is read through `noor_get_option( 'key' )` in the theme
code — none of them are decorative; each one visibly changes the front end.

## Every setting maps to real output — verifying this yourself

If you want to confirm a setting actually does something (recommended
practice before relying on any theme's options screen): search
`inc/theme-options.php` for the field's `name="..."` attribute, then search
the rest of the theme for `noor_get_option( 'that_key' )` — every match is
a place it's used on the front end.

## Page-specific settings

Some content belongs to one specific page, not the whole site — those live
in a meta box on that page's own edit screen instead of here:

- The **front page** gets a "محتوى الصفحة الرئيسية" box (hero fields, see
  `04-homepage.md`).
- The page using the **"نُور — عن الاستوديو"** template gets a "محتوى صفحة
  عن الاستوديو" box (process steps, awards, clients list).
- Pages using **"نُور — الباقات والأسعار"** or **"نُور — تواصل معنا"**
  templates each get their own FAQ box.

## Colors & Typography (Appearance → Customize)

A dedicated **"نُور — التصميم"** panel in the native WordPress Customizer
(Appearance → Customize) — not a third-party options framework — controls:

- **الألوان**: اللون الأساسي (text + solid button background), لون التمييز
  (hover/accent), لون الخلفية, لون الخلفية الثانوية (alternating section
  background), لون النص الثانوي (captions/labels). Each maps directly to a
  CSS custom property already used throughout `assets/css/main.css` — there
  is no separate/hidden color system to fight.
- **الطباعة**: خط العناوين (heading font, 4 curated Arabic-safe choices),
  خط النصوص (body font, 4 choices), الحجم الأساسي لنص الموقع (small/medium/
  large).

All controls use **live preview** — changing a color or font updates the
Customizer preview instantly, no save/reload needed. Every default value
matches the original shipped design exactly, so an unconfigured install
looks identical to before this feature existed.

## Homepage section visibility

Each homepage section (Hero, Manifesto, Portfolio, About preview, Services,
Stats, Testimonials, Instagram, Final CTA) can be hidden independently from
the **"أقسام الصفحة الرئيسية"** box in the sidebar of the front page's edit
screen — content isn't deleted, just not rendered, and can be re-enabled
anytime. All sections are visible by default.

## Featured items

**Portfolio** and **Testimonial** posts each have a "مميز على الصفحة
الرئيسية" checkbox in their meta box. If one or more items are flagged
featured, the homepage shows those (instead of just the latest N) — with
zero items flagged (the default), it falls back to showing the most recent
posts, exactly as before this feature existed. Services are controlled via
their existing "الترتيب" (Page Attributes → Order) field plus normal
publish/draft status — a second, competing "featured" flag on Services
would just be a redundant, confusing second way to do the same thing.

## Global site basics (not duplicated here)

Logo, site icon/favicon, and site title/tagline are managed through the
native **Appearance → Customize → Site Identity** screen — there is no
reason to duplicate WordPress's own, already-familiar UI for these.
