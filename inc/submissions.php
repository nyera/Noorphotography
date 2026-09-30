<?php
/**
 * Durable storage for booking + contact form submissions.
 *
 * wp_mail() has no delivery guarantee — on a lot of hosts it silently fails
 * without an SMTP plugin configured. The email in inc/forms.php is a
 * best-effort notification; this is the actual record of what was
 * submitted, visible and manageable from wp-admin like any other content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_register_submission_post_types() {
	register_post_type(
		'booking_request',
		array(
			'labels'              => array(
				'name'          => __( 'طلبات الحجز', 'noor-photography' ),
				'singular_name' => __( 'طلب حجز', 'noor-photography' ),
				'all_items'     => __( 'كل الطلبات', 'noor-photography' ),
				'view_item'     => __( 'عرض الطلب', 'noor-photography' ),
				'search_items'  => __( 'بحث في الطلبات', 'noor-photography' ),
				'not_found'     => __( 'لا توجد طلبات حجز بعد', 'noor-photography' ),
				'menu_name'     => __( 'طلبات الحجز', 'noor-photography' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-calendar-alt',
			'menu_position'       => 25,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
		)
	);

	register_post_type(
		'contact_message',
		array(
			'labels'              => array(
				'name'          => __( 'رسائل التواصل', 'noor-photography' ),
				'singular_name' => __( 'رسالة', 'noor-photography' ),
				'all_items'     => __( 'كل الرسائل', 'noor-photography' ),
				'view_item'     => __( 'عرض الرسالة', 'noor-photography' ),
				'search_items'  => __( 'بحث في الرسائل', 'noor-photography' ),
				'not_found'     => __( 'لا توجد رسائل بعد', 'noor-photography' ),
				'menu_name'     => __( 'رسائل التواصل', 'noor-photography' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-email',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'noor_register_submission_post_types' );

function noor_submission_statuses() {
	return array(
		'new'       => __( 'جديد', 'noor-photography' ),
		'contacted' => __( 'تم التواصل', 'noor-photography' ),
		'confirmed' => __( 'مؤكد', 'noor-photography' ),
		'closed'    => __( 'مغلق', 'noor-photography' ),
	);
}

/* -------- create records (called from inc/forms.php) -------- */

function noor_store_booking_request( $data ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'booking_request',
			'post_title'  => sprintf( '%s — %s', $data['name'], $data['date'] ),
			'post_status' => 'publish',
		)
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}
	$fields = array( 'name', 'email', 'phone', 'date', 'service', 'package', 'location', 'notes' );
	foreach ( $fields as $field ) {
		update_post_meta( $post_id, '_noor_b_' . $field, $data[ $field ] ?? '' );
	}
	update_post_meta( $post_id, '_noor_b_status', 'new' );
	update_post_meta( $post_id, '_noor_b_ip', isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	return $post_id;
}

function noor_store_contact_message( $data ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'contact_message',
			'post_title'  => sprintf( '%s: %s', $data['name'], $data['subject'] ?: __( '(بدون موضوع)', 'noor-photography' ) ),
			'post_status' => 'publish',
		)
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}
	$fields = array( 'name', 'email', 'subject', 'message' );
	foreach ( $fields as $field ) {
		update_post_meta( $post_id, '_noor_i_' . $field, $data[ $field ] ?? '' );
	}
	update_post_meta( $post_id, '_noor_i_status', 'new' );
	update_post_meta( $post_id, '_noor_i_ip', isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	return $post_id;
}

/* -------- admin list columns -------- */

function noor_booking_columns( $columns ) {
	$columns = array(
		'cb'      => $columns['cb'],
		'title'   => __( 'الاسم والتاريخ', 'noor-photography' ),
		'phone'   => __( 'الجوال', 'noor-photography' ),
		'email'   => __( 'البريد', 'noor-photography' ),
		'service' => __( 'الخدمة', 'noor-photography' ),
		'status'  => __( 'الحالة', 'noor-photography' ),
		'date'    => __( 'تاريخ الإرسال', 'noor-photography' ),
	);
	return $columns;
}
add_filter( 'manage_booking_request_posts_columns', 'noor_booking_columns' );

function noor_booking_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'phone':
			echo '<span dir="ltr">' . esc_html( get_post_meta( $post_id, '_noor_b_phone', true ) ) . '</span>';
			break;
		case 'email':
			echo esc_html( get_post_meta( $post_id, '_noor_b_email', true ) );
			break;
		case 'service':
			echo esc_html( get_post_meta( $post_id, '_noor_b_service', true ) );
			break;
		case 'status':
			$statuses = noor_submission_statuses();
			$status   = get_post_meta( $post_id, '_noor_b_status', true ) ?: 'new';
			echo esc_html( $statuses[ $status ] ?? $status );
			break;
	}
}
add_action( 'manage_booking_request_posts_custom_column', 'noor_booking_column_content', 10, 2 );

function noor_contact_columns( $columns ) {
	$columns = array(
		'cb'     => $columns['cb'],
		'title'  => __( 'الاسم والموضوع', 'noor-photography' ),
		'email'  => __( 'البريد', 'noor-photography' ),
		'status' => __( 'الحالة', 'noor-photography' ),
		'date'   => __( 'تاريخ الإرسال', 'noor-photography' ),
	);
	return $columns;
}
add_filter( 'manage_contact_message_posts_columns', 'noor_contact_columns' );

function noor_contact_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'email':
			echo esc_html( get_post_meta( $post_id, '_noor_i_email', true ) );
			break;
		case 'status':
			$statuses = noor_submission_statuses();
			$status   = get_post_meta( $post_id, '_noor_i_status', true ) ?: 'new';
			echo esc_html( $statuses[ $status ] ?? $status );
			break;
	}
}
add_action( 'manage_contact_message_posts_custom_column', 'noor_contact_column_content', 10, 2 );

/* -------- detail meta boxes (view submitted data + set status) -------- */

function noor_add_submission_meta_boxes() {
	add_meta_box( 'noor_booking_detail', __( 'تفاصيل طلب الحجز', 'noor-photography' ), 'noor_render_booking_detail_box', 'booking_request', 'normal', 'high' );
	add_meta_box( 'noor_contact_detail', __( 'تفاصيل الرسالة', 'noor-photography' ), 'noor_render_contact_detail_box', 'contact_message', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'noor_add_submission_meta_boxes' );

function noor_render_status_select( $post_id, $meta_key, $nonce_action, $nonce_name ) {
	wp_nonce_field( $nonce_action, $nonce_name );
	$current = get_post_meta( $post_id, $meta_key, true ) ?: 'new';
	echo '<p class="noor-field"><label><strong>' . esc_html__( 'الحالة', 'noor-photography' ) . '</strong></label><select name="' . esc_attr( $meta_key ) . '" class="widefat">';
	foreach ( noor_submission_statuses() as $key => $label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
	}
	echo '</select></p>';
}

function noor_render_readonly_row( $label, $value, $dir = null ) {
	printf(
		'<p class="noor-field"><label><strong>%1$s</strong></label><span style="display:block;padding:0.5rem 0.75rem;background:#f6f7f7;border:1px solid #dcdcde"%3$s>%2$s</span></p>',
		esc_html( $label ),
		nl2br( esc_html( $value ) ),
		$dir ? ' dir="' . esc_attr( $dir ) . '"' : ''
	);
}

function noor_render_booking_detail_box( $post ) {
	noor_render_status_select( $post->ID, '_noor_b_status', 'noor_save_booking_status', 'noor_booking_status_nonce' );
	noor_render_readonly_row( __( 'الاسم', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_name', true ) );
	noor_render_readonly_row( __( 'البريد الإلكتروني', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_email', true ), 'ltr' );
	noor_render_readonly_row( __( 'رقم الجوال', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_phone', true ), 'ltr' );
	noor_render_readonly_row( __( 'التاريخ المطلوب', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_date', true ) );
	noor_render_readonly_row( __( 'الخدمة', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_service', true ) );
	noor_render_readonly_row( __( 'الباقة', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_package', true ) );
	noor_render_readonly_row( __( 'الموقع', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_location', true ) );
	noor_render_readonly_row( __( 'تفاصيل إضافية', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_notes', true ) );
	noor_render_readonly_row( __( 'عنوان IP وقت الإرسال', 'noor-photography' ), get_post_meta( $post->ID, '_noor_b_ip', true ), 'ltr' );
}

function noor_render_contact_detail_box( $post ) {
	noor_render_status_select( $post->ID, '_noor_i_status', 'noor_save_contact_status', 'noor_contact_status_nonce' );
	noor_render_readonly_row( __( 'الاسم', 'noor-photography' ), get_post_meta( $post->ID, '_noor_i_name', true ) );
	noor_render_readonly_row( __( 'البريد الإلكتروني', 'noor-photography' ), get_post_meta( $post->ID, '_noor_i_email', true ), 'ltr' );
	noor_render_readonly_row( __( 'الموضوع', 'noor-photography' ), get_post_meta( $post->ID, '_noor_i_subject', true ) );
	noor_render_readonly_row( __( 'الرسالة', 'noor-photography' ), get_post_meta( $post->ID, '_noor_i_message', true ) );
	noor_render_readonly_row( __( 'عنوان IP وقت الإرسال', 'noor-photography' ), get_post_meta( $post->ID, '_noor_i_ip', true ), 'ltr' );
}

function noor_save_booking_status( $post_id ) {
	if ( ! isset( $_POST['noor_booking_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_booking_status_nonce'] ) ), 'noor_save_booking_status' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_noor_b_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['_noor_b_status'] ) );
		if ( array_key_exists( $status, noor_submission_statuses() ) ) {
			update_post_meta( $post_id, '_noor_b_status', $status );
		}
	}
}
add_action( 'save_post_booking_request', 'noor_save_booking_status' );

function noor_save_contact_status( $post_id ) {
	if ( ! isset( $_POST['noor_contact_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_contact_status_nonce'] ) ), 'noor_save_contact_status' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_noor_i_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['_noor_i_status'] ) );
		if ( array_key_exists( $status, noor_submission_statuses() ) ) {
			update_post_meta( $post_id, '_noor_i_status', $status );
		}
	}
}
add_action( 'save_post_contact_message', 'noor_save_contact_status' );

/**
 * "New" count bubble on the two admin menu entries, same pattern as core's
 * Comments bubble — makes unread submissions impossible to miss.
 *
 * Core provides no filter to append a badge to an existing admin menu
 * item's label, so directly reading/mutating the $menu global late (added
 * at priority 999, after menu items already exist) is the standard,
 * widely-used technique for this — only individual label strings for our
 * own two menu items are appended to, the global itself is never replaced.
 */
function noor_submission_menu_bubble() {
	global $menu; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	if ( ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( empty( $item[2] ) ) {
			continue;
		}
		$post_type = '';
		if ( 'edit.php?post_type=booking_request' === $item[2] ) {
			$post_type = 'booking_request';
			$meta_key  = '_noor_b_status';
		} elseif ( 'edit.php?post_type=contact_message' === $item[2] ) {
			$post_type = 'contact_message';
			$meta_key  = '_noor_i_status';
		} else {
			continue;
		}
		$new_count = new WP_Query(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => $meta_key,
				'meta_value'     => 'new',
				'no_found_rows'  => false,
			)
		);
		if ( $new_count->found_posts > 0 ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$menu[ $i ][0] .= ' <span class="awaiting-mod count-' . (int) $new_count->found_posts . '"><span class="pending-count">' . (int) $new_count->found_posts . '</span></span>';
		}
	}
}
add_action( 'admin_menu', 'noor_submission_menu_bubble', 999 );
