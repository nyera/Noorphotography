<?php
/**
 * Site header — transparent-over-hero on the front page (default), solid
 * elsewhere. "دائمًا صلبة" in Theme Settings disables the transparent
 * variant everywhere, for admins who prefer a consistent header even over
 * the homepage hero.
 */
$transparent = is_front_page() && 'solid' !== noor_get_option( 'header_style' );
?>
<header class="site-header <?php echo $transparent ? 'is-transparent' : 'is-solid'; ?>" data-transparent="<?php echo $transparent ? '1' : '0'; ?>">
	<div class="shell site-header__row">
		<?php
		/*
		 * aria-label only when a logo IMAGE renders (no visible text there,
		 * so a descriptive label is genuinely needed). When the text
		 * wordmark fallback renders instead, its own visible text already
		 * serves as the accessible name — adding an aria-label on top of it
		 * would need to repeat that exact text or it fails WCAG 2.5.3
		 * (Label in Name); simplest correct fix is to just not add one.
		 */
		$noor_logo_label = has_custom_logo() ? __( 'الصفحة الرئيسية', 'noor-photography' ) : '';
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" <?php echo $noor_logo_label ? 'aria-label="' . esc_attr( $noor_logo_label ) . '"' : ''; ?>>
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="site-logo__mark" aria-hidden="true"></span>
				<span class="site-logo__ar"><?php echo esc_html( noor_get_option( 'brand_name_ar' ) ?: get_bloginfo( 'name' ) ); ?></span>
				<?php if ( noor_get_option( 'brand_name_en' ) ) : ?>
					<span class="site-logo__en"><?php echo esc_html( noor_get_option( 'brand_name_en' ) ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</a>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'التنقّل الرئيسي', 'noor-photography' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<button type="button" data-search-open><?php esc_html_e( 'بحث', 'noor-photography' ); ?></button>
			<span class="header-divider" aria-hidden="true"></span>
			<button type="button" data-theme-toggle data-label-dark="<?php esc_attr_e( 'داكن', 'noor-photography' ); ?>" data-label-light="<?php esc_attr_e( 'فاتح', 'noor-photography' ); ?>"><?php esc_html_e( 'داكن', 'noor-photography' ); ?></button>
			<span class="header-divider" aria-hidden="true"></span>
			<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn <?php echo $transparent ? 'btn-ghost-light' : 'btn-solid'; ?>"><?php esc_html_e( 'احجز جلستك', 'noor-photography' ); ?></a>
			<button type="button" class="menu-toggle" aria-label="<?php esc_attr_e( 'فتح القائمة', 'noor-photography' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
				<span></span>
				<span></span>
			</button>
		</div>
	</div>
</header>
