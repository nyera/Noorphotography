<?php
/**
 * Core theme setup: supports, menus, image sizes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_setup() {
	load_theme_textdomain( 'noor-photography', NOOR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'primary' => __( 'القائمة الرئيسية', 'noor-photography' ),
			'footer'  => __( 'قائمة الفوتر', 'noor-photography' ),
		)
	);

	add_image_size( 'noor-hero', 1920, 1200, true );
	add_image_size( 'noor-portrait', 1024, 1376, true );
	add_image_size( 'noor-card-landscape', 900, 600, true );
	add_image_size( 'noor-card-portrait', 800, 1000, true );
	add_image_size( 'noor-card-square', 800, 800, true );
	add_image_size( 'noor-thumb', 320, 320, true );
}
add_action( 'after_setup_theme', 'noor_setup' );

/**
 * Default RTL-ready arabic content width.
 */
function noor_content_width() {
	$GLOBALS['content_width'] = 1408;
}
add_action( 'after_setup_theme', 'noor_content_width', 0 );

/**
 * This theme's entire design and copy are Arabic-first RTL (matching the
 * source design 1:1 — there is no LTR variant of the layout). WordPress
 * only outputs dir="rtl" when an RTL language pack is active, so relying on
 * Settings → General → Site Language would silently break the layout on any
 * install left on the default English locale despite all content being
 * Arabic. We force it here instead of hoping the admin remembers to switch
 * the site language.
 */
function noor_force_rtl_language_attributes( $output, $doctype ) {
	if ( 'html' !== $doctype ) {
		return $output;
	}
	$lang = get_bloginfo( 'language' );
	if ( ! preg_match( '/^ar/i', $lang ) ) {
		$lang = 'ar';
	}
	return 'dir="rtl" lang="' . esc_attr( $lang ) . '"';
}
add_filter( 'language_attributes', 'noor_force_rtl_language_attributes', 10, 2 );

/**
 * is_rtl() isn't filterable — it reads $wp_locale->text_direction, which
 * core only sets to "rtl" when an RTL language pack is loaded. Force it so
 * is_rtl()-aware core/plugin CSS (e.g. wp_enqueue_style's .rtl.css lookup)
 * also does the right thing on an English-locale install.
 */
function noor_force_wp_locale_rtl() {
	global $wp_locale;
	if ( isset( $wp_locale ) ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'after_setup_theme', 'noor_force_wp_locale_rtl', 0 );

/**
 * Register widget areas (footer stays fully dynamic even if unused by default templates).
 */
function noor_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'الشريط الجانبي للمدونة', 'noor-photography' ),
			'id'            => 'blog-sidebar',
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="t-eyebrow">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'noor_widgets_init' );

/**
 * Sensible excerpt length/ellipsis to match the editorial tone of the design.
 */
add_filter(
	'excerpt_length',
	function () {
		return 24;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);
