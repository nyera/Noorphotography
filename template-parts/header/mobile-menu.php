<?php
/**
 * Fullscreen mobile navigation drawer.
 */
?>
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'قائمة التنقّل', 'noor-photography' ); ?>">
	<div class="shell mobile-menu__inner">
		<div class="mobile-menu__top">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
				<span class="site-logo__mark" aria-hidden="true"></span>
				<span class="site-logo__ar"><?php echo esc_html( noor_get_option( 'brand_name_ar' ) ?: get_bloginfo( 'name' ) ); ?></span>
			</a>
			<button type="button" class="mobile-menu__close"><?php esc_html_e( 'إغلاق', 'noor-photography' ); ?></button>
		</div>

		<nav aria-label="<?php esc_attr_e( 'قائمة الجوال', 'noor-photography' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'fallback_cb'    => false,
					'walker'         => new Noor_Mobile_Menu_Walker(),
				)
			);
			?>
		</nav>

		<div class="mobile-menu__footer">
			<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid-light btn-block"><?php esc_html_e( 'احجز جلستك', 'noor-photography' ); ?></a>
			<p class="t-caption" style="color: rgba(255,255,255,0.6)">
				<?php echo esc_html( noor_get_option( 'contact_email' ) ); ?> · <span dir="ltr"><?php echo esc_html( noor_get_option( 'contact_phone' ) ); ?></span>
			</p>
		</div>
	</div>
</div>
