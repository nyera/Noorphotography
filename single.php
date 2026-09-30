<?php
/**
 * Single blog post.
 */
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<main class="page-content">
		<?php
		noor_page_header(
			__( 'المدونة', 'noor-photography' ),
			get_the_title(),
			get_the_date(),
			array(
				array(
					'label' => __( 'الرئيسية', 'noor-photography' ),
					'url'   => home_url( '/' ),
				),
				array(
					'label' => __( 'المدونة', 'noor-photography' ),
					'url'   => get_permalink( get_option( 'page_for_posts' ) ),
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
						'before' => '<nav class="pagination-nav" aria-label="' . esc_attr__( 'ترقيم صفحات المقال', 'noor-photography' ) . '"><p>' . esc_html__( 'الصفحات:', 'noor-photography' ),
						'after'  => '</p></nav>',
					)
				);
				?>
			</div>
			<?php
			$noor_tags = get_the_tags();
			if ( $noor_tags ) :
				?>
				<div class="noor-post-tags">
					<span class="t-caption"><?php esc_html_e( 'الوسوم:', 'noor-photography' ); ?></span>
					<?php foreach ( $noor_tags as $noor_tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $noor_tag ) ); ?>" class="noor-post-tags__link"><?php echo esc_html( $noor_tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
				<aside style="margin-top:4rem"><?php dynamic_sidebar( 'blog-sidebar' ); ?></aside>
			<?php endif; ?>
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div style="margin-top:4rem"><?php comments_template(); ?></div>
			<?php endif; ?>
		</article>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
