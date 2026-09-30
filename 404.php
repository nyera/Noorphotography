<?php
/**
 * 404 error page.
 */
get_header();
?>
<main class="error-page">
	<?php
	$ph_image = (int) noor_get_option( 'photographer_image' );
	if ( $ph_image ) {
		echo wp_get_attachment_image(
			$ph_image,
			'noor-hero',
			false,
			array(
				'class'       => 'error-page__bg',
				'alt'         => '',
				'aria-hidden' => 'true',
			)
		);
	}
	?>
	<div class="shell error-page__content">
		<p class="t-eyebrow"><?php esc_html_e( 'خطأ ٤٠٤', 'noor-photography' ); ?></p>
		<h1 class="t-display"><?php esc_html_e( 'يبدو أن هذه الصورة ضاعت في الطريق.', 'noor-photography' ); ?></h1>
		<p class="t-lead"><?php esc_html_e( 'الصفحة التي تبحث عنها غير موجودة أو تم نقلها. يمكنك العودة إلى الرئيسية أو تصفّح الأعمال.', 'noor-photography' ); ?></p>
		<div class="error-page__actions">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-solid"><?php esc_html_e( 'العودة للرئيسية', 'noor-photography' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>" class="btn btn-outline"><?php esc_html_e( 'تصفّح الأعمال', 'noor-photography' ); ?></a>
		</div>
	</div>
</main>
<?php get_footer(); ?>
