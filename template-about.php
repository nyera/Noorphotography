<?php
/**
 * Template Name: نُور — عن الاستوديو
 * About page: photographer bio (from Theme Options), stats, process,
 * awards & clients (from this page's own meta), testimonials.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$process      = noor_parse_pairs( get_post_meta( get_the_ID(), '_noor_process', true ) );
	$awards       = noor_parse_pairs( get_post_meta( get_the_ID(), '_noor_awards', true ) );
	$clients      = noor_parse_lines( get_post_meta( get_the_ID(), '_noor_clients', true ) );
	$stats        = noor_get_stats();
	$ph_image     = (int) noor_get_option( 'photographer_image' );
	$testimonials = get_posts(
		array(
			'post_type'      => 'testimonial',
			'posts_per_page' => 3,
		)
	);
	?>
	<main>
		<?php
		noor_page_header(
			__( 'عن الاستوديو', 'noor-photography' ),
			get_the_title(),
			get_the_excerpt() ?: '',
			array(
				array(
					'label' => __( 'الرئيسية', 'noor-photography' ),
					'url'   => home_url( '/' ),
				),
				array( 'label' => get_the_title() ),
			)
		);
		?>

		<section class="section">
			<div class="shell about-preview-grid" style="align-items:start">
				<div class="reveal figure-col">
					<?php if ( $ph_image ) : ?>
						<?php echo wp_get_attachment_image( $ph_image, 'noor-portrait', false, array( 'alt' => noor_get_option( 'photographer_name' ) ) ); ?>
						<p class="t-caption" style="margin-top:1rem"><?php echo esc_html( noor_get_option( 'photographer_name' ) ); ?></p>
					<?php elseif ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'noor-portrait' ); ?>
					<?php endif; ?>
				</div>
				<div class="content-col">
					<h2 class="t-h2"><?php esc_html_e( 'السيرة', 'noor-photography' ); ?></h2>
					<div class="t-lead" style="margin-top:1.5rem;display:flex;flex-direction:column;gap:1.5rem"><?php the_content(); ?></div>
					<?php if ( noor_get_option( 'photographer_bio' ) ) : ?>
						<h2 class="t-h2" style="margin-top:3.5rem"><?php esc_html_e( 'الفلسفة', 'noor-photography' ); ?></h2>
						<p class="t-lead" style="margin-top:1.5rem"><?php echo esc_html( noor_get_option( 'photographer_bio' ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php if ( $stats ) : ?>
		<section class="stats-band bg-ink">
			<div class="shell stats-grid">
				<?php foreach ( $stats as $stat ) : ?>
					<div>
						<p class="value"><span data-counter-to="<?php echo esc_attr( $stat['value'] ); ?>" data-counter-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>">0</span></p>
						<p class="t-caption label"><?php echo esc_html( $stat['label'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $process ) : ?>
		<section class="section">
			<div class="shell">
				<div style="margin-bottom:3rem;max-width:38rem">
					<p class="t-eyebrow"><?php esc_html_e( 'طريقة العمل', 'noor-photography' ); ?></p>
					<h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'من أول رسالة حتى التسليم', 'noor-photography' ); ?></h2>
				</div>
				<ol class="process-list">
					<?php foreach ( $process as $i => $p ) : ?>
						<li><span class="num t-caption"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $p[0] ); ?></h3><p class="t-lead" style="font-size:0.9rem"><?php echo esc_html( $p[1] ); ?></p></li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $awards || $clients ) : ?>
		<section class="section bg-surface">
			<div class="shell awards-clients-grid">
				<?php if ( $awards ) : ?>
				<div>
					<h2 class="t-h2"><?php esc_html_e( 'الجوائز والمنشورات', 'noor-photography' ); ?></h2>
					<ul class="awards-list" style="margin-top:2rem">
						<?php foreach ( $awards as $a ) : ?>
							<li><span class="year t-caption"><?php echo esc_html( $a[0] ); ?></span><span style="font-size:0.9rem"><?php echo esc_html( $a[1] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
				<?php if ( $clients ) : ?>
				<div>
					<h2 class="t-h2"><?php esc_html_e( 'علامات عملنا معها', 'noor-photography' ); ?></h2>
					<ul class="clients-grid">
						<?php
						foreach ( $clients as $c ) :
							?>
							<li><?php echo esc_html( $c ); ?></li><?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $testimonials ) : ?>
		<section class="section">
			<div class="shell">
				<div class="text-center" style="margin-bottom:3rem"><p class="t-eyebrow"><?php esc_html_e( 'آراء', 'noor-photography' ); ?></p><h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'شهادات من عملاء الاستوديو', 'noor-photography' ); ?></h2></div>
				<div class="testimonials-grid">
					<?php foreach ( $testimonials as $t ) : ?>
						<figure>
							<blockquote style="font-family:var(--font-display);font-size:1.125rem;line-height:2">«<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>»</blockquote>
							<figcaption class="t-caption" style="margin-top:1.5rem;border-top:1px solid var(--border);padding-top:1.25rem"><?php echo esc_html( get_the_title( $t ) . ' — ' . get_post_meta( $t->ID, '_noor_role', true ) ); ?></figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
				<div class="text-center" style="margin-top:4rem"><a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid"><?php esc_html_e( 'احجز جلستك', 'noor-photography' ); ?></a></div>
			</div>
		</section>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
