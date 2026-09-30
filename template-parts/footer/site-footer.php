<?php
/**
 * Site footer: brand, site nav, services list, contact + newsletter.
 * All content pulled from Theme Options / CPTs — nothing hardcoded.
 */
$services = get_posts(
	array(
		'post_type'      => 'service',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>
<footer class="site-footer">
	<div class="shell footer-grid">
		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="site-logo__mark" aria-hidden="true"></span>
					<span class="site-logo__ar"><?php echo esc_html( noor_get_option( 'brand_name_ar' ) ?: get_bloginfo( 'name' ) ); ?></span>
				<?php endif; ?>
			</a>
			<?php if ( noor_get_option( 'brand_tagline' ) ) : ?>
				<p class="t-eyebrow" style="margin-top:1rem"><?php echo esc_html( noor_get_option( 'brand_tagline' ) ); ?></p>
			<?php endif; ?>
			<?php if ( noor_get_option( 'brand_description' ) ) : ?>
				<p class="t-lead"><?php echo esc_html( noor_get_option( 'brand_description' ) ); ?></p>
			<?php endif; ?>

			<?php $socials = noor_get_social_links(); ?>
			<?php if ( $socials ) : ?>
				<ul class="footer-socials">
					<?php foreach ( $socials as $social ) : ?>
						<li><a class="link-underline" href="<?php echo esc_url( $social['url'] ); ?>"><?php echo esc_html( $social['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav aria-label="<?php esc_attr_e( 'روابط الموقع', 'noor-photography' ); ?>">
			<h2 class="t-eyebrow"><?php esc_html_e( 'الموقع', 'noor-photography' ); ?></h2>
			<ul>
				<li><a class="link-underline" href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>"><?php esc_html_e( 'الأعمال', 'noor-photography' ); ?></a></li>
				<li><a class="link-underline" href="<?php echo esc_url( noor_get_about_url() ); ?>"><?php esc_html_e( 'عن الاستوديو', 'noor-photography' ); ?></a></li>
				<li><a class="link-underline" href="<?php echo esc_url( noor_get_pricing_url() ); ?>"><?php esc_html_e( 'الباقات', 'noor-photography' ); ?></a></li>
				<li><a class="link-underline" href="<?php echo esc_url( noor_get_contact_url() ); ?>"><?php esc_html_e( 'تواصل', 'noor-photography' ); ?></a></li>
			</ul>
		</nav>

		<?php if ( $services ) : ?>
		<nav aria-label="<?php esc_attr_e( 'الخدمات', 'noor-photography' ); ?>">
			<h2 class="t-eyebrow"><?php esc_html_e( 'الخدمات', 'noor-photography' ); ?></h2>
			<ul>
				<?php foreach ( $services as $social ) : ?>
					<li><a class="link-underline" href="<?php echo esc_url( get_permalink( $social ) ); ?>"><?php echo esc_html( get_the_title( $social ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<?php endif; ?>

		<div>
			<h2 class="t-eyebrow"><?php esc_html_e( 'تواصل', 'noor-photography' ); ?></h2>
			<address class="footer-address">
				<?php
				if ( noor_get_option( 'contact_address' ) ) :
					?>
					<p><?php echo esc_html( noor_get_option( 'contact_address' ) ); ?></p><?php endif; ?>
				<?php
				if ( noor_get_option( 'contact_phone' ) ) :
					?>
					<p dir="ltr"><?php echo esc_html( noor_get_option( 'contact_phone' ) ); ?></p><?php endif; ?>
				<?php
				if ( noor_get_option( 'contact_email' ) ) :
					?>
					<p><?php echo esc_html( noor_get_option( 'contact_email' ) ); ?></p><?php endif; ?>
				<?php
				if ( noor_get_option( 'contact_hours' ) ) :
					?>
					<p><?php echo esc_html( noor_get_option( 'contact_hours' ) ); ?></p><?php endif; ?>
			</address>

			<form class="footer-newsletter" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="noor_newsletter" />
				<?php wp_nonce_field( 'noor_newsletter_form', 'noor_newsletter_nonce' ); ?>
				<div class="honeypot-field"><label>Website<input type="text" name="noor_website" tabindex="-1" autocomplete="off" /></label></div>
				<label for="newsletter" class="t-caption"><?php echo esc_html( noor_get_option( 'newsletter_note' ) ); ?></label>
				<div class="field-row">
					<input id="newsletter" name="newsletter_email" type="email" required placeholder="<?php esc_attr_e( 'بريدك الإلكتروني', 'noor-photography' ); ?>" class="field" />
					<button type="submit" class="btn btn-solid btn-sm"><?php esc_html_e( 'اشترك', 'noor-photography' ); ?></button>
				</div>
				<?php
				// Read-only display flag set by inc/forms.php's own redirect after
				// it already verified the form's nonce server-side — nothing is
				// written based on this value, it only toggles a static message.
				$noor_newsletter_status = isset( $_GET['noor_newsletter'] ) ? sanitize_key( $_GET['noor_newsletter'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( 'sent' === $noor_newsletter_status ) :
					?>
					<p role="status" style="margin-top:0.75rem;color:var(--success)"><?php esc_html_e( 'تم الاشتراك، شكرًا لك.', 'noor-photography' ); ?></p>
				<?php endif; ?>
			</form>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="shell footer-bottom__row">
			<p><?php echo esc_html( str_replace( '%year%', gmdate( 'Y' ), noor_get_option( 'footer_text' ) ) ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</div>
</footer>
