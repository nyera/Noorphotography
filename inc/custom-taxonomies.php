<?php
/**
 * Custom taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_register_taxonomies() {
	register_taxonomy(
		'portfolio_category',
		'portfolio',
		array(
			'labels'            => array(
				'name'          => __( 'تصنيفات الأعمال', 'noor-photography' ),
				'singular_name' => __( 'تصنيف', 'noor-photography' ),
				'search_items'  => __( 'بحث في التصنيفات', 'noor-photography' ),
				'all_items'     => __( 'كل التصنيفات', 'noor-photography' ),
				'edit_item'     => __( 'تعديل التصنيف', 'noor-photography' ),
				'add_new_item'  => __( 'إضافة تصنيف جديد', 'noor-photography' ),
				'menu_name'     => __( 'التصنيفات', 'noor-photography' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'portfolio-category',
				'with_front' => false,
			),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'noor_register_taxonomies' );
