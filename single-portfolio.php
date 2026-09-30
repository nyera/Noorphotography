<?php
/**
 * Single portfolio project: editorial hero, meta strip, description,
 * full gallery with lightbox, prev/next, related projects.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$project_id = get_the_ID();
	$fields     = noor_get_project_fields( $project_id );
	$terms      = get_the_terms( $project_id, 'portfolio_category' );
	$cat_label  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

	$siblings = get_posts(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
		)
	);
	$pos      = array_search( $project_id, $siblings, true );
	$count    = count( $siblings );
	$prev_id  = $count ? $siblings[ ( $pos - 1 + $count ) % $count ] : 0;
	$next_id  = $count ? $siblings[ ( $pos + 1 ) % $count ] : 0;

	$related = get_posts(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => 3,
			'post__not_in'   => array( $project_id ),
			'orderby'        => 'rand',
		)
	);
	?>

	<main>
		<section class="single-hero">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'noor-hero', array( 'alt' => get_the_title() ) ); ?>
			<?php endif; ?>
			<div class="single-hero__scrim"></div>
			<div class="shell single-hero__content">
				<?php
				noor_breadcrumbs(
					array(
						array(
							'label' => __( 'الرئيسية', 'noor-photography' ),
							'url'   => home_url( '/' ),
						),
						array(
							'label' => __( 'الأعمال', 'noor-photography' ),
							'url'   => get_post_type_archive_link( 'portfolio' ),
						),
						array( 'label' => get_the_title() ),
					)
				);
				?>
				<h1 class="t-display"><?php the_title(); ?></h1>
				<?php if ( get_the_excerpt() ) : ?>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<section class="meta-strip">
			<dl class="shell meta-strip__grid">
				<?php if ( $fields['client'] ) : ?>
				<div><dt class="t-caption"><?php esc_html_e( 'العميل', 'noor-photography' ); ?></dt><dd><?php echo esc_html( $fields['client'] ); ?></dd></div>
				<?php endif; ?>
				<div><dt class="t-caption"><?php esc_html_e( 'التصنيف', 'noor-photography' ); ?></dt><dd><?php echo esc_html( $cat_label ); ?></dd></div>
				<?php if ( $fields['location'] ) : ?>
				<div><dt class="t-caption"><?php esc_html_e( 'الموقع', 'noor-photography' ); ?></dt><dd><?php echo esc_html( $fields['location'] ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $fields['year'] ) : ?>
				<div><dt class="t-caption"><?php esc_html_e( 'السنة', 'noor-photography' ); ?></dt><dd><?php echo esc_html( $fields['year'] ); ?></dd></div>
				<?php endif; ?>
			</dl>
		</section>

		<?php if ( get_the_content() ) : ?>
		<section class="section" style="padding-block: clamp(3rem, 6vw, 5rem)">
			<div class="shell" style="display:grid;gap:2rem">
				<h2 class="t-eyebrow"><?php esc_html_e( 'عن المشروع', 'noor-photography' ); ?></h2>
				<div class="t-lead" style="max-width:56rem"><?php the_content(); ?></div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $fields['gallery'] ) : ?>
		<section style="padding-bottom:2rem">
			<div class="shell editorial-gallery">
				<div class="editorial-gallery__lead reveal">
					<?php $first = $fields['gallery'][0]; ?>
					<button type="button" class="img-zoom" style="width:100%" data-lightbox-open="<?php echo esc_url( wp_get_attachment_image_url( $first, 'full' ) ); ?>" data-lightbox-alt="<?php echo esc_attr( get_the_title() ); ?>" aria-label="<?php esc_attr_e( 'فتح الصورة بملء الشاشة', 'noor-photography' ); ?>">
						<?php echo wp_get_attachment_image( $first, 'large' ); ?>
					</button>
				</div>
				<?php if ( count( $fields['gallery'] ) > 1 ) : ?>
				<div class="editorial-gallery__grid">
					<?php foreach ( array_slice( $fields['gallery'], 1 ) as $i => $att_id ) : ?>
						<div class="reveal" style="transition-delay: <?php echo esc_attr( $i * 80 ); ?>ms">
							<?php /* translators: %d: image position in the gallery */ ?>
							<button type="button" class="img-zoom" style="width:100%" data-lightbox-open="<?php echo esc_url( wp_get_attachment_image_url( $att_id, 'full' ) ); ?>" data-lightbox-alt="<?php echo esc_attr( get_the_title() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'فتح الصورة %d بملء الشاشة', 'noor-photography' ), $i + 2 ) ); ?>">
								<?php echo wp_get_attachment_image( $att_id, 'large' ); ?>
							</button>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php /* translators: 1: project location, 2: project year */ ?>
				<p class="t-caption"><?php echo esc_html( sprintf( __( 'جميع الصور مصوّرة بضوء طبيعي · %1$s · %2$s', 'noor-photography' ), $fields['location'], $fields['year'] ) ); ?></p>
			</div>
		</section>
		<?php endif; ?>

		<section class="section" style="padding-block: clamp(3rem, 6vw, 5rem)">
			<div class="shell share-row">
				<p class="t-caption"><?php esc_html_e( 'مشاركة المشروع', 'noor-photography' ); ?></p>
				<ul>
					<li><a class="link-underline" target="_blank" rel="noopener noreferrer" href="https://wa.me/?text=<?php echo rawurlencode( get_the_permalink() ); ?>"><?php esc_html_e( 'واتساب', 'noor-photography' ); ?></a></li>
					<li><a class="link-underline" target="_blank" rel="noopener noreferrer" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_the_permalink() ); ?>"><?php esc_html_e( 'إكس', 'noor-photography' ); ?></a></li>
					<li><button type="button" class="link-underline" onclick="navigator.clipboard&&navigator.clipboard.writeText('<?php echo esc_js( get_the_permalink() ); ?>')"><?php esc_html_e( 'نسخ الرابط', 'noor-photography' ); ?></button></li>
				</ul>
			</div>

			<?php if ( $prev_id && $next_id && $count > 1 ) : ?>
			<div class="shell prev-next">
				<a class="prev" href="<?php echo esc_url( get_permalink( $prev_id ) ); ?>" rel="prev">
					<span class="t-caption"><?php esc_html_e( 'المشروع السابق', 'noor-photography' ); ?></span>
					<span><?php echo esc_html( get_the_title( $prev_id ) ); ?></span>
				</a>
				<a class="next" href="<?php echo esc_url( get_permalink( $next_id ) ); ?>" rel="next">
					<span class="t-caption"><?php esc_html_e( 'المشروع التالي', 'noor-photography' ); ?></span>
					<span><?php echo esc_html( get_the_title( $next_id ) ); ?></span>
				</a>
			</div>
			<?php endif; ?>
		</section>

		<?php if ( $related ) : ?>
		<section class="section bg-surface">
			<div class="shell">
				<h2 class="t-h3" style="margin-bottom:2.5rem"><?php esc_html_e( 'مشاريع ذات صلة', 'noor-photography' ); ?></h2>
				<div class="grid-cards">
					<?php foreach ( $related as $r ) : ?>
						<?php
						get_template_part(
							'template-parts/portfolio/card',
							null,
							array(
								'project' => $r,
								'ratio'   => '4 / 5',
							)
						);
						?>
					<?php endforeach; ?>
				</div>
				<div class="text-center" style="margin-top:4rem">
					<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="btn btn-solid"><?php esc_html_e( 'احجز مشروعًا مشابهًا', 'noor-photography' ); ?></a>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $fields['gallery'] ) : ?>
		<div class="noor-lightbox">
			<div class="noor-lightbox__top">
				<p class="t-caption noor-lightbox__counter" style="color:rgba(255,255,255,0.7)"></p>
				<button type="button" class="noor-lightbox__close"><?php esc_html_e( 'إغلاق', 'noor-photography' ); ?></button>
			</div>
			<div class="noor-lightbox__stage"><img src="" alt="" /></div>
			<div class="noor-lightbox__bottom">
				<button type="button" class="btn btn-ghost-light" data-lightbox-prev><?php esc_html_e( 'السابق', 'noor-photography' ); ?></button>
				<p class="t-caption noor-lightbox__caption" style="color:rgba(255,255,255,0.7)"><?php the_title(); ?></p>
				<button type="button" class="btn btn-ghost-light" data-lightbox-next><?php esc_html_e( 'التالي', 'noor-photography' ); ?></button>
			</div>
		</div>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
