<?php
/**
 * Portfolio archive: filter bar, layout switch, masonry/grid/full views.
 * Also serves the portfolio_category taxonomy archive (see taxonomy-portfolio_category.php).
 */
get_header();

$categories   = get_terms(
	array(
		'taxonomy'   => 'portfolio_category',
		'hide_empty' => true,
	)
);
$current_term = is_tax( 'portfolio_category' ) ? get_queried_object() : null;

if ( have_posts() ) :
	?>
<main>
	<?php
	noor_page_header(
		__( 'الأعمال', 'noor-photography' ),
		$current_term ? $current_term->name : __( 'أرشيف المشاريع', 'noor-photography' ),
		__( 'استخدم التصفية أو البحث للوصول إلى ما يهمك.', 'noor-photography' ),
		array(
			array(
				'label' => __( 'الرئيسية', 'noor-photography' ),
				'url'   => home_url( '/' ),
			),
			array( 'label' => __( 'الأعمال', 'noor-photography' ) ),
		)
	);
	?>

	<div class="toolbar">
		<div class="shell toolbar__row">
			<div class="toolbar__filters" data-filter-bar role="group" aria-label="<?php esc_attr_e( 'تصفية حسب التصنيف', 'noor-photography' ); ?>">
				<button type="button" class="<?php echo ! $current_term ? 'is-active' : ''; ?>" data-filter="all"><?php esc_html_e( 'الكل', 'noor-photography' ); ?></button>
				<?php foreach ( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" style="text-decoration:none">
						<button type="button" class="<?php echo ( $current_term && $current_term->term_id === $category->term_id ) ? 'is-active' : ''; ?>" data-filter="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></button>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="toolbar__right">
				<div class="layout-switch" data-layout-switch role="group" aria-label="<?php esc_attr_e( 'نمط العرض', 'noor-photography' ); ?>">
					<button type="button" class="is-active" data-layout="masonry"><?php esc_html_e( 'متدرّج', 'noor-photography' ); ?></button>
					<button type="button" data-layout="grid-cards"><?php esc_html_e( 'شبكة', 'noor-photography' ); ?></button>
					<button type="button" data-layout="grid-full"><?php esc_html_e( 'عرض كامل', 'noor-photography' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<section class="section">
		<div class="shell">
			<h2 class="sr-only"><?php esc_html_e( 'قائمة المشاريع', 'noor-photography' ); ?></h2>
			<div class="masonry" data-project-grid>
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<?php
					get_template_part(
						'template-parts/portfolio/card',
						null,
						array(
							'project'  => get_post(),
							'priority' => $i < 3,
						)
					);
					++$i;
					?>
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
		</div>
	</section>
</main>
<?php else : ?>
<main>
	<?php
	noor_page_header(
		__( 'الأعمال', 'noor-photography' ),
		__( 'أرشيف المشاريع', 'noor-photography' ),
		'',
		array(
			array(
				'label' => __( 'الرئيسية', 'noor-photography' ),
				'url'   => home_url( '/' ),
			),
			array( 'label' => __( 'الأعمال', 'noor-photography' ) ),
		)
	);
	?>
	<section class="section">
		<div class="shell">
			<div class="empty-state">
				<span class="marker" aria-hidden="true"></span>
				<h3 class="t-h3"><?php esc_html_e( 'لا توجد مشاريع بعد', 'noor-photography' ); ?></h3>
				<p class="t-lead"><?php esc_html_e( 'أضف أول مشروع من لوحة التحكم لعرضه هنا.', 'noor-photography' ); ?></p>
			</div>
		</div>
	</section>
</main>
<?php endif; ?>

<?php get_footer(); ?>
