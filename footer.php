<?php
/**
 * Closes #main, renders the site footer + cookie notice, then wp_footer/html.
 */
?>
</div><!-- #main -->

<?php get_template_part( 'template-parts/footer/site-footer' ); ?>

<div class="cookie-notice">
	<div class="shell cookie-notice__row">
		<p class="t-caption"><?php esc_html_e( 'نستخدم ملفات تعريف الارتباط لتحسين تجربة التصفّح وقياس أداء المعارض. يمكنك مراجعة سياسة الخصوصية في أي وقت.', 'noor-photography' ); ?></p>
		<div class="cookie-notice__actions">
			<button type="button" class="btn btn-outline btn-sm" data-cookie-dismiss><?php esc_html_e( 'الأساسية فقط', 'noor-photography' ); ?></button>
			<button type="button" class="btn btn-solid btn-sm" data-cookie-dismiss><?php esc_html_e( 'موافق', 'noor-photography' ); ?></button>
		</div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
