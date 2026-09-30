<?php
/**
 * Meta boxes for portfolio, service, testimonial and package post types.
 * Hand-rolled (no ACF dependency) — every field here actually drives the frontend.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_meta_boxes_enqueue( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'noor-admin-fields', NOOR_URI . '/assets/js/admin-fields.js', array( 'jquery', 'jquery-ui-sortable' ), NOOR_VERSION, true );
	wp_enqueue_style( 'noor-admin-fields', NOOR_URI . '/assets/css/admin-fields.css', array(), NOOR_VERSION );
}
add_action( 'admin_enqueue_scripts', 'noor_meta_boxes_enqueue' );

function noor_add_meta_boxes() {
	add_meta_box( 'noor_portfolio_details', __( 'تفاصيل المشروع', 'noor-photography' ), 'noor_render_portfolio_meta_box', 'portfolio', 'normal', 'high' );
	add_meta_box( 'noor_service_details', __( 'تفاصيل الخدمة', 'noor-photography' ), 'noor_render_service_meta_box', 'service', 'normal', 'high' );
	add_meta_box( 'noor_testimonial_details', __( 'تفاصيل رأي العميل', 'noor-photography' ), 'noor_render_testimonial_meta_box', 'testimonial', 'normal', 'high' );
	add_meta_box( 'noor_package_details', __( 'تفاصيل الباقة', 'noor-photography' ), 'noor_render_package_meta_box', 'package', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'noor_add_meta_boxes' );

/* ---------------- field renderers ---------------- */

function noor_field_text( $id, $label, $value, $desc = '', $type = 'text' ) {
	printf(
		'<p class="noor-field"><label for="%1$s"><strong>%2$s</strong></label><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" class="widefat" />%5$s</p>',
		esc_attr( $id ),
		esc_html( $label ),
		esc_attr( $type ),
		esc_attr( $value ),
		$desc ? '<p class="description">' . esc_html( $desc ) . '</p>' : ''
	);
}

function noor_field_textarea( $id, $label, $value, $desc = '', $rows = 4 ) {
	printf(
		'<p class="noor-field"><label for="%1$s"><strong>%2$s</strong></label><textarea id="%1$s" name="%1$s" rows="%3$d" class="widefat">%4$s</textarea>%5$s</p>',
		esc_attr( $id ),
		esc_html( $label ),
		(int) $rows,
		esc_textarea( $value ),
		$desc ? '<p class="description">' . esc_html( $desc ) . '</p>' : ''
	);
}

function noor_field_checkbox( $id, $label, $checked ) {
	printf(
		'<p class="noor-field"><label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s /> <strong>%3$s</strong></label></p>',
		esc_attr( $id ),
		checked( $checked, true, false ),
		esc_html( $label )
	);
}

function noor_field_select( $id, $label, $value, $options ) {
	echo '<p class="noor-field"><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $label ) . '</strong></label><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" class="widefat">';
	foreach ( $options as $key => $optlabel ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $value, $key, false ), esc_html( $optlabel ) );
	}
	echo '</select></p>';
}

function noor_field_gallery( $id, $label, $ids ) {
	$ids_str = is_array( $ids ) ? implode( ',', $ids ) : '';
	echo '<p class="noor-field"><label><strong>' . esc_html( $label ) . '</strong></label></p>';
	echo '<div class="noor-gallery-field" data-target="' . esc_attr( $id ) . '">';
	echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $ids_str ) . '" />';
	echo '<div class="noor-gallery-preview">';
	if ( is_array( $ids ) ) {
		foreach ( $ids as $attachment_id ) {
			$src = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
			if ( $src ) {
				echo '<img src="' . esc_url( $src ) . '" />';
			}
		}
	}
	echo '</div>';
	echo '<button type="button" class="button noor-gallery-select">' . esc_html__( 'اختيار الصور', 'noor-photography' ) . '</button> ';
	echo '<button type="button" class="button noor-gallery-clear">' . esc_html__( 'إفراغ', 'noor-photography' ) . '</button>';
	echo '</div>';
}

/* ---------------- portfolio ---------------- */

function noor_render_portfolio_meta_box( $post ) {
	wp_nonce_field( 'noor_save_portfolio', 'noor_portfolio_nonce' );
	$client   = get_post_meta( $post->ID, '_noor_client', true );
	$location = get_post_meta( $post->ID, '_noor_location', true );
	$year     = get_post_meta( $post->ID, '_noor_year', true );
	$ratio    = get_post_meta( $post->ID, '_noor_ratio', true ) ?: 'landscape';
	$gallery  = get_post_meta( $post->ID, '_noor_gallery', true );
	$gallery  = $gallery ? array_map( 'intval', explode( ',', $gallery ) ) : array();

	noor_field_text( '_noor_client', __( 'العميل', 'noor-photography' ), $client );
	noor_field_text( '_noor_location', __( 'الموقع', 'noor-photography' ), $location );
	noor_field_text( '_noor_year', __( 'السنة', 'noor-photography' ), $year, __( 'مثال: ٢٠٢٥', 'noor-photography' ) );
	noor_field_select(
		'_noor_ratio',
		__( 'نسبة العرض الافتراضية للبطاقة', 'noor-photography' ),
		$ratio,
		array(
			'landscape' => __( 'أفقي (3:2)', 'noor-photography' ),
			'portrait'  => __( 'عمودي (4:5)', 'noor-photography' ),
			'square'    => __( 'مربع (1:1)', 'noor-photography' ),
		)
	);
	noor_field_checkbox( '_noor_featured', __( 'مشروع مميز على الصفحة الرئيسية', 'noor-photography' ), (bool) get_post_meta( $post->ID, '_noor_featured', true ) );
	echo '<p class="description">' . esc_html__( 'إذا لم يُميَّز أي مشروع، تعرض الصفحة الرئيسية أحدث المشاريع تلقائيًا.', 'noor-photography' ) . '</p>';
	noor_field_gallery( '_noor_gallery', __( 'معرض صور المشروع', 'noor-photography' ), $gallery );
	echo '<p class="description">' . esc_html__( 'الصورة المميّزة تُستخدم كصورة غلاف البطاقة والصفحة الرئيسية للمشروع. المقتطف (Excerpt) يُستخدم كوصف قصير، ومحتوى المقال يُستخدم كوصف المشروع الكامل.', 'noor-photography' ) . '</p>';
}

function noor_save_portfolio_meta( $post_id ) {
	if ( ! isset( $_POST['noor_portfolio_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_portfolio_nonce'] ) ), 'noor_save_portfolio' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array( '_noor_client', '_noor_location', '_noor_year' );
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	if ( isset( $_POST['_noor_ratio'] ) && in_array( $_POST['_noor_ratio'], array( 'landscape', 'portrait', 'square' ), true ) ) {
		update_post_meta( $post_id, '_noor_ratio', sanitize_key( $_POST['_noor_ratio'] ) );
	}
	update_post_meta( $post_id, '_noor_featured', ! empty( $_POST['_noor_featured'] ) );
	if ( isset( $_POST['_noor_gallery'] ) ) {
		$ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_POST['_noor_gallery'] ) ) ) ) );
		update_post_meta( $post_id, '_noor_gallery', implode( ',', $ids ) );
	}
}
add_action( 'save_post_portfolio', 'noor_save_portfolio_meta' );

/* ---------------- service ---------------- */

function noor_render_service_meta_box( $post ) {
	wp_nonce_field( 'noor_save_service', 'noor_service_nonce' );
	$price    = get_post_meta( $post->ID, '_noor_price', true );
	$features = get_post_meta( $post->ID, '_noor_features', true );
	$includes = get_post_meta( $post->ID, '_noor_includes', true );
	$faq      = get_post_meta( $post->ID, '_noor_faq', true );

	noor_field_text( '_noor_price', __( 'السعر (نص حر)', 'noor-photography' ), $price, __( 'مثال: تبدأ من ١٢٬٠٠٠ ر.س', 'noor-photography' ) );
	noor_field_textarea( '_noor_features', __( 'المزايا (سطر لكل ميزة)', 'noor-photography' ), $features, '', 5 );
	noor_field_textarea( '_noor_includes', __( 'ما الذي يشمله (سطر لكل بند)', 'noor-photography' ), $includes, '', 5 );
	noor_field_textarea( '_noor_faq', __( 'الأسئلة الشائعة — بصيغة: السؤال :: الجواب (سطر لكل سؤال)', 'noor-photography' ), $faq, '', 6 );
	echo '<p class="description">' . esc_html__( 'المقتطف (Excerpt) يُستخدم كوصف قصير للخدمة في الصفحة الرئيسية وأرشيف الخدمات. ترتيب العرض في الأرشيف يُضبط عبر "الترتيب" أسفل الصفحة.', 'noor-photography' ) . '</p>';
}

function noor_save_service_meta( $post_id ) {
	if ( ! isset( $_POST['noor_service_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_service_nonce'] ) ), 'noor_save_service' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_noor_price'] ) ) {
		update_post_meta( $post_id, '_noor_price', sanitize_text_field( wp_unslash( $_POST['_noor_price'] ) ) );
	}
	foreach ( array( '_noor_features', '_noor_includes', '_noor_faq' ) as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post_service', 'noor_save_service_meta' );

/* ---------------- testimonial ---------------- */

function noor_render_testimonial_meta_box( $post ) {
	wp_nonce_field( 'noor_save_testimonial', 'noor_testimonial_nonce' );
	$role   = get_post_meta( $post->ID, '_noor_role', true );
	$rating = get_post_meta( $post->ID, '_noor_rating', true ) ?: '5';

	noor_field_text( '_noor_role', __( 'الدور / المناسبة', 'noor-photography' ), $role, __( 'مثال: عروس · الرياض', 'noor-photography' ) );
	noor_field_select(
		'_noor_rating',
		__( 'التقييم', 'noor-photography' ),
		$rating,
		array(
			'5' => '★★★★★',
			'4' => '★★★★',
			'3' => '★★★',
			'2' => '★★',
			'1' => '★',
		)
	);
	noor_field_checkbox( '_noor_featured', __( 'رأي مميز على الصفحة الرئيسية', 'noor-photography' ), (bool) get_post_meta( $post->ID, '_noor_featured', true ) );
	echo '<p class="description">' . esc_html__( 'العنوان = اسم العميل. محتوى المقال = نص الرأي. الصورة المميّزة = صورة العميل. إذا لم يُميَّز أي رأي، تعرض الصفحة الرئيسية أحدث الآراء تلقائيًا.', 'noor-photography' ) . '</p>';
}

function noor_save_testimonial_meta( $post_id ) {
	if ( ! isset( $_POST['noor_testimonial_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_testimonial_nonce'] ) ), 'noor_save_testimonial' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_noor_role'] ) ) {
		update_post_meta( $post_id, '_noor_role', sanitize_text_field( wp_unslash( $_POST['_noor_role'] ) ) );
	}
	if ( isset( $_POST['_noor_rating'] ) ) {
		update_post_meta( $post_id, '_noor_rating', absint( $_POST['_noor_rating'] ) );
	}
	update_post_meta( $post_id, '_noor_featured', ! empty( $_POST['_noor_featured'] ) );
}
add_action( 'save_post_testimonial', 'noor_save_testimonial_meta' );

/* ---------------- package ---------------- */

function noor_render_package_meta_box( $post ) {
	wp_nonce_field( 'noor_save_package', 'noor_package_nonce' );
	$price     = get_post_meta( $post->ID, '_noor_price', true );
	$note      = get_post_meta( $post->ID, '_noor_note', true );
	$highlight = (bool) get_post_meta( $post->ID, '_noor_highlight', true );
	$features  = get_post_meta( $post->ID, '_noor_features', true );
	$compare   = get_post_meta( $post->ID, '_noor_compare', true );

	noor_field_text( '_noor_price', __( 'السعر (أرقام فقط، بدون العملة)', 'noor-photography' ), $price, __( 'مثال: ١٢٬٠٠٠', 'noor-photography' ) );
	noor_field_text( '_noor_note', __( 'ملاحظة قصيرة أعلى الاسم', 'noor-photography' ), $note, __( 'مثال: الأكثر طلبًا', 'noor-photography' ) );
	noor_field_checkbox( '_noor_highlight', __( 'إبراز هذه الباقة (تصميم داكن مميّز)', 'noor-photography' ), $highlight );
	noor_field_textarea( '_noor_features', __( 'المزايا (سطر لكل ميزة)', 'noor-photography' ), $features, '', 6 );
	noor_field_textarea( '_noor_compare', __( 'صفوف جدول المقارنة — بصيغة: التسمية :: القيمة (سطر لكل صف)', 'noor-photography' ), $compare, __( 'مثال: ساعات التغطية :: ١٠ ساعات', 'noor-photography' ), 6 );
	echo '<p class="description">' . esc_html__( 'ترتيب الباقات في الصفحة يتبع "الترتيب" أسفل الصفحة (Page Attributes).', 'noor-photography' ) . '</p>';
}

function noor_save_package_meta( $post_id ) {
	if ( ! isset( $_POST['noor_package_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_package_nonce'] ) ), 'noor_save_package' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( '_noor_price', '_noor_note' ) as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	update_post_meta( $post_id, '_noor_highlight', ! empty( $_POST['_noor_highlight'] ) );
	foreach ( array( '_noor_features', '_noor_compare' ) as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post_package', 'noor_save_package_meta' );
