<?php
/**
 * Global theme options — everything reused across multiple templates
 * (brand info, social links, stats, photographer bio, instagram grid).
 * Settings API, no ACF dependency.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOOR_OPTION', 'noor_options' );

function noor_default_options() {
	return array(
		'brand_name_ar'           => '',
		'brand_name_en'           => '',
		'brand_tagline'           => '',
		'brand_description'       => '',
		'contact_email'           => get_option( 'admin_email' ),
		'contact_phone'           => '',
		'contact_address'         => '',
		'contact_hours'           => '',
		'social_links'            => '',
		'footer_text'             => __( '© %year% جميع الحقوق محفوظة.', 'noor-photography' ),
		'stats'                   => '',
		'photographer_name'       => '',
		'photographer_bio'        => '',
		'photographer_image'      => '',
		'photographer_experience' => '',
		'photographer_awards'     => '',
		'instagram_handle'        => '',
		'instagram_images'        => '',
		'newsletter_note'         => __( 'النشرة البريدية — مقال واحد شهريًا', 'noor-photography' ),
		'header_style'            => 'transparent',
		'currency_symbol'         => __( 'ر.س', 'noor-photography' ),
	);
}

function noor_get_option( $key ) {
	$options = wp_parse_args( get_option( NOOR_OPTION, array() ), noor_default_options() );
	return isset( $options[ $key ] ) ? $options[ $key ] : '';
}

function noor_register_options() {
	register_setting( 'noor_options_group', NOOR_OPTION, 'noor_sanitize_options' );
}
add_action( 'admin_init', 'noor_register_options' );

function noor_sanitize_options( $input ) {
	$output      = noor_default_options();
	$text_fields = array( 'brand_name_ar', 'brand_name_en', 'brand_tagline', 'contact_email', 'contact_phone', 'contact_address', 'contact_hours', 'instagram_handle', 'currency_symbol' );
	foreach ( $text_fields as $field ) {
		$output[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
	}
	$textarea_fields = array( 'brand_description', 'social_links', 'stats', 'photographer_bio', 'footer_text', 'newsletter_note' );
	foreach ( $textarea_fields as $field ) {
		$output[ $field ] = isset( $input[ $field ] ) ? sanitize_textarea_field( $input[ $field ] ) : '';
	}
	$output['photographer_name']       = isset( $input['photographer_name'] ) ? sanitize_text_field( $input['photographer_name'] ) : '';
	$output['photographer_experience'] = isset( $input['photographer_experience'] ) ? sanitize_text_field( $input['photographer_experience'] ) : '';
	$output['photographer_awards']     = isset( $input['photographer_awards'] ) ? sanitize_text_field( $input['photographer_awards'] ) : '';
	$output['photographer_image']      = isset( $input['photographer_image'] ) ? absint( $input['photographer_image'] ) : '';
	$output['instagram_images']        = isset( $input['instagram_images'] ) ? implode( ',', array_filter( array_map( 'intval', explode( ',', $input['instagram_images'] ) ) ) ) : '';
	$output['header_style']            = ( isset( $input['header_style'] ) && 'solid' === $input['header_style'] ) ? 'solid' : 'transparent';

	if ( isset( $input['contact_email'] ) && ! is_email( $input['contact_email'] ) ) {
		add_settings_error( NOOR_OPTION, 'contact_email', __( 'البريد الإلكتروني للتواصل غير صحيح.', 'noor-photography' ) );
		$output['contact_email'] = noor_get_option( 'contact_email' );
	}

	return $output;
}

function noor_options_menu() {
	add_menu_page(
		__( 'إعدادات نُور', 'noor-photography' ),
		__( 'إعدادات نُور', 'noor-photography' ),
		'manage_options',
		'noor-options',
		'noor_render_options_page',
		'dashicons-admin-customizer',
		2
	);
}
add_action( 'admin_menu', 'noor_options_menu' );

function noor_render_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$o = wp_parse_args( get_option( NOOR_OPTION, array() ), noor_default_options() );
	?>
	<div class="wrap noor-options-wrap">
		<h1><?php esc_html_e( 'إعدادات نُور — المحتوى العام للموقع', 'noor-photography' ); ?></h1>
		<p><?php esc_html_e( 'هذه الإعدادات تتحكم في المحتوى الظاهر عبر أكثر من صفحة: الفوتر، الترويسة، الصفحة الرئيسية، وصفحة عن الاستوديو.', 'noor-photography' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'noor_options_group' ); ?>

			<h2 class="title"><?php esc_html_e( 'الهوية والعلامة', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="brand_name_ar"><?php esc_html_e( 'اسم العلامة (عربي)', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="brand_name_ar" name="<?php echo esc_attr( NOOR_OPTION ); ?>[brand_name_ar]" value="<?php echo esc_attr( $o['brand_name_ar'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="brand_name_en"><?php esc_html_e( 'اسم العلامة (إنجليزي)', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="brand_name_en" name="<?php echo esc_attr( NOOR_OPTION ); ?>[brand_name_en]" value="<?php echo esc_attr( $o['brand_name_en'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="brand_tagline"><?php esc_html_e( 'الشعار الفرعي', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="brand_tagline" name="<?php echo esc_attr( NOOR_OPTION ); ?>[brand_tagline]" value="<?php echo esc_attr( $o['brand_tagline'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="brand_description"><?php esc_html_e( 'وصف مختصر (يظهر في الفوتر)', 'noor-photography' ); ?></label></th>
					<td><textarea class="large-text" rows="3" id="brand_description" name="<?php echo esc_attr( NOOR_OPTION ); ?>[brand_description]"><?php echo esc_textarea( $o['brand_description'] ); ?></textarea></td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'التواصل', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="contact_email"><?php esc_html_e( 'البريد الإلكتروني', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="email" id="contact_email" name="<?php echo esc_attr( NOOR_OPTION ); ?>[contact_email]" value="<?php echo esc_attr( $o['contact_email'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="contact_phone"><?php esc_html_e( 'رقم الهاتف', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" dir="ltr" type="text" id="contact_phone" name="<?php echo esc_attr( NOOR_OPTION ); ?>[contact_phone]" value="<?php echo esc_attr( $o['contact_phone'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="contact_address"><?php esc_html_e( 'العنوان', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="contact_address" name="<?php echo esc_attr( NOOR_OPTION ); ?>[contact_address]" value="<?php echo esc_attr( $o['contact_address'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="contact_hours"><?php esc_html_e( 'ساعات العمل', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="contact_hours" name="<?php echo esc_attr( NOOR_OPTION ); ?>[contact_hours]" value="<?php echo esc_attr( $o['contact_hours'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="social_links"><?php esc_html_e( 'روابط التواصل الاجتماعي', 'noor-photography' ); ?></label></th>
					<td>
						<textarea class="large-text code" rows="5" id="social_links" name="<?php echo esc_attr( NOOR_OPTION ); ?>[social_links]"><?php echo esc_textarea( $o['social_links'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'سطر لكل رابط، بصيغة: الاسم :: الرابط — مثال: إنستغرام :: https://instagram.com/noor.studio', 'noor-photography' ); ?></p>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'الإحصائيات', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="stats"><?php esc_html_e( 'الأرقام (تظهر في الرئيسية وصفحة عن الاستوديو)', 'noor-photography' ); ?></label></th>
					<td>
						<textarea class="large-text code" rows="5" id="stats" name="<?php echo esc_attr( NOOR_OPTION ); ?>[stats]"><?php echo esc_textarea( $o['stats'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'سطر لكل رقم، بصيغة: القيمة :: اللاحقة :: التسمية — مثال: 480 :: + :: مشروع مُنجز', 'noor-photography' ); ?></p>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'المصوّر / عن الاستوديو', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="photographer_name"><?php esc_html_e( 'الاسم', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="photographer_name" name="<?php echo esc_attr( NOOR_OPTION ); ?>[photographer_name]" value="<?php echo esc_attr( $o['photographer_name'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="photographer_bio"><?php esc_html_e( 'نبذة (تُستخدم في الصفحة الرئيسية)', 'noor-photography' ); ?></label></th>
					<td><textarea class="large-text" rows="4" id="photographer_bio" name="<?php echo esc_attr( NOOR_OPTION ); ?>[photographer_bio]"><?php echo esc_textarea( $o['photographer_bio'] ); ?></textarea></td>
				</tr>
				<tr>
					<th><label for="photographer_experience"><?php esc_html_e( 'سنوات الخبرة (نص قصير)', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="photographer_experience" name="<?php echo esc_attr( NOOR_OPTION ); ?>[photographer_experience]" value="<?php echo esc_attr( $o['photographer_experience'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label for="photographer_awards"><?php esc_html_e( 'عدد الجوائز (نص قصير)', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="photographer_awards" name="<?php echo esc_attr( NOOR_OPTION ); ?>[photographer_awards]" value="<?php echo esc_attr( $o['photographer_awards'] ); ?>" /></td>
				</tr>
				<tr>
					<th><label><?php esc_html_e( 'صورة المصوّر', 'noor-photography' ); ?></label></th>
					<td><div class="noor-single-image-field" data-target="photographer_image">
							<input type="hidden" id="photographer_image" name="<?php echo esc_attr( NOOR_OPTION ); ?>[photographer_image]" value="<?php echo esc_attr( $o['photographer_image'] ); ?>" />
							<div class="noor-gallery-preview">
								<?php if ( $o['photographer_image'] ) : ?>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $o['photographer_image'], 'thumbnail' ) ); ?>" />
								<?php endif; ?>
							</div>
							<button type="button" class="button noor-single-image-select"><?php esc_html_e( 'اختيار صورة', 'noor-photography' ); ?></button>
						</div>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'إنستغرام', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="instagram_handle"><?php esc_html_e( 'المعرّف', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" dir="ltr" type="text" id="instagram_handle" name="<?php echo esc_attr( NOOR_OPTION ); ?>[instagram_handle]" value="<?php echo esc_attr( $o['instagram_handle'] ); ?>" placeholder="@noor.studio" /></td>
				</tr>
				<tr>
					<th><label><?php esc_html_e( 'شبكة الصور', 'noor-photography' ); ?></label></th>
					<td>
						<div class="noor-gallery-field" data-target="instagram_images">
							<input type="hidden" id="instagram_images" name="<?php echo esc_attr( NOOR_OPTION ); ?>[instagram_images]" value="<?php echo esc_attr( $o['instagram_images'] ); ?>" />
							<div class="noor-gallery-preview">
								<?php foreach ( array_filter( explode( ',', $o['instagram_images'] ) ) as $iid ) : ?>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $iid, 'thumbnail' ) ); ?>" />
								<?php endforeach; ?>
							</div>
							<button type="button" class="button noor-gallery-select"><?php esc_html_e( 'اختيار الصور', 'noor-photography' ); ?></button>
							<button type="button" class="button noor-gallery-clear"><?php esc_html_e( 'إفراغ', 'noor-photography' ); ?></button>
						</div>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'التخطيط', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="header_style"><?php esc_html_e( 'نمط الترويسة على الصفحة الرئيسية', 'noor-photography' ); ?></label></th>
					<td>
						<select id="header_style" name="<?php echo esc_attr( NOOR_OPTION ); ?>[header_style]">
							<option value="transparent" <?php selected( $o['header_style'], 'transparent' ); ?>><?php esc_html_e( 'شفافة فوق الغلاف ثم صلبة عند التمرير (افتراضي)', 'noor-photography' ); ?></option>
							<option value="solid" <?php selected( $o['header_style'], 'solid' ); ?>><?php esc_html_e( 'صلبة دائمًا', 'noor-photography' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'يُطبَّق فقط على الصفحة الرئيسية — بقية الصفحات تستخدم دائمًا الترويسة الصلبة.', 'noor-photography' ); ?></p>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'الأسعار', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="currency_symbol"><?php esc_html_e( 'رمز العملة', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="currency_symbol" name="<?php echo esc_attr( NOOR_OPTION ); ?>[currency_symbol]" value="<?php echo esc_attr( $o['currency_symbol'] ); ?>" />
					<p class="description"><?php esc_html_e( 'يظهر هذا الرمز بجانب أسعار الباقات في صفحتي الباقات والحجز.', 'noor-photography' ); ?></p></td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'الفوتر', 'noor-photography' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="footer_text"><?php esc_html_e( 'نص حقوق النشر', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="footer_text" name="<?php echo esc_attr( NOOR_OPTION ); ?>[footer_text]" value="<?php echo esc_attr( $o['footer_text'] ); ?>" />
					<p class="description"><?php esc_html_e( 'استخدم %year% لإدراج السنة الحالية تلقائيًا.', 'noor-photography' ); ?></p></td>
				</tr>
				<tr>
					<th><label for="newsletter_note"><?php esc_html_e( 'نص دعوة الاشتراك بالنشرة', 'noor-photography' ); ?></label></th>
					<td><input class="regular-text" type="text" id="newsletter_note" name="<?php echo esc_attr( NOOR_OPTION ); ?>[newsletter_note]" value="<?php echo esc_attr( $o['newsletter_note'] ); ?>" /></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>

		<hr />
		<?php do_action( 'noor_options_page_bottom' ); ?>
	</div>
	<?php
}

/* -------- parsing helpers for the "::" line-based mini repeaters -------- */

function noor_parse_lines( $raw ) {
	$lines = array_filter( array_map( 'trim', explode( "\n", (string) $raw ) ) );
	return array_values( $lines );
}

function noor_parse_pairs( $raw ) {
	$pairs = array();
	foreach ( noor_parse_lines( $raw ) as $line ) {
		$parts = explode( '::', $line, 2 );
		if ( count( $parts ) === 2 ) {
			$pairs[] = array( trim( $parts[0] ), trim( $parts[1] ) );
		}
	}
	return $pairs;
}

function noor_parse_triples( $raw ) {
	$triples = array();
	foreach ( noor_parse_lines( $raw ) as $line ) {
		$parts = array_map( 'trim', explode( '::', $line ) );
		if ( count( $parts ) >= 3 ) {
			$triples[] = array( $parts[0], $parts[1], $parts[2] );
		}
	}
	return $triples;
}

function noor_get_stats() {
	$out = array();
	foreach ( noor_parse_triples( noor_get_option( 'stats' ) ) as $t ) {
		$out[] = array(
			'value'  => (int) preg_replace( '/[^0-9]/', '', $t[0] ),
			'suffix' => $t[1],
			'label'  => $t[2],
		);
	}
	return $out;
}

function noor_get_social_links() {
	$out = array();
	foreach ( noor_parse_pairs( noor_get_option( 'social_links' ) ) as $p ) {
		$out[] = array(
			'label' => $p[0],
			'url'   => esc_url_raw( $p[1] ),
		);
	}
	return $out;
}

/**
 * Build a real Instagram profile URL from the `instagram_handle` option.
 * Accepts a plain handle ("noor.studio" or "@noor.studio") or a full URL
 * already pasted into the field — either way returns one valid URL, or an
 * empty string when no handle is configured.
 */
function noor_get_instagram_url() {
	$handle = trim( (string) noor_get_option( 'instagram_handle' ) );
	if ( '' === $handle ) {
		return '';
	}
	if ( preg_match( '#^https?://#i', $handle ) ) {
		return esc_url_raw( $handle );
	}
	$handle = ltrim( $handle, '@' );
	if ( '' === $handle ) {
		return '';
	}
	return esc_url_raw( 'https://instagram.com/' . rawurlencode( $handle ) );
}
