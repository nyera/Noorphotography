<?php
/**
 * Asset enqueueing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_enqueue_assets() {
	// version intentionally null: this is an external Google Fonts URL, not a
	// local asset — WP's own version-cache-busting query param doesn't apply
	// to a third-party host, and Google's CDN handles its own caching.
	wp_enqueue_style( 'noor-fonts', noor_google_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'noor-main', NOOR_URI . '/assets/css/main.css', array(), NOOR_VERSION );

	wp_enqueue_script( 'noor-main', NOOR_URI . '/assets/js/main.js', array(), NOOR_VERSION, true );
	wp_localize_script(
		'noor-main',
		'noorData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'search'  => noor_get_search_index(),
			'i18n'    => array(
				'noResults' => __( 'لا توجد نتائج مطابقة', 'noor-photography' ),
			),
		)
	);

	if ( is_singular( 'portfolio' ) || is_post_type_archive( 'portfolio' ) ) {
		wp_enqueue_script( 'noor-lightbox', NOOR_URI . '/assets/js/lightbox.js', array(), NOOR_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'noor_enqueue_assets' );

/**
 * Preconnect to Google Fonts for perf parity with the original design.
 */
add_filter(
	'wp_resource_hints',
	function ( $urls, $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}
		return $urls;
	},
	10,
	2
);
