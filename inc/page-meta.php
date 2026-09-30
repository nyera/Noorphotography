<?php
/**
 * Page-level meta boxes: content specific to one page template rather than
 * global site settings. Registered as WP page templates so the admin picks
 * them from the normal "Template" dropdown — fully native UX.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------- register page templates -------- */

function noor_page_templates( $templates ) {
	$templates['template-about.php']   = __( 'نُور — عن الاستوديو', 'noor-photography' );
	$templates['template-pricing.php'] = __( 'نُور — الباقات والأسعار', 'noor-photography' );
	$templates['template-contact.php'] = __( 'نُور — تواصل معنا', 'noor-photography' );
	$templates['template-booking.php'] = __( 'نُور — الحجز', 'noor-photography' );
	return $templates;
}
add_filter( 'theme_page_templates', 'noor_page_templates' );

/* -------- meta boxes -------- */

function noor_add_page_meta_boxes( $post_type, $post ) {
	if ( 'page' !== $post_type ) {
		return;
	}
	$template = get_page_template_slug( $post->ID );
	$is_front = (int) get_option( 'page_on_front' ) === $post->ID;

	if ( $is_front ) {
		add_meta_box( 'noor_home_fields', __( 'محتوى الصفحة الرئيسية', 'noor-photography' ), 'noor_render_home_meta_box', 'page', 'normal', 'high' );
		add_meta_box( 'noor_home_sections', __( 'أقسام الصفحة الرئيسية', 'noor-photography' ), 'noor_render_home_sections_meta_box', 'page', 'side', 'default' );
	}
	if ( 'template-about.php' === $template ) {
		add_meta_box( 'noor_about_fields', __( 'محتوى صفحة عن الاستوديو', 'noor-photography' ), 'noor_render_about_meta_box', 'page', 'normal', 'high' );
	}
	if ( 'template-pricing.php' === $template || 'template-contact.php' === $template ) {
		add_meta_box( 'noor_faq_fields', __( 'الأسئلة الشائعة لهذه الصفحة', 'noor-photography' ), 'noor_render_faq_meta_box', 'page', 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'noor_add_page_meta_boxes', 10, 2 );

function noor_render_home_meta_box( $post ) {
	wp_nonce_field( 'noor_save_home', 'noor_home_nonce' );
	$hero_eyebrow = get_post_meta( $post->ID, '_noor_hero_eyebrow', true );
	$hero_caption = get_post_meta( $post->ID, '_noor_hero_caption', true );
	$manifesto    = get_post_meta( $post->ID, '_noor_manifesto', true );
	$cta_heading  = get_post_meta( $post->ID, '_noor_cta_heading', true );
	$cta_image    = get_post_meta( $post->ID, '_noor_cta_image', true );

	noor_field_text( '_noor_hero_eyebrow', __( 'الغلاف — سطر علوي', 'noor-photography' ), $hero_eyebrow, __( 'مثال: استوديو نُور · الرياض', 'noor-photography' ) );
	echo '<p class="description">' . esc_html__( 'عنوان الصفحة الرئيسية = عنوان الغلاف الكبير. محتوى الصفحة = الفقرة الوصفية تحت العنوان. الصورة المميّزة = صورة الغلاف.', 'noor-photography' ) . '</p>';
	noor_field_text( '_noor_hero_caption', __( 'الغلاف — سطر سفلي يمين', 'noor-photography' ), $hero_caption, __( 'مثال: ليلٌ ونُور · الرياض ٢٠٢٥', 'noor-photography' ) );
	noor_field_textarea( '_noor_manifesto', __( 'فقرة الفلسفة (بعد الغلاف مباشرة)', 'noor-photography' ), $manifesto, '', 4 );
	noor_field_text( '_noor_cta_heading', __( 'عنوان دعوة الإجراء الختامية', 'noor-photography' ), $cta_heading );

	echo '<div class="noor-single-image-field" data-target="_noor_cta_image">';
	echo '<label><strong>' . esc_html__( 'صورة خلفية دعوة الإجراء الختامية', 'noor-photography' ) . '</strong></label>';
	echo '<input type="hidden" id="_noor_cta_image" name="_noor_cta_image" value="' . esc_attr( $cta_image ) . '" />';
	echo '<div class="noor-gallery-preview">';
	if ( $cta_image ) {
		echo '<img src="' . esc_url( wp_get_attachment_image_url( $cta_image, 'thumbnail' ) ) . '" />';
	}
	echo '</div><button type="button" class="button noor-single-image-select">' . esc_html__( 'اختيار صورة', 'noor-photography' ) . '</button></div>';
}

function noor_render_home_sections_meta_box( $post ) {
	wp_nonce_field( 'noor_save_home_sections', 'noor_home_sections_nonce' );
	echo '<p class="description">' . esc_html__( 'أخفِ أي قسم لا تحتاجه دون حذف محتواه — يمكنك إعادة إظهاره لاحقًا. اسحب ⠿ لإعادة ترتيب الأقسام في الصفحة الرئيسية.', 'noor-photography' ) . '</p>';

	$labels = noor_homepage_sections();
	$order  = noor_get_home_section_order( $post->ID );

	echo '<input type="hidden" id="noor-home-section-order" name="_noor_home_section_order" value="' . esc_attr( implode( ',', $order ) ) . '" />';
	echo '<ul id="noor-home-sections-sortable" class="noor-sortable-list">';
	foreach ( $order as $key ) {
		if ( ! isset( $labels[ $key ] ) ) {
			continue;
		}
		echo '<li data-key="' . esc_attr( $key ) . '">';
		echo '<span class="noor-drag-handle" aria-hidden="true">⠿</span>';
		echo '<label><input type="checkbox" name="_noor_show_' . esc_attr( $key ) . '" value="1" ' . checked( noor_section_visible( $post->ID, $key ), true, false ) . ' /> ' . esc_html( $labels[ $key ] ) . '</label>';
		echo '</li>';
	}
	echo '</ul>';
}

function noor_save_home_sections_meta( $post_id ) {
	if ( ! isset( $_POST['noor_home_sections_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_home_sections_nonce'] ) ), 'noor_save_home_sections' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array_keys( noor_homepage_sections() ) as $key ) {
		update_post_meta( $post_id, '_noor_show_' . $key, ! empty( $_POST[ '_noor_show_' . $key ] ) ? '1' : '0' );
	}
	if ( isset( $_POST['_noor_home_section_order'] ) ) {
		$order = sanitize_text_field( wp_unslash( $_POST['_noor_home_section_order'] ) );
		update_post_meta( $post_id, '_noor_home_section_order', $order );
	}
}
add_action( 'save_post_page', 'noor_save_home_sections_meta' );

function noor_save_home_meta( $post_id ) {
	if ( ! isset( $_POST['noor_home_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_home_nonce'] ) ), 'noor_save_home' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( '_noor_hero_eyebrow', '_noor_hero_caption', '_noor_cta_heading' ) as $f ) {
		if ( isset( $_POST[ $f ] ) ) {
			update_post_meta( $post_id, $f, sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) );
		}
	}
	if ( isset( $_POST['_noor_manifesto'] ) ) {
		update_post_meta( $post_id, '_noor_manifesto', sanitize_textarea_field( wp_unslash( $_POST['_noor_manifesto'] ) ) );
	}
	if ( isset( $_POST['_noor_cta_image'] ) ) {
		update_post_meta( $post_id, '_noor_cta_image', absint( $_POST['_noor_cta_image'] ) );
	}
}
add_action( 'save_post_page', 'noor_save_home_meta' );

function noor_render_about_meta_box( $post ) {
	wp_nonce_field( 'noor_save_about', 'noor_about_nonce' );
	$process = get_post_meta( $post->ID, '_noor_process', true );
	$awards  = get_post_meta( $post->ID, '_noor_awards', true );
	$clients = get_post_meta( $post->ID, '_noor_clients', true );

	noor_field_textarea( '_noor_process', __( 'مراحل العمل — بصيغة: العنوان :: الوصف', 'noor-photography' ), $process, '', 6 );
	noor_field_textarea( '_noor_awards', __( 'الجوائز والمنشورات — بصيغة: السنة :: النص', 'noor-photography' ), $awards, '', 5 );
	noor_field_textarea( '_noor_clients', __( 'علامات عملنا معها (سطر لكل علامة)', 'noor-photography' ), $clients, '', 4 );
	echo '<p class="description">' . esc_html__( 'بيانات المصوّر (الاسم، النبذة، الخبرة، الجوائز، الصورة) تُدار من إعدادات نُور العامة.', 'noor-photography' ) . '</p>';
}

function noor_save_about_meta( $post_id ) {
	if ( ! isset( $_POST['noor_about_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_about_nonce'] ) ), 'noor_save_about' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( '_noor_process', '_noor_awards', '_noor_clients' ) as $f ) {
		if ( isset( $_POST[ $f ] ) ) {
			update_post_meta( $post_id, $f, sanitize_textarea_field( wp_unslash( $_POST[ $f ] ) ) );
		}
	}
}
add_action( 'save_post_page', 'noor_save_about_meta' );

function noor_render_faq_meta_box( $post ) {
	wp_nonce_field( 'noor_save_faq', 'noor_faq_nonce' );
	$faq = get_post_meta( $post->ID, '_noor_faq', true );
	noor_field_textarea( '_noor_faq', __( 'الأسئلة الشائعة — بصيغة: السؤال :: الجواب', 'noor-photography' ), $faq, '', 6 );
}

function noor_save_faq_meta( $post_id ) {
	if ( ! isset( $_POST['noor_faq_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_faq_nonce'] ) ), 'noor_save_faq' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_noor_faq'] ) ) {
		update_post_meta( $post_id, '_noor_faq', sanitize_textarea_field( wp_unslash( $_POST['_noor_faq'] ) ) );
	}
}
add_action( 'save_post_page', 'noor_save_faq_meta' );
