<?php
/**
 * Custom post types: portfolio, service, testimonial, package.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_register_post_types() {

	register_post_type(
		'portfolio',
		array(
			'labels'        => array(
				'name'          => __( 'الأعمال', 'noor-photography' ),
				'singular_name' => __( 'مشروع', 'noor-photography' ),
				'add_new'       => __( 'إضافة مشروع', 'noor-photography' ),
				'add_new_item'  => __( 'إضافة مشروع جديد', 'noor-photography' ),
				'edit_item'     => __( 'تعديل المشروع', 'noor-photography' ),
				'new_item'      => __( 'مشروع جديد', 'noor-photography' ),
				'view_item'     => __( 'عرض المشروع', 'noor-photography' ),
				'search_items'  => __( 'بحث في الأعمال', 'noor-photography' ),
				'not_found'     => __( 'لا توجد مشاريع', 'noor-photography' ),
				'menu_name'     => __( 'الأعمال', 'noor-photography' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'portfolio',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-camera',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'show_in_rest'  => true,
			'template'      => array(),
		)
	);

	register_post_type(
		'service',
		array(
			'labels'        => array(
				'name'          => __( 'الخدمات', 'noor-photography' ),
				'singular_name' => __( 'خدمة', 'noor-photography' ),
				'add_new'       => __( 'إضافة خدمة', 'noor-photography' ),
				'add_new_item'  => __( 'إضافة خدمة جديدة', 'noor-photography' ),
				'edit_item'     => __( 'تعديل الخدمة', 'noor-photography' ),
				'new_item'      => __( 'خدمة جديدة', 'noor-photography' ),
				'view_item'     => __( 'عرض الخدمة', 'noor-photography' ),
				'search_items'  => __( 'بحث في الخدمات', 'noor-photography' ),
				'not_found'     => __( 'لا توجد خدمات', 'noor-photography' ),
				'menu_name'     => __( 'الخدمات', 'noor-photography' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'services',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'              => array(
				'name'          => __( 'آراء العملاء', 'noor-photography' ),
				'singular_name' => __( 'رأي عميل', 'noor-photography' ),
				'add_new'       => __( 'إضافة رأي', 'noor-photography' ),
				'add_new_item'  => __( 'إضافة رأي جديد', 'noor-photography' ),
				'edit_item'     => __( 'تعديل الرأي', 'noor-photography' ),
				'menu_name'     => __( 'آراء العملاء', 'noor-photography' ),
			),
			'public'              => true,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest'        => true,
		)
	);

	register_post_type(
		'package',
		array(
			'labels'              => array(
				'name'          => __( 'باقات الأسعار', 'noor-photography' ),
				'singular_name' => __( 'باقة', 'noor-photography' ),
				'add_new'       => __( 'إضافة باقة', 'noor-photography' ),
				'add_new_item'  => __( 'إضافة باقة جديدة', 'noor-photography' ),
				'edit_item'     => __( 'تعديل الباقة', 'noor-photography' ),
				'menu_name'     => __( 'باقات الأسعار', 'noor-photography' ),
			),
			'public'              => true,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-money-alt',
			'menu_position'       => 8,
			'supports'            => array( 'title', 'page-attributes' ),
			'show_in_rest'        => true,
		)
	);
}
add_action( 'init', 'noor_register_post_types' );
