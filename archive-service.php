<?php
/**
 * Services archive: card grid + a page-wide FAQ (managed on the Services
 * page itself if one is assigned a template, otherwise omitted).
 */
get_header();
?>
<main>
	<?php
	noor_page_header(
		__( 'الخدمات', 'noor-photography' ),
		__( 'خدمات التصوير', 'noor-photography' ),
		__( 'كل خدمة لها فريقها وإعدادها وجدولها الزمني. اختر ما يناسب مشروعك، أو تواصل معنا لتصميم باقة خاصة.', 'noor-photography' ),
		array(
			array(
				'label' => __( 'الرئيسية', 'noor-photography' ),
				'url'   => home_url( '/' ),
			),
			array( 'label' => __( 'الخدمات', 'noor-photography' ) ),
		)
	);
	?>

	<section class="section">
		<div class="shell">
			<?php if ( have_posts() ) : ?>
			<div class="services-grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					$sf    = noor_get_service_fields( get_the_ID() );
					$ratio = ( 0 === $i % 3 ) ? '4 / 5' : '3 / 2';
					?>
					<article class="service-card reveal" style="transition-delay: <?php echo esc_attr( ( $i % 2 ) * 90 ); ?>ms">
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="img-zoom" style="aspect-ratio: <?php echo esc_attr( $ratio ); ?>">
									<?php the_post_thumbnail( 'noor-card-landscape', array( 'style' => 'width:100%;height:100%;object-fit:cover' ) ); ?>
								</div>
							<?php endif; ?>
							<div class="project-card__row">
								<h2><?php the_title(); ?></h2>
								<p class="t-caption" style="flex-shrink:0"><?php echo esc_html( $sf['price'] ); ?></p>
							</div>
							<p class="t-lead" style="margin-top:0.75rem;font-size:0.9rem"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<span class="link-underline" style="margin-top:1.25rem;display:inline-block;font-size:0.9rem"><?php esc_html_e( 'تفاصيل الخدمة', 'noor-photography' ); ?></span>
						</a>
					</article>
					<?php
					++$i;
				endwhile;
				?>
			</div>
			<?php else : ?>
				<div class="empty-state">
					<span class="marker" aria-hidden="true"></span>
					<h3 class="t-h3"><?php esc_html_e( 'لا توجد خدمات بعد', 'noor-photography' ); ?></h3>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section bg-surface">
		<div class="shell text-center">
			<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid"><?php esc_html_e( 'أرسل طلب حجز', 'noor-photography' ); ?></a>
		</div>
	</section>
</main>
<?php get_footer(); ?>
