<?php
/**
 * Template Name: نُور — الحجز
 * Booking page: full booking form (service + package selects pull live from
 * the Service and Package CPTs) + a static "how it works" sidebar.
 */
get_header();
while ( have_posts() ) :
	the_post();
	// Read-only display flag set by inc/forms.php's own redirect after it
	// already verified the form's nonce server-side — nothing is written or
	// changed based on this value, it only picks which static message to
	// show, so a nonce check doesn't apply here.
	$submit_status = isset( $_GET['noor_status'] ) ? sanitize_key( $_GET['noor_status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$services      = get_posts(
		array(
			'post_type'      => 'service',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	$packages      = get_posts(
		array(
			'post_type'      => 'package',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	$process       = noor_parse_pairs( get_post_meta( get_the_ID(), '_noor_process', true ) );
	?>
	<main>
		<?php if ( 'sent' === $submit_status ) : ?>
			<?php
			noor_page_header(
				__( 'تم الاستلام', 'noor-photography' ),
				__( 'وصلنا طلبك، شكرًا لك', 'noor-photography' ),
				'',
				array(
					array(
						'label' => __( 'الرئيسية', 'noor-photography' ),
						'url'   => home_url( '/' ),
					),
					array( 'label' => __( 'الحجز', 'noor-photography' ) ),
				)
			);
			?>
			<section class="section">
				<div class="shell-narrow sidebar-card text-center">
					<span aria-hidden="true" style="margin-inline:auto;display:block;width:0.75rem;height:0.75rem;transform:rotate(45deg);border:1px solid var(--accent);margin-bottom:1.5rem"></span>
					<h2 class="t-h3"><?php esc_html_e( 'سنعود إليك خلال ٢٤ ساعة', 'noor-photography' ); ?></h2>
					<p class="t-lead" style="margin-top:1rem"><?php esc_html_e( 'راجعنا التاريخ المطلوب، وسنرسل لك عرضًا تفصيليًا وخطوات تثبيت الموعد.', 'noor-photography' ); ?></p>
					<div style="margin-top:2.25rem;display:flex;flex-wrap:wrap;justify-content:center;gap:1rem">
						<a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>" class="btn btn-solid"><?php esc_html_e( 'تصفّح الأعمال', 'noor-photography' ); ?></a>
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-outline"><?php esc_html_e( 'إرسال طلب آخر', 'noor-photography' ); ?></a>
					</div>
				</div>
			</section>
		<?php else : ?>
			<?php
			noor_page_header(
				__( 'الحجز', 'noor-photography' ),
				get_the_title(),
				get_the_excerpt() ?: __( 'أخبرنا بتفاصيل المناسبة، ونرسل لك خلال ٢٤ ساعة عرضًا مفصّلًا وتأكيدًا مبدئيًا للتاريخ.', 'noor-photography' ),
				array(
					array(
						'label' => __( 'الرئيسية', 'noor-photography' ),
						'url'   => home_url( '/' ),
					),
					array( 'label' => __( 'الحجز', 'noor-photography' ) ),
				)
			);
			?>
			<section class="section">
				<div class="shell" style="display:grid;gap:4rem;grid-template-columns:1fr">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:grid;gap:1.75rem" novalidate>
						<input type="hidden" name="action" value="noor_booking" />
						<?php wp_nonce_field( 'noor_booking_form', 'noor_booking_nonce' ); ?>
						<div class="honeypot-field"><label>Website<input type="text" name="noor_website" tabindex="-1" autocomplete="off" /></label></div>

						<?php if ( 'error' === $submit_status ) : ?>
							<p class="form-alert" role="alert"><?php esc_html_e( 'تعذّر إرسال الطلب — راجع الحقول المطلوبة أدناه.', 'noor-photography' ); ?></p>
						<?php endif; ?>

						<div style="display:grid;gap:1.75rem;grid-template-columns:1fr 1fr">
							<div><label for="b-name" class="t-caption"><?php esc_html_e( 'الاسم الكامل *', 'noor-photography' ); ?></label><input id="b-name" name="name" class="field" required /></div>
							<div><label for="b-email" class="t-caption"><?php esc_html_e( 'البريد الإلكتروني *', 'noor-photography' ); ?></label><input id="b-email" name="email" type="email" class="field" required /></div>
							<div><label for="b-phone" class="t-caption"><?php esc_html_e( 'رقم الجوال *', 'noor-photography' ); ?></label><input id="b-phone" name="phone" type="tel" class="field" required /></div>
							<div><label for="b-date" class="t-caption"><?php esc_html_e( 'التاريخ المبدئي *', 'noor-photography' ); ?></label><input id="b-date" name="date" type="date" class="field" required /></div>

							<div>
								<label for="b-service" class="t-caption"><?php esc_html_e( 'نوع الخدمة *', 'noor-photography' ); ?></label>
								<select id="b-service" name="service" class="field" required>
									<option value=""><?php esc_html_e( 'اختر خدمة…', 'noor-photography' ); ?></option>
									<?php foreach ( $services as $service ) : ?>
										<option value="<?php echo esc_attr( get_the_title( $service ) ); ?>"><?php echo esc_html( get_the_title( $service ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<?php if ( $packages ) : ?>
							<div>
								<label for="b-pkg" class="t-caption"><?php esc_html_e( 'الباقة المفضّلة', 'noor-photography' ); ?></label>
								<select id="b-pkg" name="package" class="field">
									<?php
									foreach ( $packages as $p ) :
										$pf = noor_get_package_fields( $p->ID );
										?>
										<option value="<?php echo esc_attr( get_the_title( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) . ' — ' . $pf['price'] . ' ' . noor_get_option( 'currency_symbol' ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<?php endif; ?>

							<div style="grid-column:1/-1"><label for="b-location" class="t-caption"><?php esc_html_e( 'مكان التصوير', 'noor-photography' ); ?></label><input id="b-location" name="location" class="field" placeholder="<?php esc_attr_e( 'مثال: قاعة الواحة، الرياض', 'noor-photography' ); ?>" /></div>
							<div style="grid-column:1/-1"><label for="b-notes" class="t-caption"><?php esc_html_e( 'تفاصيل إضافية', 'noor-photography' ); ?></label><textarea id="b-notes" name="notes" rows="5" class="field" placeholder="<?php esc_attr_e( 'عدد الضيوف، الجدول الزمني، أي طلبات خاصة…', 'noor-photography' ); ?>"></textarea></div>

							<div style="grid-column:1/-1;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap">
								<button type="submit" class="btn btn-solid"><?php esc_html_e( 'أرسل طلب الحجز', 'noor-photography' ); ?></button>
								<p class="t-caption"><?php esc_html_e( 'الحقول المعلّمة بـ * مطلوبة', 'noor-photography' ); ?></p>
							</div>
						</div>
					</form>

					<?php if ( $process ) : ?>
					<aside class="sidebar-card" style="height:fit-content">
						<h2 class="t-h3"><?php esc_html_e( 'كيف تسير العملية؟', 'noor-photography' ); ?></h2>
						<ol style="margin-top:2rem;display:flex;flex-direction:column;gap:1.75rem">
							<?php foreach ( $process as $i => $p ) : ?>
							<li style="display:flex;gap:1.25rem">
								<span class="t-caption" style="flex-shrink:0"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span>
									<span style="display:block;font-family:var(--font-display);font-size:1.125rem"><?php echo esc_html( $p[0] ); ?></span>
									<span class="t-lead" style="display:block;margin-top:0.25rem;font-size:0.9rem"><?php echo esc_html( $p[1] ); ?></span>
								</span>
							</li>
							<?php endforeach; ?>
						</ol>
						<p class="t-caption" style="margin-top:2.25rem;border-top:1px solid var(--border);padding-top:1.5rem">
							<?php esc_html_e( 'تفضّل الحديث مباشرة؟', 'noor-photography' ); ?>
							<a href="<?php echo esc_url( noor_get_contact_url() ); ?>" class="link-underline" style="color:var(--foreground)"><?php esc_html_e( 'صفحة التواصل', 'noor-photography' ); ?></a>
						</p>
					</aside>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
