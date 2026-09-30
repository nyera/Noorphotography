<?php
/**
 * Contact + booking + newsletter form handling. Plain PHP, no page-builder
 * form plugin dependency: nonce-verified CSRF protection, honeypot spam
 * trap, per-IP throttling, server-side validation matching (and backing up)
 * the client-side constraints, then mailed to the configured contact
 * address and redirected back with a status flag. Nothing here trusts the
 * client: every field is re-validated server-side regardless of the
 * `required`/`type` attributes in the markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Strip any stale status flags off the redirect target before re-adding our
 * own, so reloading a result page never stacks ?noor_status=sent&noor_status=error.
 */
function noor_clean_redirect_target() {
	$redirect = wp_get_referer() ?: home_url( '/' );
	return remove_query_arg( array( 'noor_status', 'noor_newsletter' ), $redirect );
}

/**
 * Lightweight per-IP throttle so a script hammering admin-post.php can't
 * spam wp_mail() even if it has a valid nonce (nonces are long-lived and
 * shared across a browser session, so they alone don't rate-limit).
 */
function noor_form_is_rate_limited( $form_key, $seconds = 20 ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'noor_throttle_' . $form_key . '_' . md5( $ip );
	if ( false !== get_transient( $key ) ) {
		return true;
	}
	set_transient( $key, 1, $seconds );
	return false;
}

/**
 * Only ever act on real POSTs — GET requests to admin-post.php with no body
 * can't carry a valid nonce anyway, but failing fast here avoids doing any
 * further work (or setting a throttle) for requests that were never a form
 * submission in the first place.
 */
function noor_require_post_request() {
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}

function noor_handle_contact_form() {
	noor_require_post_request();
	$redirect = noor_clean_redirect_target();

	if ( ! isset( $_POST['noor_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_contact_nonce'] ) ), 'noor_contact_form' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}
	if ( ! empty( $_POST['noor_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'sent', $redirect ) ); // silently drop bots.
		exit;
	}
	if ( noor_form_is_rate_limited( 'contact' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$name    = mb_substr( $name, 0, 100 );
	$subject = mb_substr( $subject, 0, 150 );
	$message = mb_substr( $message, 0, 4000 );

	if ( mb_strlen( $name ) < 2 || ! is_email( $email ) || mb_strlen( $message ) < 10 ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}

	noor_store_contact_message( compact( 'name', 'email', 'subject', 'message' ) );

	$to           = noor_get_option( 'contact_email' ) ?: get_option( 'admin_email' );
	$subject_line = sprintf( '[%s] %s', get_bloginfo( 'name' ), $subject ?: __( 'رسالة جديدة من نموذج التواصل', 'noor-photography' ) );
	$body         = sprintf( "%s: %s\n%s: %s\n\n%s", __( 'الاسم', 'noor-photography' ), $name, __( 'البريد', 'noor-photography' ), $email, $message );
	$headers      = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	wp_mail( $to, $subject_line, $body, $headers );

	wp_safe_redirect( add_query_arg( 'noor_status', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_noor_contact', 'noor_handle_contact_form' );
add_action( 'admin_post_nopriv_noor_contact', 'noor_handle_contact_form' );

function noor_handle_booking_form() {
	noor_require_post_request();
	$redirect = noor_clean_redirect_target();

	if ( ! isset( $_POST['noor_booking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_booking_nonce'] ) ), 'noor_booking_form' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}
	if ( ! empty( $_POST['noor_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'sent', $redirect ) );
		exit;
	}
	if ( noor_form_is_rate_limited( 'booking' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}

	$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$date     = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$service  = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$package  = isset( $_POST['package'] ) ? sanitize_text_field( wp_unslash( $_POST['package'] ) ) : '';
	$location = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
	$notes    = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';

	$name     = mb_substr( $name, 0, 100 );
	$phone    = mb_substr( $phone, 0, 30 );
	$service  = mb_substr( $service, 0, 150 );
	$package  = mb_substr( $package, 0, 150 );
	$location = mb_substr( $location, 0, 200 );
	$notes    = mb_substr( $notes, 0, 4000 );

	// Only accept a service/package label that actually exists — the select
	// options are server-rendered from the CPTs, so anything else means the
	// request didn't come from our own form.
	$valid_services = array_map(
		'get_the_title',
		get_posts(
			array(
				'post_type'      => 'service',
				'posts_per_page' => -1,
			)
		)
	);
	$valid_packages = array_map(
		'get_the_title',
		get_posts(
			array(
				'post_type'      => 'package',
				'posts_per_page' => -1,
			)
		)
	);

	$errors = array();
	if ( mb_strlen( $name ) < 3 ) {
		$errors[] = 'name';
	}
	if ( ! is_email( $email ) ) {
		$errors[] = 'email';
	}
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 9 ) {
		$errors[] = 'phone';
	}
	if ( ! $date || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || ! strtotime( $date ) ) {
		$errors[] = 'date';
	}
	if ( ! $service || ! in_array( $service, $valid_services, true ) ) {
		$errors[] = 'service';
	}
	if ( $package && ! in_array( $package, $valid_packages, true ) ) {
		$errors[] = 'package';
	}

	if ( ! empty( $errors ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}

	noor_store_booking_request( compact( 'name', 'email', 'phone', 'date', 'service', 'package', 'location', 'notes' ) );

	$to           = noor_get_option( 'contact_email' ) ?: get_option( 'admin_email' );
	$subject_line = sprintf( '[%s] %s', get_bloginfo( 'name' ), __( 'طلب حجز جديد', 'noor-photography' ) );
	$body         = implode(
		"\n",
		array(
			__( 'الاسم', 'noor-photography' ) . ': ' . $name,
			__( 'البريد', 'noor-photography' ) . ': ' . $email,
			__( 'الهاتف', 'noor-photography' ) . ': ' . $phone,
			__( 'التاريخ المبدئي', 'noor-photography' ) . ': ' . $date,
			__( 'الخدمة', 'noor-photography' ) . ': ' . $service,
			__( 'الباقة', 'noor-photography' ) . ': ' . $package,
			__( 'الموقع', 'noor-photography' ) . ': ' . $location,
			'',
			__( 'تفاصيل إضافية', 'noor-photography' ) . ':',
			$notes,
		)
	);
	$headers      = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	wp_mail( $to, $subject_line, $body, $headers );

	wp_safe_redirect( add_query_arg( 'noor_status', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_noor_booking', 'noor_handle_booking_form' );
add_action( 'admin_post_nopriv_noor_booking', 'noor_handle_booking_form' );

function noor_handle_newsletter_form() {
	noor_require_post_request();
	$redirect = noor_clean_redirect_target();

	if ( ! isset( $_POST['noor_newsletter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_newsletter_nonce'] ) ), 'noor_newsletter_form' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}
	if ( ! empty( $_POST['noor_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'noor_newsletter', 'sent', $redirect ) );
		exit;
	}
	if ( noor_form_is_rate_limited( 'newsletter' ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}

	$email = isset( $_POST['newsletter_email'] ) ? sanitize_email( wp_unslash( $_POST['newsletter_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'noor_status', 'error', $redirect ) );
		exit;
	}
	$to = noor_get_option( 'contact_email' ) ?: get_option( 'admin_email' );
	wp_mail( $to, sprintf( '[%s] %s', get_bloginfo( 'name' ), __( 'اشتراك جديد بالنشرة البريدية', 'noor-photography' ) ), $email );

	wp_safe_redirect( add_query_arg( 'noor_newsletter', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_noor_newsletter', 'noor_handle_newsletter_form' );
add_action( 'admin_post_nopriv_noor_newsletter', 'noor_handle_newsletter_form' );
