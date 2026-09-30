<?php
/**
 * Native WordPress Customizer controls for the theme's visual design
 * tokens (colors, typography). Deliberately NOT a third-party options
 * framework — every control here maps 1:1 onto a CSS custom property
 * already defined in assets/css/main.css, and every default exactly
 * matches the current shipped design so activating the theme (or never
 * touching these controls) looks identical to before this file existed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated, Arabic-script-safe Google Font choices. Kept short on purpose —
 * this is meant to be a handful of tasteful, on-brand options, not a
 * general-purpose font picker.
 */
function noor_heading_font_choices() {
	return array(
		'amiri'      => array(
			'label'  => __( 'أميري (الخط الافتراضي)', 'noor-photography' ),
			'google' => 'Amiri:wght@400;700',
			'css'    => '"Amiri", "Times New Roman", serif',
		),
		'aref_ruqaa' => array(
			'label'  => __( 'عارف رقعة', 'noor-photography' ),
			'google' => 'Aref Ruqaa:wght@400;700',
			'css'    => '"Aref Ruqaa", "Amiri", serif',
		),
		'markazi'    => array(
			'label'  => __( 'مركزي تكست', 'noor-photography' ),
			'google' => 'Markazi Text:wght@400;700',
			'css'    => '"Markazi Text", "Amiri", serif',
		),
		'lateef'     => array(
			'label'  => __( 'لطيف', 'noor-photography' ),
			'google' => 'Lateef:wght@400;700',
			'css'    => '"Lateef", "Amiri", serif',
		),
	);
}

function noor_body_font_choices() {
	return array(
		'ibm_plex' => array(
			'label'  => __( 'IBM Plex Sans Arabic (الخط الافتراضي)', 'noor-photography' ),
			'google' => 'IBM+Plex+Sans+Arabic:wght@300;400;500;600',
			'css'    => '"IBM Plex Sans Arabic", system-ui, sans-serif',
		),
		'cairo'    => array(
			'label'  => __( 'القاهرة (Cairo)', 'noor-photography' ),
			'google' => 'Cairo:wght@300;400;500;600',
			'css'    => '"Cairo", system-ui, sans-serif',
		),
		'tajawal'  => array(
			'label'  => __( 'تجوال (Tajawal)', 'noor-photography' ),
			'google' => 'Tajawal:wght@300;400;500;700',
			'css'    => '"Tajawal", system-ui, sans-serif',
		),
		'almarai'  => array(
			'label'  => __( 'المراعي (Almarai)', 'noor-photography' ),
			'google' => 'Almarai:wght@300;400;700',
			'css'    => '"Almarai", system-ui, sans-serif',
		),
	);
}

function noor_font_size_choices() {
	return array(
		'small'  => array(
			'label' => __( 'صغير', 'noor-photography' ),
			'value' => '15px',
		),
		'medium' => array(
			'label' => __( 'متوسط (افتراضي)', 'noor-photography' ),
			'value' => '16px',
		),
		'large'  => array(
			'label' => __( 'كبير', 'noor-photography' ),
			'value' => '17.5px',
		),
	);
}

/**
 * Single source of truth for every customizer-controlled design token:
 * theme_mod key => [ default, matching CSS custom property ]. Colors use
 * the same hex approximations already established for the block editor
 * palette (inc/block-editor.php) so the whole product is visually
 * consistent, not just the frontend.
 */
/*
 * Precise sRGB conversions of the design's actual oklch() tokens (measured
 * via canvas pixel sampling against the live reference site, not
 * hand-eyeballed) — verified against real WCAG contrast math. An earlier
 * hand-picked approximation for --muted-foreground (#8a8479) was measurably
 * too light: 3.41:1 against the background, failing WCAG AA's 4.5:1
 * threshold for small text (t-eyebrow/t-caption use it everywhere). The
 * corrected value (#6e6862) is the *exact* color the original design
 * already renders at 5.15:1 — this is a precision fix, not a design change.
 */
function noor_customizer_color_defaults() {
	return array(
		'noor_color_primary'    => array( '#110f0d', '--foreground' ),
		'noor_color_accent'     => array( '#ab947b', '--accent' ),
		'noor_color_background' => array( '#faf7f3', '--background' ),
		'noor_color_surface'    => array( '#f2f0ea', '--surface' ),
		'noor_color_muted'      => array( '#6e6862', '--muted-foreground' ),
	);
}

function noor_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'noor_design',
		array(
			'title'       => __( 'نُور — التصميم', 'noor-photography' ),
			'description' => __( 'الألوان والخطوط الأساسية للثيم. القيم الافتراضية هي تصميم نُور الأصلي — أي تغيير هنا يُطبَّق فورًا على كامل الموقع.', 'noor-photography' ),
			'priority'    => 30,
		)
	);

	/* -------- colors -------- */

	$wp_customize->add_section(
		'noor_colors',
		array(
			'title' => __( 'الألوان', 'noor-photography' ),
			'panel' => 'noor_design',
		)
	);

	$color_labels = array(
		'noor_color_primary'    => __( 'اللون الأساسي (النص وخلفية الأزرار)', 'noor-photography' ),
		'noor_color_accent'     => __( 'لون التمييز (التفاعل عند التحويم)', 'noor-photography' ),
		'noor_color_background' => __( 'لون الخلفية', 'noor-photography' ),
		'noor_color_surface'    => __( 'لون الخلفية الثانوية (الأقسام البديلة)', 'noor-photography' ),
		'noor_color_muted'      => __( 'لون النص الثانوي (التسميات والتفاصيل)', 'noor-photography' ),
	);

	$priority = 10;
	foreach ( noor_customizer_color_defaults() as $key => $meta ) {
		list( $default, $css_var ) = $meta;
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $default,
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$key,
				array(
					'label'    => $color_labels[ $key ],
					'section'  => 'noor_colors',
					'priority' => $priority,
				)
			)
		);
		$priority += 10;
	}

	/* -------- typography -------- */

	$wp_customize->add_section(
		'noor_typography',
		array(
			'title' => __( 'الطباعة', 'noor-photography' ),
			'panel' => 'noor_design',
		)
	);

	$heading_choices = wp_list_pluck( noor_heading_font_choices(), 'label' );
	$wp_customize->add_setting(
		'noor_font_heading',
		array(
			'default'           => 'amiri',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'noor_font_heading',
		array(
			'label'    => __( 'خط العناوين', 'noor-photography' ),
			'section'  => 'noor_typography',
			'type'     => 'select',
			'choices'  => $heading_choices,
			'priority' => 10,
		)
	);

	$body_choices = wp_list_pluck( noor_body_font_choices(), 'label' );
	$wp_customize->add_setting(
		'noor_font_body',
		array(
			'default'           => 'ibm_plex',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'noor_font_body',
		array(
			'label'    => __( 'خط النصوص', 'noor-photography' ),
			'section'  => 'noor_typography',
			'type'     => 'select',
			'choices'  => $body_choices,
			'priority' => 20,
		)
	);

	$size_choices = wp_list_pluck( noor_font_size_choices(), 'label' );
	$wp_customize->add_setting(
		'noor_font_size',
		array(
			'default'           => 'medium',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'noor_font_size',
		array(
			'label'    => __( 'الحجم الأساسي لنص الموقع', 'noor-photography' ),
			'section'  => 'noor_typography',
			'type'     => 'select',
			'choices'  => $size_choices,
			'priority' => 30,
		)
	);
}
add_action( 'customize_register', 'noor_customize_register' );

/**
 * Build the inline <style> block overriding the design tokens defined in
 * assets/css/main.css. Shared between the live frontend (wp_head) and the
 * Customizer preview iframe's initial paint, so both start from the same
 * source of truth before postMessage takes over for instant preview.
 */
function noor_customizer_css() {
	/*
	 * Color overrides must ONLY apply in light mode. assets/css/main.css
	 * defines the dark palette on a `.dark` selector — same specificity as
	 * a bare `:root`, so an unscoped `:root { --background: ... }` here
	 * would win the cascade (it loads after main.css) and silently lock
	 * every page into light-mode colors even with dark mode toggled on,
	 * producing invisible text (dark-mode text color + light-mode
	 * background). Scoping to `:root:not(.dark)` lets `.dark` win when
	 * active, so dark mode keeps using the theme's own built-in palette —
	 * these Customizer controls are specifically for the light theme.
	 */
	$css = ":root:not(.dark) {\n";
	foreach ( noor_customizer_color_defaults() as $key => $meta ) {
		list( $default, $css_var ) = $meta;
		$value                     = get_theme_mod( $key, $default );
		$css                      .= "\t{$css_var}: {$value};\n";
	}
	$css .= "}\n";

	// Typography applies in both modes — a separate, unscoped block.
	$css        .= ":root {\n";
	$heading_key = get_theme_mod( 'noor_font_heading', 'amiri' );
	$headings    = noor_heading_font_choices();
	if ( isset( $headings[ $heading_key ] ) ) {
		$css .= "\t--font-display: " . $headings[ $heading_key ]['css'] . ";\n";
	}

	$body_key = get_theme_mod( 'noor_font_body', 'ibm_plex' );
	$bodies   = noor_body_font_choices();
	if ( isset( $bodies[ $body_key ] ) ) {
		$css .= "\t--font-body: " . $bodies[ $body_key ]['css'] . ";\n";
	}

	$css .= "}\n";

	$size_key = get_theme_mod( 'noor_font_size', 'medium' );
	$sizes    = noor_font_size_choices();
	if ( isset( $sizes[ $size_key ] ) ) {
		$css .= 'body { font-size: ' . $sizes[ $size_key ]['value'] . "; }\n";
	}

	return $css;
}

function noor_output_customizer_css() {
	echo '<style id="noor-customizer-vars">' . noor_customizer_css() . '</style>' . "\n"; // phpcs:ignore -- built entirely from a fixed allow-list of hex colors/font-family strings above, nothing user-supplied reaches here unescaped.
}
add_action( 'wp_head', 'noor_output_customizer_css', 20 );

/**
 * The Google Fonts <link> in inc/enqueue.php must request whichever
 * families are actually selected — falling back to the same two families
 * (Amiri + IBM Plex Sans Arabic) it always requested when nothing has been
 * customized, so a default install's network request is unchanged.
 */
function noor_google_fonts_url() {
	$heading_key = get_theme_mod( 'noor_font_heading', 'amiri' );
	$body_key    = get_theme_mod( 'noor_font_body', 'ibm_plex' );
	$headings    = noor_heading_font_choices();
	$bodies      = noor_body_font_choices();

	$families = array();
	if ( isset( $headings[ $heading_key ] ) ) {
		$families[] = $headings[ $heading_key ]['google'];
	}
	if ( isset( $bodies[ $body_key ] ) && $body_key !== $heading_key ) {
		$families[] = $bodies[ $body_key ]['google'];
	}
	if ( empty( $families ) ) {
		$families[] = $headings['amiri']['google'];
	}

	/*
	 * Google's css2 API requires ":", "@" and ";" to stay literal in the
	 * "family" query value (they're part of its own family:axis@values
	 * syntax, not URL-reserved data to escape) — only the space inside a
	 * multi-word family name needs encoding, as "+". rawurlencode()-ing the
	 * whole string (as an earlier version of this function did) turns
	 * "Amiri:wght@400;700" into "Amiri%3Awght%40400%3B700", which Google
	 * doesn't recognize; the request 400s, zero @font-face rules load, and
	 * every page silently falls back to Times New Roman / system-ui.
	 */
	$safe_families = array_map(
		function ( $family ) {
			return str_replace( ' ', '+', $family );
		},
		$families
	);

	return 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $safe_families ) . '&display=swap';
}

/**
 * Live preview: only loaded inside the Customizer's preview iframe, binds
 * to every postMessage-transport setting above and rewrites the same
 * #noor-customizer-vars tag instantly, no full page reload. Font changes
 * also swap the Google Fonts <link> so a newly picked family actually
 * loads without saving first.
 */
function noor_customize_preview_js() {
	wp_enqueue_script( 'noor-customizer-preview', NOOR_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), NOOR_VERSION, true );
	wp_localize_script(
		'noor-customizer-preview',
		'noorCustomizerData',
		array(
			'colorVars'  => wp_list_pluck( noor_customizer_color_defaults(), 1 ),
			'headingCss' => wp_list_pluck( noor_heading_font_choices(), 'css' ),
			'bodyCss'    => wp_list_pluck( noor_body_font_choices(), 'css' ),
			'headingUrl' => wp_list_pluck( noor_heading_font_choices(), 'google' ),
			'bodyUrl'    => wp_list_pluck( noor_body_font_choices(), 'google' ),
			'fontSizes'  => wp_list_pluck( noor_font_size_choices(), 'value' ),
		)
	);
}
add_action( 'customize_preview_init', 'noor_customize_preview_js' );
