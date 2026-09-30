<?php
/**
 * Noor Photography theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOOR_VERSION', '1.0.1' );
define( 'NOOR_DIR', get_template_directory() );
define( 'NOOR_URI', get_template_directory_uri() );

require NOOR_DIR . '/inc/setup.php';
require NOOR_DIR . '/inc/enqueue.php';
require NOOR_DIR . '/inc/helpers.php';
require NOOR_DIR . '/inc/walkers.php';
require NOOR_DIR . '/inc/custom-post-types.php';
require NOOR_DIR . '/inc/custom-taxonomies.php';
require NOOR_DIR . '/inc/custom-fields.php';
require NOOR_DIR . '/inc/theme-options.php';
require NOOR_DIR . '/inc/page-meta.php';
require NOOR_DIR . '/inc/submissions.php';
require NOOR_DIR . '/inc/forms.php';
require NOOR_DIR . '/inc/demo-content.php';
require NOOR_DIR . '/inc/compat.php';
require NOOR_DIR . '/inc/seo.php';
require NOOR_DIR . '/inc/block-editor.php';
require NOOR_DIR . '/inc/customizer.php';
