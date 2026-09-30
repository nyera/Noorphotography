<?php
/**
 * Generic fallback archive (categories, tags, authors, dates).
 */
get_header();
?>
<main class="page-content">
	<?php
	noor_page_header(
		__( 'أرشيف', 'noor-photography' ),
		get_the_archive_title(),
		get_the_archive_description(),
		array(
			array(
				'label' => __( 'الرئيسية', 'noor-photography' ),
				'url'   => home_url( '/' ),
			),
			array( 'label' => get_the_archive_title() ),
		)
	);
	?>
	<div class="shell">
		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
						<a href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) :
								the_post_thumbnail( 'noor-card-landscape' );
endif;
							?>
							<h2><?php the_title(); ?></h2>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( 'السابق', 'noor-photography' ),
					'next_text' => __( 'التالي', 'noor-photography' ),
					'class'     => 'pagination-nav',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state"><h3 class="t-h3"><?php esc_html_e( 'لا يوجد محتوى', 'noor-photography' ); ?></h3></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
