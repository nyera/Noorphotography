<?php
/**
 * Comments template. comments_template() (called from single.php) silently
 * renders nothing if the active theme has no comments.php — this is the
 * file that makes the blog's comment list + form actually work.
 */

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="noor-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="t-h3 noor-comments__title">
			<?php
			printf(
				/* translators: %s: comment count */
				esc_html( _nx( '%s تعليق', '%s تعليقات', get_comments_number(), 'comments title', 'noor-photography' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>

		<ol class="noor-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => __( 'السابق', 'noor-photography' ),
				'next_text' => __( 'التالي', 'noor-photography' ),
				'class'     => 'pagination-nav noor-comments__pagination',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="t-caption noor-comments__closed"><?php esc_html_e( 'التعليقات مغلقة.', 'noor-photography' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'   => __( 'أضف تعليقًا', 'noor-photography' ),
			'label_submit'  => __( 'إرسال التعليق', 'noor-photography' ),
			'class_submit'  => 'btn btn-solid',
			'comment_field' => '<p class="comment-form-comment"><label for="comment" class="t-caption">' . esc_html__( 'التعليق', 'noor-photography' ) . ' *</label><textarea id="comment" name="comment" class="field" rows="6" required></textarea></p>',
			'fields'        => array(
				'author' => '<p class="comment-form-author"><label for="author" class="t-caption">' . esc_html__( 'الاسم', 'noor-photography' ) . ' *</label><input id="author" name="author" type="text" class="field" required /></p>',
				'email'  => '<p class="comment-form-email"><label for="email" class="t-caption">' . esc_html__( 'البريد الإلكتروني', 'noor-photography' ) . ' *</label><input id="email" name="email" type="email" class="field" required /></p>',
			),
		)
	);
	?>
</div>
