<?php
/**
 * Generic page template (no dedicated design-specific template).
 *
 * Pages built with Elementor render their own full-width canvas — we get
 * out of the way entirely (no page-header, no `.shell` width constraint) so
 * Elementor's own container/width settings apply as the page builder
 * expects. See inc/compat.php.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$is_elementor = noor_is_elementor_page( get_the_ID() );
	?>
	<main class="page-content">
		<?php if ( $is_elementor ) : ?>
			<?php the_content(); ?>
		<?php else : ?>
			<?php
			noor_page_header(
				__( 'صفحة', 'noor-photography' ),
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'shell' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<div style="margin-bottom:2.5rem"><?php the_post_thumbnail( 'noor-card-landscape', array( 'style' => 'width:100%;object-fit:cover' ) ); ?></div>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="pagination-nav" aria-label="' . esc_attr__( 'ترقيم الصفحات', 'noor-photography' ) . '"><p>' . esc_html__( 'الصفحات:', 'noor-photography' ),
							'after'  => '</p></nav>',
						)
					);
					?>
				</div>
			</article>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
