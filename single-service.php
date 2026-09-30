<?php
/**
 * Single service: hero, description + features, includes sidebar, sample
 * gallery, FAQ, one testimonial, related services.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$service_id   = get_the_ID();
	$fields       = noor_get_service_fields( $service_id );
	$related      = get_posts(
		array(
			'post_type'      => 'service',
			'posts_per_page' => 3,
			'post__not_in'   => array( $service_id ),
			'orderby'        => 'rand',
		)
	);
	$gallery      = get_posts(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => 4,
			'orderby'        => 'rand',
		)
	);
	$testimonials = get_posts(
		array(
			'post_type'      => 'testimonial',
			'posts_per_page' => 1,
		)
	);
	?>
	<main>
		<section class="single-service-hero">
			<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail( 'noor-hero', array( 'alt' => get_the_title() ) );
endif;
			?>
			<div class="single-service-hero__scrim"></div>
			<div class="shell single-service-hero__content">
				<?php
				noor_breadcrumbs(
					array(
						array(
							'label' => __( 'الرئيسية', 'noor-photography' ),
							'url'   => home_url( '/' ),
						),
						array(
							'label' => __( 'الخدمات', 'noor-photography' ),
							'url'   => get_post_type_archive_link( 'service' ),
						),
						array( 'label' => get_the_title() ),
					)
				);
				?>
				<h1 class="t-h1"><?php the_title(); ?></h1>
				<?php
				if ( get_the_excerpt() ) :
					?>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				<?php
				if ( $fields['price'] ) :
					?>
					<p class="t-eyebrow single-service-hero__price"><?php echo esc_html( $fields['price'] ); ?></p><?php endif; ?>
			</div>
		</section>

		<section class="section">
			<div class="shell" style="display:grid;gap:3.5rem;grid-template-columns:1fr">
				<div style="max-width:44rem">
					<h2 class="t-h2"><?php esc_html_e( 'عن الخدمة', 'noor-photography' ); ?></h2>
					<div class="t-lead" style="margin-top:1.5rem"><?php the_content(); ?></div>

					<?php if ( $fields['features'] ) : ?>
					<h3 class="t-h3" style="margin-top:3rem"><?php esc_html_e( 'المزايا', 'noor-photography' ); ?></h3>
					<ul class="features-grid">
						<?php foreach ( $fields['features'] as $f ) : ?>
							<li><span class="dot" aria-hidden="true"></span><?php echo esc_html( $f ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</div>

				<?php if ( $fields['includes'] || $fields['price'] ) : ?>
				<aside class="sidebar-card" style="max-width:26rem">
					<p class="t-eyebrow"><?php esc_html_e( 'ما الذي يشمله؟', 'noor-photography' ); ?></p>
					<ul>
						<?php
						foreach ( $fields['includes'] as $inc ) :
							?>
							<li><?php echo esc_html( $inc ); ?></li><?php endforeach; ?>
					</ul>
					<?php
					if ( $fields['price'] ) :
						?>
						<p class="t-h3" style="margin-top:2rem"><?php echo esc_html( $fields['price'] ); ?></p><?php endif; ?>
					<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid btn-block" style="margin-top:1.5rem"><?php esc_html_e( 'احجز هذه الخدمة', 'noor-photography' ); ?></a>
					<a href="<?php echo esc_url( noor_get_pricing_url() ); ?>" class="btn btn-outline btn-block" style="margin-top:0.75rem"><?php esc_html_e( 'مقارنة الباقات', 'noor-photography' ); ?></a>
				</aside>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( $gallery ) : ?>
		<section style="padding-bottom:5rem">
			<div class="shell">
				<h2 class="t-h3" style="margin-bottom:2rem"><?php esc_html_e( 'نماذج من الأعمال', 'noor-photography' ); ?></h2>
				<ul class="simple-media-grid">
					<?php foreach ( $gallery as $g ) : ?>
						<li><?php echo get_the_post_thumbnail( $g, 'noor-card-portrait', array( 'alt' => get_the_title( $g ) ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $fields['faq'] || $testimonials ) : ?>
		<section class="section bg-surface">
			<div class="shell" style="display:grid;gap:3.5rem;grid-template-columns:1fr">
				<?php if ( $fields['faq'] ) : ?>
				<div>
					<h2 class="t-h2"><?php esc_html_e( 'أسئلة متكررة', 'noor-photography' ); ?></h2>
					<div style="margin-top:2rem"><?php noor_render_accordion( $fields['faq'] ); ?></div>
				</div>
				<?php endif; ?>
				<?php
				if ( $testimonials ) :
					$t = $testimonials[0];
					?>
				<div>
					<h2 class="t-h2"><?php esc_html_e( 'رأي عميل', 'noor-photography' ); ?></h2>
					<figure style="margin-top:2rem">
						<blockquote style="font-family:var(--font-display);font-size:1.5rem;line-height:2">«<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>»</blockquote>
						<figcaption class="t-caption" style="margin-top:1.5rem"><?php echo esc_html( get_the_title( $t ) . ' — ' . get_post_meta( $t->ID, '_noor_role', true ) ); ?></figcaption>
					</figure>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $related ) : ?>
		<section class="section">
			<div class="shell">
				<h2 class="t-h3" style="margin-bottom:2rem"><?php esc_html_e( 'خدمات ذات صلة', 'noor-photography' ); ?></h2>
				<ul class="grid-cards">
					<?php
					foreach ( $related as $r ) :
						$rf = noor_get_service_fields( $r->ID );
						?>
						<li class="reveal">
							<a href="<?php echo esc_url( get_permalink( $r ) ); ?>">
								<?php if ( has_post_thumbnail( $r ) ) : ?>
									<div class="img-zoom"><?php echo get_the_post_thumbnail( $r, 'noor-card-landscape', array( 'style' => 'width:100%' ) ); ?></div>
								<?php endif; ?>
								<h3 style="margin-top:1.25rem;font-family:var(--font-display);font-size:1.25rem"><?php echo esc_html( get_the_title( $r ) ); ?></h3>
								<p class="t-caption" style="margin-top:0.5rem"><?php echo esc_html( $rf['price'] ); ?></p>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
