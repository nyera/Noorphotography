<?php
/**
 * Site search overlay. Results are rendered client-side (assets/js/main.js)
 * from the noorData.search index localized in inc/enqueue.php.
 */
?>
<div class="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'البحث في الموقع', 'noor-photography' ); ?>">
	<div class="shell">
		<div class="search-overlay__top">
			<p class="t-eyebrow"><?php esc_html_e( 'البحث', 'noor-photography' ); ?></p>
			<button type="button" class="btn btn-outline btn-sm search-overlay__close"><?php esc_html_e( 'إغلاق', 'noor-photography' ); ?></button>
		</div>

		<label for="site-search" class="sr-only"><?php esc_html_e( 'ابحث في الأعمال والخدمات', 'noor-photography' ); ?></label>
		<input type="text" id="site-search" autocomplete="off" placeholder="<?php esc_attr_e( 'ابحث عن مشروع أو خدمة…', 'noor-photography' ); ?>" />

		<div class="search-overlay__suggestions">
			<p class="t-caption" style="width:100%"><?php esc_html_e( 'اقتراحات سريعة', 'noor-photography' ); ?></p>
			<?php foreach ( array( 'أعراس', 'بورتريه', 'الباقات', 'تواصل' ) as $suggestion ) : ?>
				<button type="button" data-search-suggestion="<?php echo esc_attr( $suggestion ); ?>"><?php echo esc_html( $suggestion ); ?></button>
			<?php endforeach; ?>
		</div>

		<div class="search-overlay__results"></div>
	</div>
</div>
