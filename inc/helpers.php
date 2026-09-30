<?php
/**
 * Template helpers shared across the theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Aspect ratio class per portfolio card ratio setting.
 */
function noor_ratio_css( $ratio ) {
	$map = array(
		'portrait'  => '4 / 5',
		'landscape' => '3 / 2',
		'square'    => '1 / 1',
	);
	return isset( $map[ $ratio ] ) ? $map[ $ratio ] : $map['landscape'];
}

function noor_image_size_for_ratio( $ratio ) {
	$map = array(
		'portrait'  => 'noor-card-portrait',
		'landscape' => 'noor-card-landscape',
		'square'    => 'noor-card-square',
	);
	return isset( $map[ $ratio ] ) ? $map[ $ratio ] : $map['landscape'];
}

/**
 * Arabic-Indic digit formatting to match the original design's locale formatting.
 */
function noor_ar_number( $number ) {
	$western = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$eastern = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
	return str_replace( $western, $eastern, (string) $number );
}

/**
 * Breadcrumb trail renderer, matching the design's <nav>/<ol> markup.
 */
function noor_breadcrumbs( $items ) {
	echo '<nav aria-label="' . esc_attr__( 'مسار التنقّل', 'noor-photography' ) . '" class="t-caption"><ol class="noor-breadcrumbs">';
	$count = count( $items );
	foreach ( $items as $i => $item ) {
		echo '<li>';
		if ( ! empty( $item['url'] ) ) {
			echo '<a class="link-underline" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		} else {
			echo '<span class="current">' . esc_html( $item['label'] ) . '</span>';
		}
		if ( $i < $count - 1 ) {
			echo '<span aria-hidden="true">/</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Page header block used by every interior page template.
 */
function noor_page_header( $eyebrow, $title, $lead, $breadcrumbs ) {
	?>
	<header class="noor-page-header">
		<div class="shell">
			<?php noor_breadcrumbs( $breadcrumbs ); ?>
			<p class="t-eyebrow noor-page-header__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<h1 class="t-h1 noor-page-header__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $lead ) : ?>
				<p class="t-lead noor-page-header__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Resolve the URL of the page using a given page template, cached per request.
 * Falls back to home_url( $fallback_slug ) so links never dead-end even before
 * the admin has created/assigned the page.
 */
function noor_url_for_template( $template, $fallback_slug ) {
	static $cache = array();
	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}
	$pages              = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
		)
	);
	$url                = $pages ? get_permalink( $pages[0] ) : home_url( '/' . $fallback_slug . '/' );
	$cache[ $template ] = $url;
	return $url;
}

function noor_get_booking_url() {
	return noor_url_for_template( 'template-booking.php', 'booking' );
}

function noor_get_contact_url() {
	return noor_url_for_template( 'template-contact.php', 'contact' );
}

function noor_get_pricing_url() {
	return noor_url_for_template( 'template-pricing.php', 'pricing' );
}

function noor_get_about_url() {
	return noor_url_for_template( 'template-about.php', 'about' );
}

/**
 * Homepage section visibility. Every section defaults to visible (matches
 * the theme's original, unconfigured design) unless the admin has
 * explicitly unchecked it in the "محتوى الصفحة الرئيسية" meta box — so a
 * page with no meta saved yet (or content imported before this feature
 * existed) renders exactly as before.
 */
function noor_section_visible( $post_id, $key ) {
	$value = get_post_meta( $post_id, '_noor_show_' . $key, true );
	return '0' !== $value;
}

function noor_homepage_sections() {
	return array(
		'hero'         => __( 'الغلاف الرئيسي (Hero)', 'noor-photography' ),
		'manifesto'    => __( 'فقرة الفلسفة', 'noor-photography' ),
		'portfolio'    => __( 'أعمال مختارة', 'noor-photography' ),
		'about'        => __( 'عن المصوّر', 'noor-photography' ),
		'services'     => __( 'الخدمات', 'noor-photography' ),
		'stats'        => __( 'شريط الإحصائيات', 'noor-photography' ),
		'testimonials' => __( 'آراء العملاء', 'noor-photography' ),
		'instagram'    => __( 'شبكة إنستغرام', 'noor-photography' ),
		'cta'          => __( 'الدعوة الختامية', 'noor-photography' ),
	);
}

/**
 * Homepage section display order, admin-configurable (drag-reorder in the
 * "أقسام الصفحة الرئيسية" meta box). Falls back to the default
 * noor_homepage_sections() key order when nothing is saved yet, and silently
 * drops/repairs anything that no longer matches a real section — so a stale
 * or hand-edited value can never hide a section or fatal the homepage.
 */
function noor_get_home_section_order( $post_id ) {
	$defaults = array_keys( noor_homepage_sections() );
	$saved    = $post_id ? get_post_meta( $post_id, '_noor_home_section_order', true ) : '';

	if ( ! $saved ) {
		return $defaults;
	}

	$order   = array_filter( array_map( 'sanitize_key', explode( ',', $saved ) ) );
	$order   = array_values( array_intersect( $order, $defaults ) );
	$missing = array_diff( $defaults, $order );

	return array_merge( $order, array_values( $missing ) );
}

/**
 * Featured-first content query: if the admin has flagged one or more posts
 * of this type as "مميز على الرئيسية" (_noor_featured), show those first
 * (oldest-flagged-first is confusing, so we preserve manual flag order by
 * falling back to publish date); otherwise — the default, out-of-the-box
 * state — just show the latest $count posts, exactly as before this
 * feature existed.
 */
function noor_get_featured_or_latest( $post_type, $count, $args = array() ) {
	$featured = get_posts(
		array_merge(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => $count,
				'meta_key'       => '_noor_featured',
				'meta_value'     => '1',
			),
			$args
		)
	);
	if ( $featured ) {
		return $featured;
	}
	return get_posts(
		array_merge(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => $count,
			),
			$args
		)
	);
}

/**
 * Portfolio project field accessors.
 */
function noor_get_project_fields( $post_id ) {
	$gallery_raw = get_post_meta( $post_id, '_noor_gallery', true );
	return array(
		'client'    => get_post_meta( $post_id, '_noor_client', true ),
		'location'  => get_post_meta( $post_id, '_noor_location', true ),
		'year'      => get_post_meta( $post_id, '_noor_year', true ),
		'ratio'     => get_post_meta( $post_id, '_noor_ratio', true ) ?: 'landscape',
		'gallery'   => $gallery_raw ? array_filter( array_map( 'intval', explode( ',', $gallery_raw ) ) ) : array(),
	);
}

function noor_get_service_fields( $post_id ) {
	return array(
		'price'    => get_post_meta( $post_id, '_noor_price', true ),
		'features' => noor_parse_lines( get_post_meta( $post_id, '_noor_features', true ) ),
		'includes' => noor_parse_lines( get_post_meta( $post_id, '_noor_includes', true ) ),
		'faq'      => noor_parse_pairs( get_post_meta( $post_id, '_noor_faq', true ) ),
	);
}

function noor_get_package_fields( $post_id ) {
	return array(
		'price'     => get_post_meta( $post_id, '_noor_price', true ),
		'note'      => get_post_meta( $post_id, '_noor_note', true ),
		'highlight' => (bool) get_post_meta( $post_id, '_noor_highlight', true ),
		'features'  => noor_parse_lines( get_post_meta( $post_id, '_noor_features', true ) ),
		'compare'   => noor_parse_pairs( get_post_meta( $post_id, '_noor_compare', true ) ),
	);
}

/**
 * Search index consumed by the client-side search overlay (title + url + type only —
 * no sensitive data, safe to expose via wp_localize_script).
 */
function noor_get_search_index() {
	$index = array();

	$posts = get_posts(
		array(
			'post_type'      => array( 'portfolio', 'service', 'page' ),
			'posts_per_page' => 60,
			'post_status'    => 'publish',
		)
	);

	$type_labels = array(
		'portfolio' => __( 'مشروع', 'noor-photography' ),
		'service'   => __( 'خدمة', 'noor-photography' ),
		'page'      => __( 'صفحة', 'noor-photography' ),
	);

	foreach ( $posts as $p ) {
		$index[] = array(
			'title' => get_the_title( $p ),
			'type'  => $type_labels[ $p->post_type ] ?? '',
			'url'   => get_permalink( $p ),
		);
	}

	return $index;
}

/**
 * Accordion renderer (FAQ pairs).
 */
function noor_render_accordion( $items ) {
	if ( empty( $items ) ) {
		return;
	}
	echo '<div class="noor-accordion">';
	foreach ( $items as $i => $item ) {
		list( $q, $a ) = $item;
		?>
		<div class="noor-accordion__item">
			<h3>
				<button type="button" class="noor-accordion__trigger" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
					<span><?php echo esc_html( $q ); ?></span>
					<span class="noor-accordion__icon" aria-hidden="true">+</span>
				</button>
			</h3>
			<div class="noor-accordion__panel" <?php echo 0 === $i ? '' : 'hidden'; ?>>
				<p class="t-lead"><?php echo esc_html( $a ); ?></p>
			</div>
		</div>
		<?php
	}
	echo '</div>';
}
