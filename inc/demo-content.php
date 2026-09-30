<?php
/**
 * One-time, admin-triggered demo content importer.
 *
 * This does NOT hardcode anything onto the frontend — it creates real,
 * editable WordPress posts/pages/terms/options through the same APIs an
 * admin would use by hand, so a new install can be previewed and then
 * edited or deleted entirely from wp-admin. Runs once (guarded by an
 * option flag) unless the admin explicitly re-runs it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function noor_demo_content_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	// Read-only: only decides whether to show a success notice, set by
	// noor_handle_import_demo()'s own redirect after it already verified the
	// import form's nonce server-side. Nothing is written based on these.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';
	if ( 'noor-options' !== $page ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$demo_status = isset( $_GET['noor_demo'] ) ? sanitize_key( $_GET['noor_demo'] ) : '';
	if ( 'done' === $demo_status ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'تم استيراد محتوى العرض التجريبي بنجاح. يمكنك الآن تعديله أو حذفه بحرية من كل شاشة.', 'noor-photography' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'noor_demo_content_notice' );

function noor_render_demo_content_box() {
	$imported    = get_option( 'noor_demo_imported' );
	$images_path = NOOR_DIR . '/assets/images';
	$has_images  = is_dir( $images_path ) && count( glob( $images_path . '/*.jpg' ) ) > 0;
	?>
	<h2 class="title"><?php esc_html_e( 'محتوى تجريبي', 'noor-photography' ); ?></h2>
	<p>
		<?php if ( $imported ) : ?>
			<?php
			/* translators: %s: date the demo content was originally imported */
			echo esc_html( sprintf( __( 'تم الاستيراد سابقًا بتاريخ %s. يمكنك إعادة التشغيل لإضافة نسخة جديدة من المحتوى التجريبي دون حذف ما هو موجود.', 'noor-photography' ), $imported ) );
			?>
		<?php else : ?>
			<?php esc_html_e( 'يمكنك استيراد محتوى تجريبي كامل (أعمال، خدمات، باقات، آراء عملاء، وصفحات) لمعاينة التصميم فورًا، ثم تعديله أو حذفه بحرية لاحقًا.', 'noor-photography' ); ?>
		<?php endif; ?>
	</p>
	<?php if ( ! $has_images ) : ?>
		<div class="notice notice-warning inline" style="margin:0 0 14px">
			<p>
				<?php esc_html_e( 'لا توجد صور توضيحية مرفقة بالحزمة الحالية للثيم. سيتم إنشاء المحتوى التجريبي (المشاريع، الخدمات، آراء العملاء...) دون صور مميّزة — أضف صورك الخاصة بحقوق ملكية واضحة لكل عنصر بعد الاستيراد. راجع ملف documentation/licensing.md لمعرفة السبب.', 'noor-photography' ); ?>
			</p>
		</div>
	<?php endif; ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="noor_import_demo" />
		<?php wp_nonce_field( 'noor_import_demo', 'noor_import_demo_nonce' ); ?>
		<?php submit_button( $imported ? __( 'إعادة استيراد المحتوى التجريبي', 'noor-photography' ) : __( 'استيراد المحتوى التجريبي', 'noor-photography' ), 'secondary' ); ?>
	</form>
	<?php
}
add_action( 'noor_options_page_bottom', 'noor_render_demo_content_box' );

/* -------- image sideload helper -------- */

function noor_sideload_theme_image( $filename, $title ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'meta_key'       => '_noor_demo_source',
			'meta_value'     => $filename,
			'post_status'    => 'inherit',
		)
	);
	if ( $existing ) {
		return $existing[0]->ID;
	}

	$file_path = NOOR_DIR . '/assets/images/' . $filename;
	if ( ! file_exists( $file_path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	// wp_upload_bits() is core's sanctioned way to write a file into the
	// uploads directory (unique naming, correct permissions) — preferred
	// over a raw file_put_contents() call.
	// Reading a local file that ships with the theme itself, not a remote
	// URL — wp_remote_get() doesn't apply here.
	$contents = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$uploaded = wp_upload_bits( $filename, null, $contents );
	if ( ! empty( $uploaded['error'] ) ) {
		return 0;
	}

	$filetype    = wp_check_filetype( $uploaded['file'], null );
	$attachment  = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
	);
	$attach_id   = wp_insert_attachment( $attachment, $uploaded['file'] );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded['file'] );
	wp_update_attachment_metadata( $attach_id, $attach_data );
	update_post_meta( $attach_id, '_noor_demo_source', $filename );
	update_post_meta( $attach_id, '_wp_attachment_image_alt', $title );

	return $attach_id;
}

/**
 * Exact-title post lookup, scoped to one post type — replaces the
 * deprecated (since WP 6.2) get_page_by_title().
 */
function noor_find_post_by_title( $title, $post_type ) {
	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'title'          => $title,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		)
	);
	return $query->have_posts() ? $query->posts[0] : null;
}

function noor_get_or_create_page( $title, $template, $content = '', $excerpt = '' ) {
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'title'          => $title,
			'post_status'    => array( 'publish', 'draft' ),
		)
	);
	$args     = array(
		'post_type'    => 'page',
		'post_title'   => $title,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_status'  => 'publish',
	);
	if ( $existing ) {
		$args['ID'] = $existing[0]->ID;
		$page_id    = wp_update_post( $args );
	} else {
		$page_id = wp_insert_post( $args );
	}
	if ( $template ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}
	return $page_id;
}

function noor_handle_import_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'لا تملك صلاحية القيام بذلك.', 'noor-photography' ) );
	}
	if ( ! isset( $_POST['noor_import_demo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noor_import_demo_nonce'] ) ), 'noor_import_demo' ) ) {
		wp_die( esc_html__( 'انتهت صلاحية الطلب، حاول مرة أخرى.', 'noor-photography' ) );
	}

	/* ---- images ---- */
	$images = array(
		'hero'         => noor_sideload_theme_image( 'hero-wedding.jpg', 'ليلٌ ونُور' ),
		'portrait'     => noor_sideload_theme_image( 'portrait-1.jpg', 'وُجوه' ),
		'fashion'      => noor_sideload_theme_image( 'fashion-1.jpg', 'ظِلال حادّة' ),
		'product'      => noor_sideload_theme_image( 'product-1.jpg', 'عِطر' ),
		'event'        => noor_sideload_theme_image( 'event-1.jpg', 'سهرة' ),
		'commercial'   => noor_sideload_theme_image( 'commercial-1.jpg', 'حُجرة' ),
		'photographer' => noor_sideload_theme_image( 'photographer.jpg', 'خالد النُّعمان' ),
		'detail'       => noor_sideload_theme_image( 'detail-1.jpg', 'تفصيل' ),
	);

	/* ---- taxonomy terms ---- */
	$cats     = array(
		'weddings'   => 'أعراس',
		'portrait'   => 'بورتريه',
		'fashion'    => 'أزياء',
		'commercial' => 'تجاري',
		'products'   => 'منتجات',
		'events'     => 'مناسبات',
	);
	$term_ids = array();
	foreach ( $cats as $slug => $name ) {
		$term = term_exists( $name, 'portfolio_category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'portfolio_category', array( 'slug' => $slug ) );
		}
		$term_ids[ $slug ] = is_wp_error( $term ) ? 0 : $term['term_id'];
	}

	/* ---- portfolio projects ---- */
	$projects = array(
		array(
			'title'    => 'ليلٌ ونُور',
			'cat'      => 'weddings',
			'client'   => 'سارة وعبدالله',
			'location' => 'الرياض',
			'year'     => '٢٠٢٥',
			'ratio'    => 'landscape',
			'image'    => 'hero',
			'gallery'  => array( 'hero', 'detail', 'event', 'portrait' ),
			'excerpt'  => 'حفل زفاف في رواق حجري، بين ضوء العصر وحرير الفستان.',
			'content'  => 'بدأ اليوم قبل الشروق بساعتين. أردنا أن نوثّق الطقوس الصغيرة قبل الاحتفال الكبير: يد تُمسك بيد، خطوة مترددة في الرواق، ضحكة تتسلل بين الأعمدة. اعتمدنا على الضوء الطبيعي بالكامل، وحدّدنا لوحة ألوان دافئة قريبة من الحجر والحرير، حتى تبدو الصور وكأنها ذاكرة لا تقرير.',
		),
		array(
			'title'    => 'وُجوه',
			'cat'      => 'portrait',
			'client'   => 'مشروع شخصي',
			'location' => 'استوديو نُور',
			'year'     => '٢٠٢٥',
			'ratio'    => 'portrait',
			'image'    => 'portrait',
			'gallery'  => array( 'portrait', 'photographer', 'fashion' ),
			'excerpt'  => 'سلسلة بورتريهات بالأبيض والأسود بإضاءة مفتاحية واحدة.',
			'content'  => 'سلسلة من ثلاثين بورتريه صُوّرت على مدى عام، بمصدر ضوء واحد وخلفية فحمية. الهدف كان إزالة كل ما يشتّت: لا ديكور، لا ألوان، فقط ملامح وضوء يتراجع ببطء نحو الظل.',
		),
		array(
			'title'    => 'ظِلال حادّة',
			'cat'      => 'fashion',
			'client'   => 'دار أزياء مَها',
			'location' => 'جدة',
			'year'     => '٢٠٢٤',
			'ratio'    => 'portrait',
			'image'    => 'fashion',
			'gallery'  => array( 'fashion', 'product', 'commercial' ),
			'excerpt'  => 'حملة أزياء صيفية تعتمد على ظل الشمس المباشر كعنصر تصميم.',
			'content'  => 'صُوّرت الحملة بالكامل تحت شمس الظهيرة، حيث يتحول الظل إلى خط تصميمي يقسم الكادر. القصّات المعمارية للملابس قابلها تكوين بصري صارم ومساحات فارغة واسعة.',
		),
		array(
			'title'    => 'حُجرة',
			'cat'      => 'commercial',
			'client'   => 'مجموعة ضياء العقارية',
			'location' => 'الرياض',
			'year'     => '٢٠٢٤',
			'ratio'    => 'landscape',
			'image'    => 'commercial',
			'gallery'  => array( 'commercial', 'product', 'event' ),
			'excerpt'  => 'توثيق معماري لمساحة ضيافة بالخرسانة والخشب الدافئ.',
			'content'  => 'مهمة تجارية لتوثيق مساحة ضيافة جديدة. تتبّعنا الضوء على مدار يوم كامل واخترنا ثلاث فترات فقط: صباح ناعم، ظهيرة حادة، ومغيب طويل الظلال.',
		),
		array(
			'title'    => 'عِطر',
			'cat'      => 'products',
			'client'   => 'دار عود',
			'location' => 'استوديو نُور',
			'year'     => '٢٠٢٥',
			'ratio'    => 'square',
			'image'    => 'product',
			'gallery'  => array( 'product', 'detail', 'fashion' ),
			'excerpt'  => 'تصوير منتج فاخر على حجر رملي بإضاءة نافذة مُقلَّدة.',
			'content'  => 'أردنا لمعان الزجاج دون بريق صناعي. بُنيت الإضاءة لمحاكاة نافذة واحدة في غرفة حجرية، مع عاكس واحد فقط لضبط الظل تحت القارورة.',
		),
		array(
			'title'    => 'سهرة',
			'cat'      => 'events',
			'client'   => 'عائلة الحربي',
			'location' => 'الدرعية',
			'year'     => '٢٠٢٤',
			'ratio'    => 'landscape',
			'image'    => 'event',
			'gallery'  => array( 'event', 'hero', 'detail' ),
			'excerpt'  => 'تغطية وثائقية لسهرة عائلية بضوء الشموع.',
			'content'  => 'تغطية وثائقية بالكامل، دون أي إضاءة إضافية. سرعات غالق منخفضة تعمّدنا فيها بقاء أثر الحركة، لتبقى الصور أقرب إلى إحساس الليلة لا إلى تفاصيلها.',
		),
	);

	foreach ( $projects as $p ) {
		$existing = noor_find_post_by_title( $p['title'], 'portfolio' );
		$post_id  = wp_insert_post(
			array(
				'ID'           => $existing ? $existing->ID : 0,
				'post_type'    => 'portfolio',
				'post_title'   => $p['title'],
				'post_excerpt' => $p['excerpt'],
				'post_content' => $p['content'],
				'post_status'  => 'publish',
			)
		);
		wp_set_object_terms( $post_id, (int) $term_ids[ $p['cat'] ], 'portfolio_category' );
		if ( ! empty( $images[ $p['image'] ] ) ) {
			set_post_thumbnail( $post_id, $images[ $p['image'] ] );
		}
		update_post_meta( $post_id, '_noor_client', $p['client'] );
		update_post_meta( $post_id, '_noor_location', $p['location'] );
		update_post_meta( $post_id, '_noor_year', $p['year'] );
		update_post_meta( $post_id, '_noor_ratio', $p['ratio'] );
		$gallery_ids = array_filter(
			array_map(
				function ( $k ) use ( $images ) {
					return $images[ $k ] ?? 0;
				},
				$p['gallery']
			)
		);
		update_post_meta( $post_id, '_noor_gallery', implode( ',', $gallery_ids ) );
	}

	/* ---- services ---- */
	$services = array(
		array(
			'title'    => 'تصوير حفلات الزفاف',
			'short'    => 'تغطية كاملة لليوم من التحضير حتى آخر رقصة.',
			'price'    => 'تبدأ من ١٢٬٠٠٠ ر.س',
			'image'    => 'hero',
			'content'  => 'نوثّق يوم الزفاف بأسلوب وثائقي هادئ لا يقاطع اللحظة. نصل مبكرًا، نتعرف على العائلة، ونصوّر التفاصيل الصغيرة التي تصنع ذاكرة اليوم.',
			'features' => array( 'مصوّران اثنان', 'تغطية حتى ١٢ ساعة', 'ألبوم مطبوع فاخر', 'معرض رقمي خاص' ),
			'includes' => array( 'جلسة ما قبل الزفاف', '٦٠٠+ صورة معالَجة', 'فيلم قصير ٣ دقائق', 'تسليم خلال ٣ أسابيع' ),
			'faq'      => array( array( 'كم تستغرق مدة التسليم؟', 'معرض المعاينة خلال ٧٢ ساعة، والمجموعة الكاملة خلال ثلاثة أسابيع.' ), array( 'هل تسافرون خارج المدينة؟', 'نعم، داخل المملكة وخارجها مع احتساب تكاليف السفر.' ) ),
		),
		array(
			'title'    => 'التصوير الشخصي',
			'short'    => 'بورتريه استوديو أو ضوء طبيعي بملامح هادئة.',
			'price'    => 'تبدأ من ٢٬٥٠٠ ر.س',
			'image'    => 'portrait',
			'content'  => 'جلسة بورتريه مصمّمة حول شخصيتك: نناقش الملابس والمزاج البصري قبل الجلسة، ونصوّر بإيقاع مريح دون استعجال.',
			'features' => array( 'جلسة ٩٠ دقيقة', 'تنسيق مظهر', 'استوديو أو موقع خارجي', '٢٥ صورة معالَجة' ),
			'includes' => array( 'استشارة تحضيرية', 'معرض رقمي', 'خيار طباعة أرشيفية', 'تسليم خلال ٧ أيام' ),
			'faq'      => array( array( 'ماذا ألبس؟', 'نرسل دليلًا بصريًا للألوان والقصّات قبل الجلسة بأسبوع.' ) ),
		),
		array(
			'title'    => 'تصوير الأزياء',
			'short'    => 'حملات تحريرية للعلامات والدور المحلية.',
			'price'    => 'تبدأ من ٩٬٠٠٠ ر.س',
			'image'    => 'fashion',
			'content'  => 'من اللوح المزاجي إلى التسليم النهائي، نبني الحملة مع فريق الإنتاج ونصوّرها بلغة بصرية تليق بالعلامة.',
			'features' => array( 'لوح مزاجي وتخطيط', 'فريق إنتاج كامل', 'استوديو أو موقع', 'رخصة استخدام تجاري' ),
			'includes' => array( 'يوم تصوير كامل', '٤٠ صورة معالَجة', 'نسخ للسوشال ميديا', 'ملفات بدقة الطباعة' ),
			'faq'      => array( array( 'هل توفّرون عارضين؟', 'نعم، عبر شبكة وكالات شريكة حسب اتجاه الحملة.' ) ),
		),
		array(
			'title'    => 'التصوير التجاري',
			'short'    => 'توثيق مساحات وعلامات ومشاريع تجارية.',
			'price'    => 'تبدأ من ٧٬٥٠٠ ر.س',
			'image'    => 'commercial',
			'content'  => 'صور تخدم هوية العلامة: مساحات، فرق عمل، وعمليات تشغيل، بأسلوب نظيف قابل للاستخدام عبر كل القنوات.',
			'features' => array( 'زيارة استكشافية', 'خطة لقطات معتمدة', 'إضاءة احترافية', 'رخصة مفتوحة' ),
			'includes' => array( 'نصف يوم أو يوم كامل', '٣٠ صورة معالَجة', 'تسليم بصيغ متعددة', 'أرشفة لمدة سنة' ),
			'faq'      => array( array( 'هل يشمل التصوير الجوي؟', 'متاح كإضافة مع تصاريح الطيران اللازمة.' ) ),
		),
		array(
			'title'    => 'تصوير المنتجات',
			'short'    => 'صور منتج فاخرة للمتاجر الإلكترونية.',
			'price'    => 'تبدأ من ١٬٨٠٠ ر.س',
			'image'    => 'product',
			'content'  => 'تصوير منتجات على خلفيات نظيفة أو تركيبات مشهدية، بمعايير موحّدة تسهّل استخدامها في المتجر الإلكتروني.',
			'features' => array( 'خلفية بيضاء أو مشهدية', 'زوايا متعددة', 'معالجة دقيقة', 'تسليم سريع' ),
			'includes' => array( 'حتى ١٥ منتجًا', '٣ لقطات لكل منتج', 'قصّ خلفية', 'تسليم خلال ٥ أيام' ),
			'faq'      => array( array( 'هل تستقبلون المنتجات بالشحن؟', 'نعم، مع إعادة الشحن بعد انتهاء الجلسة.' ) ),
		),
		array(
			'title'    => 'جلسات التصوير',
			'short'    => 'جلسات مناسبات صغيرة وخطوبة ومواليد.',
			'price'    => 'تبدأ من ١٬٢٠٠ ر.س',
			'image'    => 'detail',
			'content'  => 'جلسات قصيرة مرنة للمناسبات العائلية الصغيرة، بإعداد بسيط وإيقاع سريع دون التنازل عن الجودة.',
			'features' => array( 'ساعة تصوير', 'موقع واحد', '١٥ صورة معالَجة', 'مشاركة رقمية' ),
			'includes' => array( 'تنسيق موعد مرن', 'معرض خاص', 'تسليم خلال ٤٨ ساعة' ),
			'faq'      => array( array( 'هل يمكن التمديد؟', 'نعم، كل ٣٠ دقيقة إضافية برسوم ثابتة.' ) ),
		),
	);

	foreach ( $services as $i => $s ) {
		$existing = noor_find_post_by_title( $s['title'], 'service' );
		$post_id  = wp_insert_post(
			array(
				'ID'           => $existing ? $existing->ID : 0,
				'post_type'    => 'service',
				'post_title'   => $s['title'],
				'post_excerpt' => $s['short'],
				'post_content' => $s['content'],
				'post_status'  => 'publish',
				'menu_order'   => $i,
			)
		);
		if ( ! empty( $images[ $s['image'] ] ) ) {
			set_post_thumbnail( $post_id, $images[ $s['image'] ] );
		}
		update_post_meta( $post_id, '_noor_price', $s['price'] );
		update_post_meta( $post_id, '_noor_features', implode( "\n", $s['features'] ) );
		update_post_meta( $post_id, '_noor_includes', implode( "\n", $s['includes'] ) );
		$faq_lines = array_map(
			function ( $f ) {
				return $f[0] . ' :: ' . $f[1];
			},
			$s['faq']
		);
		update_post_meta( $post_id, '_noor_faq', implode( "\n", $faq_lines ) );
	}

	/* ---- testimonials ---- */
	$testimonials = array(
		array(
			'name'   => 'سارة العتيبي',
			'role'   => 'عروس · الرياض',
			'image'  => 'portrait',
			'rating' => 5,
			'quote'  => 'لم نشعر بوجود كاميرا طوال اليوم، ومع ذلك وصلتنا صور وثّقت كل شيء. الهدوء في التعامل انعكس على الصور نفسها.',
		),
		array(
			'name'   => 'مها الدوسري',
			'role'   => 'مديرة إبداعية · دار مَها',
			'image'  => 'fashion',
			'rating' => 5,
			'quote'  => 'فريق نُور فهم اتجاه الحملة من الاجتماع الأول. النتيجة كانت أنظف وأجرأ مما تخيّلنا في اللوح المزاجي.',
		),
		array(
			'name'   => 'عبدالرحمن الحربي',
			'role'   => 'مدير تسويق · مجموعة ضياء',
			'image'  => 'commercial',
			'rating' => 5,
			'quote'  => 'احترافية في المواعيد والتسليم، وصور استخدمناها في كل قنواتنا لأكثر من سنة دون أن تفقد قيمتها.',
		),
	);
	foreach ( $testimonials as $t ) {
		$existing = noor_find_post_by_title( $t['name'], 'testimonial' );
		$post_id  = wp_insert_post(
			array(
				'ID'           => $existing ? $existing->ID : 0,
				'post_type'    => 'testimonial',
				'post_title'   => $t['name'],
				'post_content' => $t['quote'],
				'post_status'  => 'publish',
			)
		);
		if ( ! empty( $images[ $t['image'] ] ) ) {
			set_post_thumbnail( $post_id, $images[ $t['image'] ] );
		}
		update_post_meta( $post_id, '_noor_role', $t['role'] );
		update_post_meta( $post_id, '_noor_rating', $t['rating'] );
	}

	/* ---- packages ---- */
	$packages = array(
		array(
			'name'      => 'الباقة الأساسية',
			'price'     => '٦٬٥٠٠',
			'note'      => 'للمناسبات الصغيرة',
			'highlight' => false,
			'features'  => array( '٦ ساعات تغطية', 'مصوّر واحد', '٣٠٠ صورة معالَجة', 'معرض رقمي خاص', 'تسليم خلال ١٤ يومًا' ),
			'compare'   => array( array( 'ساعات التغطية', '٦ ساعات' ), array( 'عدد المصوّرين', '١' ), array( 'الصور المعالَجة', '٣٠٠' ), array( 'ألبوم مطبوع', '—' ), array( 'فيلم قصير', '—' ), array( 'مدة التسليم', '١٤ يومًا' ) ),
		),
		array(
			'name'      => 'الباقة المميّزة',
			'price'     => '١٢٬٠٠٠',
			'note'      => 'الأكثر طلبًا',
			'highlight' => true,
			'features'  => array( '١٠ ساعات تغطية', 'مصوّران', '٦٠٠ صورة معالَجة', 'جلسة ما قبل الزفاف', 'ألبوم مطبوع ٣٠×٣٠', 'تسليم خلال ٢١ يومًا' ),
			'compare'   => array( array( 'ساعات التغطية', '١٠ ساعات' ), array( 'عدد المصوّرين', '٢' ), array( 'الصور المعالَجة', '٦٠٠' ), array( 'ألبوم مطبوع', '٣٠×٣٠' ), array( 'فيلم قصير', '٣ دقائق' ), array( 'مدة التسليم', '٢١ يومًا' ) ),
		),
		array(
			'name'      => 'باقة الاستوديو',
			'price'     => '١٩٬٥٠٠',
			'note'      => 'تغطية شاملة',
			'highlight' => false,
			'features'  => array( 'تغطية يومين', 'ثلاثة مصوّرين', '١٠٠٠+ صورة معالَجة', 'فيلم سينمائي ٥ دقائق', 'ألبومان مطبوعان', 'أرشفة سحابية لخمس سنوات' ),
			'compare'   => array( array( 'ساعات التغطية', 'يومان' ), array( 'عدد المصوّرين', '٣' ), array( 'الصور المعالَجة', '١٠٠٠+' ), array( 'ألبوم مطبوع', 'ألبومان' ), array( 'فيلم قصير', '٥ دقائق' ), array( 'مدة التسليم', '٣٠ يومًا' ) ),
		),
	);
	foreach ( $packages as $i => $p ) {
		$existing = noor_find_post_by_title( $p['name'], 'package' );
		$post_id  = wp_insert_post(
			array(
				'ID'          => $existing ? $existing->ID : 0,
				'post_type'   => 'package',
				'post_title'  => $p['name'],
				'post_status' => 'publish',
				'menu_order'  => $i,
			)
		);
		update_post_meta( $post_id, '_noor_price', $p['price'] );
		update_post_meta( $post_id, '_noor_note', $p['note'] );
		update_post_meta( $post_id, '_noor_highlight', $p['highlight'] );
		update_post_meta( $post_id, '_noor_features', implode( "\n", $p['features'] ) );
		$compare_lines = array_map(
			function ( $c ) {
				return $c[0] . ' :: ' . $c[1];
			},
			$p['compare']
		);
		update_post_meta( $post_id, '_noor_compare', implode( "\n", $compare_lines ) );
	}

	/* ---- theme options ---- */
	update_option(
		NOOR_OPTION,
		array(
			'brand_name_ar'           => 'نُور',
			'brand_name_en'           => 'NOOR',
			'brand_tagline'           => 'استوديو تصوير فوتوغرافي',
			'brand_description'       => 'استوديو تصوير يوثّق اللحظات بلغة بصرية هادئة، بين الضوء والظل، لصور تبقى.',
			'contact_email'           => 'hello@noor.studio',
			'contact_phone'           => '+966 55 214 8890',
			'contact_address'         => 'حي السفارات، الرياض، المملكة العربية السعودية',
			'contact_hours'           => 'الأحد – الخميس · 10:00 ص – 7:00 م',
			'social_links'            => "إنستغرام :: https://instagram.com/noor.studio\nبيهانس :: https://behance.net/noor.studio\nڤيميو :: https://vimeo.com/noor.studio\nلينكدإن :: https://linkedin.com/company/noor-studio",
			'footer_text'             => '© %year% نُور للتصوير الفوتوغرافي. جميع الحقوق محفوظة.',
			'stats'                   => "480 :: + :: مشروع مُنجز\n12 :: :: سنة خبرة\n260 :: + :: عميل\n9 :: :: جائزة وتكريم",
			'photographer_name'       => 'خالد النُّعمان',
			'photographer_bio'        => 'مصوّر فوتوغرافي منذ اثني عشر عامًا، بدأ من التصوير الوثائقي ثم انتقل إلى الأعراس والبورتريه. يؤمن أن الصورة الجيدة تبدأ من علاقة هادئة مع من أمام العدسة، لا من العدسة نفسها.',
			'photographer_image'      => $images['photographer'] ?: '',
			'photographer_experience' => '١٢ سنة',
			'photographer_awards'     => '٩ جوائز',
			'instagram_handle'        => '@noor.studio',
			'instagram_images'        => implode( ',', array_filter( array( $images['hero'], $images['portrait'], $images['fashion'], $images['product'], $images['event'], $images['commercial'], $images['detail'], $images['photographer'] ) ) ),
			'newsletter_note'         => 'النشرة البريدية — مقال واحد شهريًا',
		)
	);

	/* ---- pages ---- */
	$home_id = noor_get_or_create_page( 'الرئيسية', '', 'استوديو تصوير يوثّق اللحظات بلغة بصرية هادئة، بين الضوء والظل، لصور تبقى.' );
	if ( ! empty( $images['hero'] ) ) {
		set_post_thumbnail( $home_id, $images['hero'] );
	}
	update_post_meta( $home_id, '_noor_hero_eyebrow', 'استوديو نُور · الرياض' );
	update_post_meta( $home_id, '_noor_hero_caption', 'ليلٌ ونُور · الرياض ٢٠٢٥' );
	update_post_meta( $home_id, '_noor_manifesto', 'لا نطارد اللحظة، ننتظرها. نصوّر بضوء موجود أصلًا في المكان، ونترك للصورة مساحة كي تتنفّس — لأن أجمل ما في الصورة هو ما لم يُقَل فيها.' );
	update_post_meta( $home_id, '_noor_cta_heading', 'لنصنع صورًا تستحق أن تُحفظ.' );
	update_post_meta( $home_id, '_noor_cta_image', $images['detail'] );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );

	$about_id = noor_get_or_create_page(
		'عن الاستوديو',
		'template-about.php',
		"بدأت التصوير عام ٢٠١٣ بكاميرا مستعملة وفيلم منتهي الصلاحية. عملت لسنوات في التصوير الوثائقي قبل أن أنتقل إلى الأعراس، وهناك اكتشفت أن أفضل ما أجيده هو الانتظار: أن أبقى في الزاوية حتى تحدث اللحظة من تلقاء نفسها.\n\nاليوم يضم الاستوديو فريقًا من أربعة أشخاص، ونعمل على عدد محدود من المشاريع سنويًا حتى نمنح كل مشروع الوقت الذي يستحقه.",
		'اثنا عشر عامًا خلف العدسة، وضوءٌ واحد لم يتغيّر.'
	);
	update_post_meta( $about_id, '_noor_process', "الاستشارة :: نتحدث عن المناسبة والمزاج البصري والتوقيت، ونحدد نطاق العمل.\nالتحضير :: لوح مزاجي، خطة لقطات، زيارة استكشافية للموقع عند الحاجة.\nالتصوير :: يوم التصوير بإيقاع هادئ، بأقل تدخل ممكن في مجرى اللحظة.\nالمعالجة :: اختيار وتنقيح بلوحة ألوان ثابتة تحافظ على هوية الصورة.\nالتسليم :: معرض رقمي خاص، ملفات بدقة الطباعة، وخيارات ألبوم أرشيفي." );
	update_post_meta( $about_id, '_noor_awards', "٢٠٢٥ :: جائزة الصورة الوثائقية — المركز الأول\n٢٠٢٤ :: اختيار هيئة الأفلام ضمن أفضل ٢٠ مصوّرًا\n٢٠٢٣ :: نشر في مجلة «إطار» — ملف خاص\n٢٠٢٢ :: معرض «وُجوه» الفردي — الرياض" );
	update_post_meta( $about_id, '_noor_clients', "دار عود\nمجموعة ضياء\nدار مَها\nهيئة الأفلام\nمجلة إطار\nبيت الحرفة" );

	$pricing_id = noor_get_or_create_page( 'الباقات', 'template-pricing.php', '', 'اختر الباقة الأقرب لاحتياجك، أو اطلب باقة مخصّصة تُبنى حول مناسبتك.' );
	update_post_meta( $pricing_id, '_noor_faq', "كيف يتم تثبيت الموعد؟ :: عربون ٣٠٪ غير مسترد يثبّت التاريخ في التقويم، والمبلغ المتبقي يُسدَّد قبل موعد التصوير بأسبوع.\nهل الأسعار شاملة الضريبة؟ :: الأسعار المعروضة قبل ضريبة القيمة المضافة، وتُضاف في الفاتورة النهائية.\nهل يمكن تخصيص باقة؟ :: نعم، نبني باقة خاصة حسب عدد الساعات والمواقع ومخرجات التسليم المطلوبة." );

	$contact_id = noor_get_or_create_page( 'تواصل', 'template-contact.php', '', 'بابنا مفتوح بموعد مسبق، ونرد على كل رسالة خلال يوم عمل واحد.' );
	update_post_meta( $contact_id, '_noor_faq', "هل الزيارة تحتاج موعدًا مسبقًا؟ :: نعم، نستقبل الزيارات بموعد لضمان توفّر الاستوديو ومساحة العرض.\nكم تستغرقون للرد؟ :: نرد على الرسائل خلال يوم عمل واحد، وعلى طلبات الحجز خلال ٢٤ ساعة." );

	$booking_id = noor_get_or_create_page( 'الحجز', 'template-booking.php', '', 'أخبرنا بتفاصيل المناسبة، ونرسل لك خلال ٢٤ ساعة عرضًا مفصّلًا وتأكيدًا مبدئيًا للتاريخ.' );
	update_post_meta( $booking_id, '_noor_process', "إرسال الطلب :: تملأ النموذج بتفاصيل المناسبة والتاريخ المبدئي.\nمكالمة تعارف :: نتحدث ٢٠ دقيقة لفهم التوقعات والأسلوب البصري.\nتثبيت الموعد :: عربون ٣٠٪ يحجز التاريخ في تقويم الاستوديو.\nيوم التصوير :: خطة لقطات معتمدة مسبقًا وفريق كامل في الموعد." );

	/* ---- primary nav menu ---- */
	$menu_name     = 'القائمة الرئيسية';
	$menu_id       = 0;
	$existing_menu = wp_get_nav_menu_object( $menu_name );
	if ( $existing_menu ) {
		$menu_id = $existing_menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $menu_name );
	}
	$menu_items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $menu_items ) ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'الرئيسية',
				'menu-item-url'    => home_url( '/' ),
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'الأعمال',
				'menu-item-url'    => get_post_type_archive_link( 'portfolio' ),
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'الخدمات',
				'menu-item-url'    => get_post_type_archive_link( 'service' ),
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'عن الاستوديو',
				'menu-item-url'    => get_permalink( $about_id ),
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'الباقات',
				'menu-item-url'    => get_permalink( $pricing_id ),
				'menu-item-status' => 'publish',
			)
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'تواصل',
				'menu-item-url'    => get_permalink( $contact_id ),
				'menu-item-status' => 'publish',
			)
		);
	}
	$locations            = get_theme_mod( 'nav_menu_locations' );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'noor_demo_imported', current_time( 'mysql' ) );

	wp_safe_redirect( admin_url( 'admin.php?page=noor-options&noor_demo=done' ) );
	exit;
}
add_action( 'admin_post_noor_import_demo', 'noor_handle_import_demo' );
