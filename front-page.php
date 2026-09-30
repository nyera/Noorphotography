<?php
/**
 * Homepage. Content is assembled from: the static front page (hero,
 * manifesto, CTA), the Portfolio/Service/Testimonial CPTs, and Theme Options
 * (stats, photographer bio, instagram grid). Nothing below is hardcoded copy.
 *
 * Sections are rendered into buffers keyed by section id, then assembled in
 * the admin-configurable order (noor_get_home_section_order()) — each
 * section's own markup/visibility logic is untouched by this; only the
 * final assembly order changes.
 */
get_header();

$front_id      = (int) get_option( 'page_on_front' );
$hero_title    = $front_id ? get_the_title( $front_id ) : get_bloginfo( 'name' );
$hero_desc     = $front_id ? apply_filters( 'the_content', get_post_field( 'post_content', $front_id ) ) : '';
$hero_eyebrow  = $front_id ? get_post_meta( $front_id, '_noor_hero_eyebrow', true ) : '';
$hero_caption  = $front_id ? get_post_meta( $front_id, '_noor_hero_caption', true ) : '';
$manifesto     = $front_id ? get_post_meta( $front_id, '_noor_manifesto', true ) : '';
$cta_heading   = $front_id ? get_post_meta( $front_id, '_noor_cta_heading', true ) : '';
$cta_image_id  = $front_id ? (int) get_post_meta( $front_id, '_noor_cta_image', true ) : 0;
$hero_image_id = $front_id ? get_post_thumbnail_id( $front_id ) : 0;

$categories        = get_terms(
	array(
		'taxonomy'   => 'portfolio_category',
		'hide_empty' => true,
	)
);
$featured_projects = noor_get_featured_or_latest( 'portfolio', 9 );
$services          = get_posts(
	array(
		'post_type'      => 'service',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
$testimonials      = noor_get_featured_or_latest( 'testimonial', 3, array( 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$stats             = noor_get_stats();
$instagram_ids     = array_filter( array_map( 'intval', explode( ',', noor_get_option( 'instagram_images' ) ) ) );

$noor_home_sections = array();

ob_start();
if ( ( $hero_image_id || $hero_title ) && noor_section_visible( $front_id, 'hero' ) ) :
	?>
	<section class="grain hero">
		<?php if ( $hero_image_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$hero_image_id,
				'noor-hero',
				false,
				array(
					'class' => 'hero__media',
					'alt'   => get_post_meta( $hero_image_id, '_wp_attachment_image_alt', true ) ?: $hero_title,
				)
			);
			?>
		<?php endif; ?>
		<div class="hero__scrim"></div>
		<div class="shell hero__content">
			<?php
			if ( $hero_eyebrow ) :
				?>
				<p class="t-eyebrow animate-fade-up" style="color:rgba(255,255,255,0.7)"><?php echo esc_html( $hero_eyebrow ); ?></p><?php endif; ?>
			<h1 class="t-display animate-fade-up" style="animation-delay:120ms"><?php echo esc_html( $hero_title ); ?></h1>
			<?php if ( $hero_desc ) : ?>
				<div class="lead animate-fade-up" style="animation-delay:240ms"><?php echo wp_kses_post( $hero_desc ); ?></div>
			<?php endif; ?>
			<div class="hero__actions animate-fade-up" style="animation-delay:360ms">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>" class="btn btn-solid-light"><?php esc_html_e( 'شاهد الأعمال', 'noor-photography' ); ?></a>
				<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-ghost-light"><?php esc_html_e( 'احجز جلستك', 'noor-photography' ); ?></a>
			</div>
			<?php if ( $hero_caption ) : ?>
			<div class="hero__meta">
				<button type="button" class="js-scroll-down"><?php esc_html_e( 'تصفّح للأسفل', 'noor-photography' ); ?></button>
				<p><?php echo esc_html( $hero_caption ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['hero'] = ob_get_clean();

ob_start();
if ( $manifesto && noor_section_visible( $front_id, 'manifesto' ) ) :
	?>
	<section class="section hairline">
		<div class="shell manifesto-grid">
			<div class="reveal col-eyebrow"><p class="t-eyebrow"><?php esc_html_e( 'فلسفتنا', 'noor-photography' ); ?></p></div>
			<div class="reveal col-text" style="transition-delay:80ms">
				<p><?php echo esc_html( $manifesto ); ?></p>
				<a href="<?php echo esc_url( noor_get_about_url() ); ?>" class="link-underline" style="margin-top:2rem;display:inline-block;font-size:0.9rem;letter-spacing:0.05em"><?php esc_html_e( 'تعرّف على الاستوديو', 'noor-photography' ); ?></a>
			</div>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['manifesto'] = ob_get_clean();

ob_start();
if ( $featured_projects && noor_section_visible( $front_id, 'portfolio' ) ) :
	?>
	<section class="section">
		<div class="shell">
			<div class="section-head" style="margin-bottom:3rem;display:flex;flex-direction:column;gap:1.5rem">
				<div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-end;gap:1.5rem">
					<div style="max-width:38rem">
						<p class="t-eyebrow"><?php esc_html_e( 'أعمال مختارة', 'noor-photography' ); ?></p>
						<h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'مشاريع من الأرشيف', 'noor-photography' ); ?></h2>
					</div>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>" class="btn btn-outline"><?php esc_html_e( 'كل الأعمال', 'noor-photography' ); ?></a>
				</div>
			</div>

			<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
			<div class="filter-bar" data-filter-bar role="group" aria-label="<?php esc_attr_e( 'تصفية حسب التصنيف', 'noor-photography' ); ?>">
				<button type="button" class="is-active" data-filter="all" aria-pressed="true"><?php esc_html_e( 'الكل', 'noor-photography' ); ?></button>
				<?php foreach ( $categories as $category ) : ?>
					<button type="button" data-filter="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="false"><?php echo esc_html( $category->name ); ?></button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="masonry">
				<?php foreach ( $featured_projects as $i => $project ) : ?>
					<div class="reveal" style="transition-delay: <?php echo esc_attr( ( $i % 3 ) * 90 ); ?>ms">
						<?php
						get_template_part(
							'template-parts/portfolio/card',
							null,
							array(
								'project'  => $project,
								'priority' => $i < 2,
							)
						);
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['portfolio'] = ob_get_clean();

ob_start();
$ph_name  = noor_get_option( 'photographer_name' );
$ph_bio   = noor_get_option( 'photographer_bio' );
$ph_image = (int) noor_get_option( 'photographer_image' );
if ( ( $ph_name || $ph_bio ) && noor_section_visible( $front_id, 'about' ) ) :
	?>
	<section class="section bg-surface">
		<div class="shell about-preview-grid">
			<div class="reveal figure-col">
				<?php if ( $ph_image ) : ?>
					<?php echo wp_get_attachment_image( $ph_image, 'noor-portrait', false, array( 'alt' => $ph_name ) ); ?>
				<?php endif; ?>
			</div>
			<div class="reveal content-col" style="transition-delay:100ms">
				<p class="t-eyebrow"><?php esc_html_e( 'عن المصوّر', 'noor-photography' ); ?></p>
				<?php
				if ( $ph_name ) :
					?>
					<h2 class="t-h2" style="margin-top:1rem"><?php echo esc_html( $ph_name ); ?></h2><?php endif; ?>
				<?php
				if ( $ph_bio ) :
					?>
					<p class="t-lead" style="margin-top:1.5rem"><?php echo esc_html( $ph_bio ); ?></p><?php endif; ?>
				<?php if ( noor_get_option( 'photographer_experience' ) || noor_get_option( 'photographer_awards' ) ) : ?>
				<dl>
					<?php if ( noor_get_option( 'photographer_experience' ) ) : ?>
					<div><dt class="t-caption"><?php esc_html_e( 'الخبرة', 'noor-photography' ); ?></dt><dd><?php echo esc_html( noor_get_option( 'photographer_experience' ) ); ?></dd></div>
					<?php endif; ?>
					<?php if ( noor_get_option( 'photographer_awards' ) ) : ?>
					<div><dt class="t-caption"><?php esc_html_e( 'التكريمات', 'noor-photography' ); ?></dt><dd><?php echo esc_html( noor_get_option( 'photographer_awards' ) ); ?></dd></div>
					<?php endif; ?>
				</dl>
				<?php endif; ?>
				<a href="<?php echo esc_url( noor_get_about_url() ); ?>" class="btn btn-outline" style="margin-top:2rem"><?php esc_html_e( 'السيرة الكاملة', 'noor-photography' ); ?></a>
			</div>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['about'] = ob_get_clean();

ob_start();
if ( $services && noor_section_visible( $front_id, 'services' ) ) :
	?>
	<section class="section">
		<div class="shell">
			<div style="margin-bottom:3rem;max-width:38rem">
				<p class="t-eyebrow"><?php esc_html_e( 'الخدمات', 'noor-photography' ); ?></p>
				<h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'ما الذي نصوّره؟', 'noor-photography' ); ?></h2>
			</div>
			<ul class="services-list">
				<?php
				foreach ( $services as $i => $service ) :
					$sf = noor_get_service_fields( $service->ID );
					?>
				<li>
					<a href="<?php echo esc_url( get_permalink( $service ) ); ?>">
						<span class="num t-caption"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( get_the_title( $service ) ); ?></h3>
						<p class="t-lead" style="font-size:0.9rem"><?php echo esc_html( get_the_excerpt( $service ) ); ?></p>
						<span class="price t-caption"><?php echo esc_html( $sf['price'] ); ?></span>
						<?php if ( has_post_thumbnail( $service ) ) : ?>
							<span class="thumb"><?php echo get_the_post_thumbnail( $service, 'noor-thumb' ); ?></span>
						<?php endif; ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['services'] = ob_get_clean();

ob_start();
if ( $stats && noor_section_visible( $front_id, 'stats' ) ) :
	?>
	<section class="stats-band bg-ink">
		<div class="shell stats-grid">
			<?php foreach ( $stats as $stat ) : ?>
				<figure class="reveal">
					<p class="value"><span data-counter-to="<?php echo esc_attr( $stat['value'] ); ?>" data-counter-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>">0</span></p>
					<figcaption class="t-caption label"><?php echo esc_html( $stat['label'] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['stats'] = ob_get_clean();

ob_start();
if ( $testimonials && noor_section_visible( $front_id, 'testimonials' ) ) :
	?>
	<section class="section">
		<div class="shell">
			<div class="text-center" style="margin-bottom:3rem">
				<p class="t-eyebrow"><?php esc_html_e( 'آراء العملاء', 'noor-photography' ); ?></p>
				<h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'ماذا قالوا عن التجربة؟', 'noor-photography' ); ?></h2>
			</div>
			<div class="testimonials-grid">
				<?php
				foreach ( $testimonials as $i => $t ) :
					$rating = (int) get_post_meta( $t->ID, '_noor_rating', true ) ?: 5;
					?>
				<figure class="testimonial reveal" style="transition-delay: <?php echo esc_attr( $i * 100 ); ?>ms">
					<p class="rating" aria-label="<?php echo esc_attr( sprintf( '%d من 5', $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', $rating ) ); ?></p>
					<blockquote>«<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>»</blockquote>
					<figcaption>
						<?php if ( has_post_thumbnail( $t ) ) : ?>
							<?php echo get_the_post_thumbnail( $t, 'thumbnail' ); ?>
						<?php endif; ?>
						<span>
							<span class="name"><?php echo esc_html( get_the_title( $t ) ); ?></span>
							<span class="t-caption"><?php echo esc_html( get_post_meta( $t->ID, '_noor_role', true ) ); ?></span>
						</span>
					</figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['testimonials'] = ob_get_clean();

ob_start();
$noor_instagram_url = noor_get_instagram_url();
if ( $instagram_ids && noor_section_visible( $front_id, 'instagram' ) ) :
	?>
	<section class="hairline" style="padding-block:4rem 5rem">
		<div class="shell" style="margin-bottom:2rem;display:flex;align-items:flex-end;justify-content:space-between;gap:1.5rem">
			<div>
				<p class="t-eyebrow"><?php esc_html_e( 'إنستغرام', 'noor-photography' ); ?></p>
				<h2 class="t-h3" style="margin-top:0.75rem"><?php echo esc_html( noor_get_option( 'instagram_handle' ) ); ?></h2>
			</div>
			<?php if ( $noor_instagram_url ) : ?>
				<a href="<?php echo esc_url( $noor_instagram_url ); ?>" class="link-underline" style="font-size:0.9rem"><?php esc_html_e( 'تابعنا', 'noor-photography' ); ?></a>
			<?php endif; ?>
		</div>
		<ul class="instagram-grid">
			<?php foreach ( $instagram_ids as $iid ) : ?>
				<li><?php if ( $noor_instagram_url ) : ?><a href="<?php echo esc_url( $noor_instagram_url ); ?>" aria-label="<?php esc_attr_e( 'صورة إنستغرام', 'noor-photography' ); ?>"><?php endif; ?>
				<?php
				echo wp_get_attachment_image(
					$iid,
					'noor-thumb',
					false,
					array(
						'alt'         => '',
						'aria-hidden' => 'true',
					)
				);
				?>
											<?php if ( $noor_instagram_url ) : ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
endif;
$noor_home_sections['instagram'] = ob_get_clean();

ob_start();
if ( $cta_heading && noor_section_visible( $front_id, 'cta' ) ) :
	?>
	<section class="final-cta">
		<?php if ( $cta_image_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$cta_image_id,
				'noor-hero',
				false,
				array(
					'alt'         => '',
					'aria-hidden' => 'true',
				)
			);
			?>
		<?php endif; ?>
		<div class="final-cta__scrim"></div>
		<div class="shell final-cta__content">
			<p class="t-eyebrow" style="color:rgba(255,255,255,0.6)"><?php esc_html_e( 'لنبدأ', 'noor-photography' ); ?></p>
			<h2 class="t-h1"><?php echo esc_html( $cta_heading ); ?></h2>
			<div class="final-cta__actions">
				<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid-light"><?php esc_html_e( 'احجز موعد التصوير', 'noor-photography' ); ?></a>
				<a href="<?php echo esc_url( noor_get_contact_url() ); ?>" class="btn btn-ghost-light"><?php esc_html_e( 'تواصل معنا', 'noor-photography' ); ?></a>
			</div>
		</div>
	</section>
	<?php
endif;
$noor_home_sections['cta'] = ob_get_clean();

$noor_home_order = noor_get_home_section_order( $front_id );
?>

<main>
	<?php
	foreach ( $noor_home_order as $noor_section_key ) {
		if ( ! empty( $noor_home_sections[ $noor_section_key ] ) ) {
			echo $noor_home_sections[ $noor_section_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each section above already escapes every dynamic value at the point it's printed into its own buffer; this just re-emits that already-safe HTML in the admin-chosen order.
		}
	}
	?>
</main>

<?php get_footer(); ?>
