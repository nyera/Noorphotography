<?php
/**
 * Third-party plugin compatibility. The theme works standalone; these hooks
 * only activate when the relevant plugin is actually present, and none of
 * them are required dependencies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==================== Elementor ==================== */

function noor_is_elementor_page( $post_id ) {
	if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Elementor renders its own sections and expects to control width itself.
 * Our page.php normally wraps content in the `.shell` max-width container
 * and adds a page-header block; both would fight Elementor's canvas, so we
 * get out of the way entirely on pages built with it.
 */
function noor_elementor_body_class( $classes ) {
	if ( is_page() && noor_is_elementor_page( get_the_ID() ) ) {
		$classes[] = 'noor-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'noor_elementor_body_class' );

/**
 * Register theme support Elementor looks for so its own UI (e.g. "Theme
 * Builder" location indicators) behaves correctly.
 */
function noor_elementor_theme_support() {
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'noor_elementor_theme_support' );

/* ==================== WooCommerce ==================== */

/**
 * This is a photography portfolio theme, not a shop theme — WooCommerce is
 * NOT a dependency and no shop-specific templates are provided. These hooks
 * only make sure that *if* an admin activates WooCommerce (e.g. to sell
 * prints or presets), its default templates inherit the theme's shell width
 * and typography instead of rendering unstyled.
 */
function noor_woocommerce_theme_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'noor_woocommerce_theme_support' );

function noor_woocommerce_wrapper_start() {
	echo '<main class="page-content"><div class="shell woocommerce-shell">';
}
function noor_woocommerce_wrapper_end() {
	echo '</div></main>';
}
add_action( 'woocommerce_before_main_content', 'noor_woocommerce_wrapper_start' );
add_action( 'woocommerce_after_main_content', 'noor_woocommerce_wrapper_end' );

/* ==================== Yoast SEO / Rank Math ==================== */

/**
 * Both plugins output their own <title>, meta description, canonical, and
 * Open Graph tags. inc/seo.php's fallback output must stay silent whenever
 * either is active so tags are never duplicated — see noor_seo_plugin_active().
 */
function noor_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );
}
