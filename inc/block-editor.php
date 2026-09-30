<?php
/**
 * Block editor (Gutenberg) support.
 *
 * front-page.php stays hand-coded PHP — it's the pixel-accurate match of
 * the reference design and rebuilding it as blocks would only add fragility
 * for no visual gain. What this file adds is genuine, additive flexibility
 * for every OTHER page: editor-preview styling so authors see something
 * close to the real front end while writing, a matching color/font palette,
 * and a handful of block patterns (quote/manifesto, stats band, final CTA)
 * so a page built with the block editor can reuse the theme's visual
 * language without touching PHP or hardcoding a homepage layout in blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_block_editor_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'الأساسي (حبر)', 'noor-photography' ),
				'slug'  => 'ink',
				'color' => '#110f0d',
			),
			array(
				'name'  => __( 'الخلفية', 'noor-photography' ),
				'slug'  => 'background',
				'color' => '#faf7f3',
			),
			array(
				'name'  => __( 'السطح', 'noor-photography' ),
				'slug'  => 'surface',
				'color' => '#f2f0ea',
			),
			array(
				'name'  => __( 'التمييز (ذهبي رملي)', 'noor-photography' ),
				'slug'  => 'accent',
				'color' => '#ab947b',
			),
			array(
				'name'  => __( 'رمادي هادئ', 'noor-photography' ),
				'slug'  => 'muted',
				'color' => '#6e6862',
			),
			array(
				'name'  => __( 'أبيض', 'noor-photography' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
		)
	);

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => __( 'عادي', 'noor-photography' ),
				'slug' => 'normal',
				'size' => 16,
			),
			array(
				'name' => __( 'كبير', 'noor-photography' ),
				'slug' => 'large',
				'size' => 20,
			),
			array(
				'name' => __( 'عنوان فرعي', 'noor-photography' ),
				'slug' => 'h3',
				'size' => 28,
			),
			array(
				'name' => __( 'عنوان', 'noor-photography' ),
				'slug' => 'h2',
				'size' => 40,
			),
			array(
				'name' => __( 'عنوان كبير', 'noor-photography' ),
				'slug' => 'display',
				'size' => 64,
			),
		)
	);

	add_theme_support( 'custom-line-height' );
	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'noor_block_editor_setup' );

/**
 * The block editor iframe doesn't inherit the frontend's <html dir="rtl">
 * by itself in every WP version — force it so authors write in the same
 * direction visitors will read.
 */
function noor_block_editor_settings( $settings ) {
	$settings['direction'] = 'rtl';
	return $settings;
}
add_filter( 'block_editor_settings_all', 'noor_block_editor_settings' );

/* -------- block patterns -------- */

function noor_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category( 'noor', array( 'label' => __( 'نُور', 'noor-photography' ) ) );

	register_block_pattern(
		'noor/manifesto',
		array(
			'title'       => __( 'نُور — فقرة فلسفة (عنوان جانبي + نص)', 'noor-photography' ),
			'description' => __( 'تخطيط عمودين: عنوان جانبي قصير مقابل فقرة كبيرة، بنفس أسلوب قسم الفلسفة في الصفحة الرئيسية.', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"className":"hairline"} -->
<div class="wp-block-group alignfull hairline" style="padding-top:6rem;padding-bottom:6rem"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph {"style":{"typography":{"letterSpacing":"0.32em","textTransform":"uppercase","fontSize":"13px"}}} -->
<p style="letter-spacing:0.32em;text-transform:uppercase;font-size:13px">' . esc_html__( 'فلسفتنا', 'noor-photography' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:paragraph {"fontSize":"h3"} -->
<p class="has-h3-font-size">' . esc_html__( 'اكتب هنا فقرة قصيرة تعبّر عن فلسفة عملك أو رؤيتك.', 'noor-photography' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'noor/stats-band',
		array(
			'title'       => __( 'نُور — شريط إحصائيات داكن', 'noor-photography' ),
			'description' => __( 'أربع إحصائيات على خلفية داكنة، بنفس أسلوب قسم الأرقام في الصفحة الرئيسية.', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:group {"align":"full","backgroundColor":"ink","textColor":"white","style":{"spacing":{"padding":{"top":"64px","bottom":"64px"}}}} -->
<div class="wp-block-group alignfull has-white-color has-ink-background-color has-text-color has-background" style="padding-top:64px;padding-bottom:64px"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"center","fontSize":"h2"} -->
<p class="has-text-align-center has-h2-font-size">480+</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">' . esc_html__( 'مشروع مُنجز', 'noor-photography' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"center","fontSize":"h2"} -->
<p class="has-text-align-center has-h2-font-size">12</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">' . esc_html__( 'سنة خبرة', 'noor-photography' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'noor/final-cta',
		array(
			'title'       => __( 'نُور — دعوة ختامية على صورة', 'noor-photography' ),
			'description' => __( 'عنوان كبير وزرّان فوق صورة غلاف داكنة، بنفس أسلوب قسم الدعوة الختامية في الصفحة الرئيسية.', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:cover {"overlayColor":"ink","dimRatio":70,"align":"full","style":{"spacing":{"padding":{"top":"112px","bottom":"160px"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:112px;padding-bottom:160px"><span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-70 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">' . esc_html__( 'لنبدأ', 'noor-photography' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","textColor":"white","fontSize":"display"} -->
<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color has-display-font-size">' . esc_html__( 'اكتب هنا عنوانًا يدعو الزائر لاتخاذ إجراء.', 'noor-photography' ) . '</h2>
<!-- /wp:heading -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"ink"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-ink-color has-white-background-color has-text-color has-background wp-element-button">' . esc_html__( 'احجز الآن', 'noor-photography' ) . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
		)
	);

	register_block_pattern(
		'noor/hero',
		array(
			'title'       => __( 'نُور — غلاف رئيسي (صورة كاملة + عنوان)', 'noor-photography' ),
			'description' => __( 'صورة غلاف بملء الشاشة تقريبًا مع عنوان كبير وزرّي إجراء، بنفس أسلوب غلاف الصفحة الرئيسية.', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:cover {"overlayColor":"ink","dimRatio":50,"minHeight":80,"minHeightUnit":"vh","align":"full","style":{"spacing":{"padding":{"top":"140px","bottom":"96px"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:140px;padding-bottom:96px;min-height:80vh"><span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-50 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"textColor":"white"} -->
<p class="has-white-color has-text-color">' . esc_html__( 'استوديو نُور · الرياض', 'noor-photography' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textColor":"white","fontSize":"display"} -->
<h2 class="wp-block-heading has-white-color has-text-color has-display-font-size">' . esc_html__( 'اكتب هنا عنوانًا كبيرًا يجذب الزائر منذ اللحظة الأولى.', 'noor-photography' ) . '</h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"ink"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-ink-color has-white-background-color has-text-color has-background wp-element-button">' . esc_html__( 'شاهد الأعمال', 'noor-photography' ) . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
		)
	);

	register_block_pattern(
		'noor/portfolio-grid',
		array(
			'title'       => __( 'نُور — شبكة أعمال (Query Loop)', 'noor-photography' ),
			'description' => __( 'شبكة ديناميكية تعرض أحدث مشاريع الأعمال مباشرة من نوع المحتوى "الأعمال" — تتحدث تلقائيًا مع كل مشروع جديد.', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:query {"query":{"perPage":6,"postType":"portfolio","order":"desc","orderBy":"date","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/5"} /-->
<!-- wp:post-title {"isLink":true,"fontSize":"h3"} /-->
<!-- wp:post-excerpt /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->',
		)
	);

	register_block_pattern(
		'noor/services-list',
		array(
			'title'       => __( 'نُور — قائمة خدمات (Query Loop)', 'noor-photography' ),
			'description' => __( 'قائمة ديناميكية تعرض الخدمات المنشورة مباشرة من نوع المحتوى "الخدمات".', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:query {"query":{"perPage":6,"postType":"service","order":"asc","orderBy":"menu_order","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:post-title {"isLink":true,"fontSize":"h3"} /-->
<!-- wp:post-excerpt /--></div>
<!-- /wp:group -->
<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->',
		)
	);

	register_block_pattern(
		'noor/testimonials',
		array(
			'title'       => __( 'نُور — شبكة آراء عملاء (Query Loop)', 'noor-photography' ),
			'description' => __( 'ثلاثة أعمدة تعرض آراء العملاء المنشورة مباشرة من نوع المحتوى "آراء العملاء".', 'noor-photography' ),
			'categories'  => array( 'noor-photography' ),
			'content'     => '<!-- wp:query {"query":{"perPage":3,"postType":"testimonial","order":"desc","orderBy":"date","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-excerpt {"fontSize":"h3"} /-->
<!-- wp:group {"layout":{"type":"flex"}} -->
<div class="wp-block-group"><!-- wp:post-featured-image {"width":"48px","height":"48px"} /-->
<!-- wp:post-title {"isLink":true,"fontSize":"normal"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->',
		)
	);
}
add_action( 'init', 'noor_register_block_patterns' );

/**
 * A handful of core-block patterns don't add much value in a theme that's
 * mostly PHP-templated; keep the pattern directory (blocks.wordpress.org)
 * off by default so the Patterns tab isn't cluttered with unrelated content
 * — remove this filter if that remote library is wanted.
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );
