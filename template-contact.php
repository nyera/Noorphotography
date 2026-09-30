<?php
/**
 * Template Name: نُور — تواصل معنا
 * Contact page: studio details (from Theme Options) + working contact form.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$faq = noor_parse_pairs( get_post_meta( get_the_ID(), '_noor_faq', true ) );
	// Read-only display flag — see the matching comment in template-booking.php.
	$submit_status = isset( $_GET['noor_status'] ) ? sanitize_key( $_GET['noor_status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<main>
		<?php
		noor_page_header(
			__( 'تواصل', 'noor-photography' ),
			get_the_title(),
			get_the_excerpt() ?: __( 'بابنا مفتوح بموعد مسبق، ونرد على كل رسالة خلال يوم عمل واحد.', 'noor-photography' ),
			array(
				array(
					'label' => __( 'الرئيسية', 'noor-photography' ),
					'url'   => home_url( '/' ),
				),
				array( 'label' => get_the_title() ),
			)
		);
		?>

		<section class="section">
			<div class="shell" style="display:grid;gap:4rem;grid-template-columns:1fr">
				<div>
					<h2 class="t-h3"><?php esc_html_e( 'بيانات الاستوديو', 'noor-photography' ); ?></h2>
					<dl style="margin-top:2rem;border-block:1px solid var(--border);font-size:0.9rem">
						<?php
						$rows = array(
							__( 'العنوان', 'noor-photography' )      => noor_get_option( 'contact_address' ),
							__( 'الهاتف', 'noor-photography' )       => noor_get_option( 'contact_phone' ),
							__( 'البريد', 'noor-photography' )        => noor_get_option( 'contact_email' ),
							__( 'ساعات العمل', 'noor-photography' )  => noor_get_option( 'contact_hours' ),
						);
						foreach ( $rows as $label => $value ) :
							if ( ! $value ) {
								continue;
							}
							?>
							<div style="display:flex;justify-content:space-between;gap:1.5rem;padding-block:1rem;border-bottom:1px solid var(--border)">
								<dt class="t-caption"><?php echo esc_html( $label ); ?></dt>
								<dd><?php echo esc_html( $value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>

					<?php if ( get_the_content() ) : ?>
						<div class="t-lead" style="margin-top:2rem"><?php the_content(); ?></div>
					<?php endif; ?>

					<p class="t-lead" style="margin-top:2rem;font-size:0.9rem">
						<?php esc_html_e( 'تبحث عن حجز جلسة مباشرة؟', 'noor-photography' ); ?>
						<a href="<?php echo esc_url( noor_get_booking_url() ); ?>" class="link-underline" style="color:var(--foreground)"><?php esc_html_e( 'انتقل إلى نموذج الحجز', 'noor-photography' ); ?></a>.
					</p>
				</div>

				<div>
					<h2 class="t-h3"><?php esc_html_e( 'أرسل رسالة', 'noor-photography' ); ?></h2>

					<?php if ( 'sent' === $submit_status ) : ?>
						<div class="sidebar-card text-center" style="margin-top:2rem">
							<span class="marker" aria-hidden="true" style="margin-inline:auto;display:block;width:0.75rem;height:0.75rem;transform:rotate(45deg);border:1px solid var(--accent);margin-bottom:1.25rem"></span>
							<h3 class="t-h3"><?php esc_html_e( 'تم إرسال رسالتك', 'noor-photography' ); ?></h3>
							<p class="t-lead" style="margin-top:0.75rem;font-size:0.9rem"><?php esc_html_e( 'شكرًا لك، سنعود إليك خلال يوم عمل.', 'noor-photography' ); ?></p>
						</div>
					<?php else : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:2rem;display:grid;gap:1.5rem" novalidate>
							<input type="hidden" name="action" value="noor_contact" />
							<?php wp_nonce_field( 'noor_contact_form', 'noor_contact_nonce' ); ?>
							<div class="honeypot-field"><label>Website<input type="text" name="noor_website" tabindex="-1" autocomplete="off" /></label></div>

							<?php if ( 'error' === $submit_status ) : ?>
								<p class="form-alert" role="alert"><?php esc_html_e( 'تحقّق من الاسم والبريد الإلكتروني، واكتب رسالة لا تقل عن ١٠ أحرف.', 'noor-photography' ); ?></p>
							<?php endif; ?>

							<div>
								<label for="c-name" class="t-caption"><?php esc_html_e( 'الاسم *', 'noor-photography' ); ?></label>
								<input id="c-name" name="name" class="field" required />
							</div>
							<div>
								<label for="c-email" class="t-caption"><?php esc_html_e( 'البريد الإلكتروني *', 'noor-photography' ); ?></label>
								<input id="c-email" name="email" type="email" class="field" required />
							</div>
							<div>
								<label for="c-subject" class="t-caption"><?php esc_html_e( 'الموضوع', 'noor-photography' ); ?></label>
								<input id="c-subject" name="subject" class="field" />
							</div>
							<div>
								<label for="c-msg" class="t-caption"><?php esc_html_e( 'الرسالة *', 'noor-photography' ); ?></label>
								<textarea id="c-msg" name="message" rows="6" class="field" required></textarea>
							</div>
							<button type="submit" class="btn btn-solid" style="width:fit-content"><?php esc_html_e( 'إرسال الرسالة', 'noor-photography' ); ?></button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php if ( $faq ) : ?>
		<section class="section bg-surface">
			<div class="shell-narrow">
				<div style="margin-bottom:2.5rem"><p class="t-eyebrow"><?php esc_html_e( 'أسئلة', 'noor-photography' ); ?></p><h2 class="t-h2" style="margin-top:1rem"><?php esc_html_e( 'قبل أن تراسلنا', 'noor-photography' ); ?></h2></div>
				<?php noor_render_accordion( $faq ); ?>
			</div>
		</section>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
