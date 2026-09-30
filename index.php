<?php
/**
 * Fallback template (template hierarchy safety net).
 */
get_header();
?>
<main class="page-content">
	<div class="shell" style="padding-top:9rem">
		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="blog-card">
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
		<?php else : ?>
			<div class="empty-state"><h3 class="t-h3"><?php esc_html_e( 'لا يوجد محتوى', 'noor-photography' ); ?></h3></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
