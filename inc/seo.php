<?php
/**
 * Minimal, dependency-free SEO output: meta description, canonical, Open
 * Graph/Twitter tags, and basic JSON-LD. This theme does NOT try to replace
 * Yoast SEO or Rank Math — every function here bails out immediately if
 * either plugin is active (see noor_seo_plugin_active() in inc/compat.php),
 * so tags are never duplicated. It exists purely so a site running with no
 * SEO plugin at all still ships correct, crawlable meta tags out of the box.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_seo_description() {
	if ( is_singular() ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}
	if ( is_front_page() ) {
		return noor_get_option( 'brand_description' ) ?: get_bloginfo( 'description' );
	}
	if ( is_post_type_archive( 'portfolio' ) ) {
		return __( 'أرشيف أعمال ومشاريع التصوير الفوتوغرافي.', 'noor-photography' );
	}
	if ( is_post_type_archive( 'service' ) ) {
		return __( 'خدمات التصوير الفوتوغرافي المتاحة.', 'noor-photography' );
	}
	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_description() ) ?: get_the_archive_title();
	}
	return get_bloginfo( 'description' );
}

function noor_seo_image_url() {
	if ( is_singular() && has_post_thumbnail() ) {
		return get_the_post_thumbnail_url( get_the_ID(), 'noor-hero' );
	}
	$photographer_image = (int) noor_get_option( 'photographer_image' );
	if ( $photographer_image ) {
		return wp_get_attachment_image_url( $photographer_image, 'noor-hero' );
	}
	return '';
}

function noor_output_seo_meta() {
	if ( noor_seo_plugin_active() ) {
		return;
	}

	$description = noor_seo_description();
	if ( $description ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( wp_trim_words( $description, 30, '…' ) ) );
	}

	if ( is_singular() && ! is_front_page() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( get_permalink() ) );
	} elseif ( is_front_page() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( home_url( '/' ) ) );
	}

	if ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex,follow" />' . "\n";
	}

	$og_title = is_front_page() ? get_bloginfo( 'name' ) : wp_get_document_title();
	$og_type  = is_singular() ? 'article' : 'website';
	$og_image = noor_seo_image_url();

	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $og_title ) );
	if ( $description ) {
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( wp_trim_words( $description, 30, '…' ) ) );
	}
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) ) ) );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( 'ar_SA' ) );
	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $og_image ) );
	}

	echo '<meta name="twitter:card" content="' . esc_attr( $og_image ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
}
add_action( 'wp_head', 'noor_output_seo_meta', 1 );

/**
 * BreadcrumbList + (where relevant) CreativeWork structured data. Search
 * engines can already infer most of this from semantic markup, but explicit
 * JSON-LD is a safe, additive improvement that doesn't fight an SEO plugin
 * if one is later activated (this only outputs when noor_seo_plugin_active()
 * is false, same as the meta tags above).
 */
function noor_output_structured_data() {
	if ( noor_seo_plugin_active() ) {
		return;
	}
	if ( ! is_singular( array( 'portfolio', 'service' ) ) ) {
		return;
	}

	$type = 'portfolio' === get_post_type() ? 'CreativeWork' : 'Service';
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => $type,
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ),
		'url'         => get_permalink(),
	);
	if ( has_post_thumbnail() ) {
		$data['image'] = get_the_post_thumbnail_url( get_the_ID(), 'noor-hero' );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'noor_output_structured_data', 2 );
