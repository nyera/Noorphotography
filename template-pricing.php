<?php
/**
 * Template Name: نُور — الباقات والأسعار
 * Pricing page: Package CPT cards + a dynamically-unioned comparison table
 * built from each package's own "compare" rows, then services + page FAQ.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$packages = get_posts(
		array(
			'post_type'      => 'package',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	$services = get_posts(
		array(
			'post_type'      => 'service',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	$faq      = noor_parse_pairs( get_post_meta( get_the_ID(), '_noor_faq', true ) );

	// Union comparison row labels across all packages, preserving first-seen order.
	$row_labels     = array();
	$package_fields = array();
	foreach ( $packages as $p ) {
		$pf                       = noor_get_package_fields( $p->ID );
		$package_fields[ $p->ID ] = $pf;
		foreach ( $pf['compare'] as $row ) {
			if ( ! in_array( $row[0], $row_labels, true ) ) {
				$row_labels[] = $row[0];
			}
		}
	}
	?>
	<main>
		<?php
		noor_page_header(
			__( 'الباقات', 'noor-photography' ),
			get_the_title(),
			get_the_excerpt() ?: __( 'اختر الباقة الأقرب لاحتياجك، أو اطلب باقة مخصّصة تُبنى حول مناسبتك.', 'noor-photography' ),
			array(
				array(
					'label' => __( 'الرئيسية', 'noor-photography' ),
					'url'   => home_url( '/' ),
				),
				array( 'label' => get_the_title() ),
			)
		);
		?>

		<?php if ( $packages ) : ?>
		<section class="section">
			<div class="shell pricing-grid">
				<?php
				foreach ( $packages as $i => $p ) :
					$pf = $package_fields[ $p->ID ];
					?>
					<article class="pricing-card reveal <?php echo $pf['highlight'] ? 'is-highlight' : ''; ?>" style="transition-delay: <?php echo esc_attr( $i * 90 ); ?>ms">
						<?php
						if ( $pf['note'] ) :
							?>
							<p class="t-eyebrow" style="<?php echo $pf['highlight'] ? 'color:rgba(255,255,255,0.6)' : ''; ?>"><?php echo esc_html( $pf['note'] ); ?></p><?php endif; ?>
						<h2 class="t-h3" style="margin-top:1rem"><?php echo esc_html( get_the_title( $p ) ); ?></h2>
						<p class="price"><?php echo esc_html( $pf['price'] ); ?> <small><?php echo esc_html( noor_get_option( 'currency_symbol' ) ); ?></small></p>
						<ul>
							<?php foreach ( $pf['features'] as $f ) : ?>
								<li><span class="dot" aria-hidden="true"></span><?php echo esc_html( $f ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn <?php echo $pf['highlight'] ? 'btn-solid-light' : 'btn-outline'; ?> btn-block" style="margin-top:2rem"><?php esc_html_e( 'احجز هذه الباقة', 'noor-photography' ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $row_labels ) : ?>
		<section class="section bg-surface">
			<div class="shell">
				<div style="margin-bottom:2.5rem;max-width:38rem"><p class="t-eyebrow"><?php esc_html_e( 'مقارنة', 'noor-photography' ); ?></p><h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'ما الفرق بين الباقات؟', 'noor-photography' ); ?></h2></div>
				<div class="compare-table-wrap">
					<table class="compare-table">
						<caption class="sr-only"><?php esc_html_e( 'مقارنة تفصيلية بين باقات التصوير', 'noor-photography' ); ?></caption>
						<thead>
							<tr>
								<th scope="col" class="t-caption"><?php esc_html_e( 'البند', 'noor-photography' ); ?></th>
								<?php
								foreach ( $packages as $p ) :
									?>
									<th scope="col" class="name"><?php echo esc_html( get_the_title( $p ) ); ?></th><?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $row_labels as $label ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $label ); ?></th>
								<?php
								foreach ( $packages as $p ) :
									$value = '—';
									foreach ( $package_fields[ $p->ID ]['compare'] as $row ) {
										if ( $row[0] === $label ) {
											$value = $row[1];
											break; }
									}
									?>
									<td><?php echo esc_html( $value ); ?></td>
								<?php endforeach; ?>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $services ) : ?>
		<section class="section">
			<div class="shell">
				<div style="margin-bottom:2.5rem;max-width:38rem"><p class="t-eyebrow"><?php esc_html_e( 'خدمات أخرى', 'noor-photography' ); ?></p><h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'أسعار تبدأ من', 'noor-photography' ); ?></h2></div>
				<ul class="services-list" style="border-block:1px solid var(--border)">
					<?php
					foreach ( $services as $service ) :
						$sf = noor_get_service_fields( $service->ID );
						?>
						<li><a href="<?php echo esc_url( get_permalink( $service ) ); ?>" style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:1rem;padding-block:1.5rem">
							<span style="font-family:var(--font-display);font-size:1.25rem"><?php echo esc_html( get_the_title( $service ) ); ?></span>
							<span class="t-caption"><?php echo esc_html( $sf['price'] ); ?></span>
						</a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $faq ) : ?>
		<section class="section bg-surface">
			<div class="shell-narrow">
				<div style="margin-bottom:2.5rem"><p class="t-eyebrow"><?php esc_html_e( 'أسئلة', 'noor-photography' ); ?></p><h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'الأسئلة الشائعة عن الأسعار', 'noor-photography' ); ?></h2></div>
				<?php noor_render_accordion( $faq ); ?>
			</div>
		</section>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
