# Portfolio, Services, Testimonials, Packages

## Portfolio

Go to **الأعمال** (Portfolio) in the admin sidebar → **إضافة مشروع** (Add
New).

- **Title** — project name.
- **Content editor** — the full project description (shown on the single
  project page under "عن المشروع").
- **Excerpt** — short one-line summary (shown on portfolio cards and the
  archive). If you don't see the Excerpt box, enable it via **Screen
  Options** at the top of the edit screen.
- **Featured Image** — the project's cover photo (used on cards, the
  archive, and the single-project hero).
- **تفاصيل المشروع** meta box:
  - العميل (Client)
  - الموقع (Location)
  - السنة (Year)
  - نسبة العرض الافتراضية للبطاقة (card aspect ratio: landscape / portrait /
    square — matches how the card crops on archives)
  - معرض صور المشروع (gallery — click "اختيار الصور" to multi-select images
    from the Media Library; these render as the full gallery + fullscreen
    lightbox on the single project page)
- **Portfolio Category** (right sidebar) — assign one or more categories.
  Manage the category list itself under **الأعمال → التصنيفات**.

Publishing, unpublishing (Draft), and deleting all work exactly like any
other WordPress post type.

### Featured projects on the homepage

The homepage shows the 9 most recently published Portfolio posts — there's
no separate "featured" flag; publish date controls what appears.

## Services

Go to **الخدمات** (Services) → **إضافة خدمة**.

- **Title**, **Content editor** (full description), **Excerpt** (short
  description shown on cards/homepage), **Featured Image**.
- **تفاصيل الخدمة** meta box:
  - السعر (free-text price string, e.g. "تبدأ من ١٢٬٠٠٠ ر.س")
  - المزايا (one feature per line)
  - ما الذي يشمله (one "included" item per line)
  - الأسئلة الشائعة (one FAQ per line, format: `السؤال :: الجواب`)
- **الترتيب** (Page Attributes → Order, bottom-right) controls the display
  order everywhere services are listed (homepage, archive, footer).

## Testimonials

Go to **آراء العملاء** → **إضافة رأي**.

- **Title** = client name.
- **Content editor** = the quote itself.
- **Featured Image** = client photo.
- **تفاصيل رأي العميل** meta box: الدور / المناسبة (role/context line) and
  التقييم (1–5 star rating).

The homepage shows the 3 most recent testimonials; the About page shows up
to 3 as well.

## Pricing Packages

Go to **باقات الأسعار** → **إضافة باقة**. Only used on the page assigned
the "نُور — الباقات والأسعار" page template.

- **Title** = package name.
- **تفاصيل الباقة** meta box:
  - السعر (numeric price, no currency symbol — "ر.س" is appended
    automatically)
  - ملاحظة قصيرة أعلى الاسم (e.g. "الأكثر طلبًا")
  - إبراز هذه الباقة (checkbox — gives the card the dark "highlighted"
    treatment)
  - المزايا (one per line — shown on the package card)
  - صفوف جدول المقارنة (one per line, format: `التسمية :: القيمة`, e.g.
    `ساعات التغطية :: ١٠ ساعات`) — the comparison table on the Pricing page
    is built dynamically: every unique label across all your packages
    becomes a row, and each package's matching value fills that row (a
    package missing a given label just shows "—" for that row).
- **الترتيب** controls left-to-right card order.
