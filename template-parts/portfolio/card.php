<?php
/**
 * Reusable portfolio project card.
 *
 * @var WP_Post $project      Set by the including template.
 * @var string  $card_ratio   Optional CSS ratio override, e.g. "4 / 5".
 * @var bool    $priority     Optional — eager-load the image when true.
 */
$project    = $args['project'] ?? get_post();
$card_ratio = $args['ratio'] ?? '';
$priority   = $args['priority'] ?? false;
$fields     = noor_get_project_fields( $project->ID );
$ratio_css  = $card_ratio ?: noor_ratio_css( $fields['ratio'] );
$terms      = get_the_terms( $project, 'portfolio_category' );
$cat_label  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_slug   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '';
?>
<article id="post-<?php echo esc_attr( $project->ID ); ?>" <?php post_class( 'project-card', $project ); ?> data-project-category="<?php echo esc_attr( $cat_slug ); ?>">
	<a href="<?php echo esc_url( get_permalink( $project ) ); ?>">
		<div class="img-zoom project-card__media" style="aspect-ratio: <?php echo esc_attr( $ratio_css ); ?>">
			<?php if ( has_post_thumbnail( $project ) ) : ?>
				<?php
				echo get_the_post_thumbnail(
					$project,
					noor_image_size_for_ratio( $fields['ratio'] ),
					array(
						'alt'     => sprintf( /* translators: 1: project title, 2: category, 3: location */ __( '%1$s — تصوير %2$s في %3$s', 'noor-photography' ), get_the_title( $project ), $cat_label, $fields['location'] ),
						'loading' => $priority ? 'eager' : 'lazy',
					)
				);
				?>
			<?php endif; ?>
			<div class="project-card__scrim"></div>
			<span class="project-card__cta"><?php esc_html_e( 'عرض المشروع', 'noor-photography' ); ?></span>
		</div>

		<div class="project-card__row">
			<h3><?php echo esc_html( get_the_title( $project ) ); ?></h3>
			<p class="t-caption" style="flex-shrink:0"><?php echo esc_html( trim( $cat_label . ' · ' . $fields['year'], ' ·' ) ); ?></p>
		</div>
		<?php $excerpt = get_the_excerpt( $project ); ?>
		<?php if ( $excerpt ) : ?>
			<p class="t-caption project-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>
	</a>
</article>
