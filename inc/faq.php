<?php
/**
 * FAQ page helpers: platform reviews (AJAX), categories meta, and page bootstrap.
 *
 * Platform reviews are stored as native WordPress comments on the FAQ page with:
 * - comment meta `cf_rating` (1–5)
 * - comment meta `cf_review_categories` (array of category slugs)
 * - comment meta `cf_platform_review` = 1
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Review topic checkboxes for the FAQ platform review form.
 *
 * @return array<string, string> slug => label
 */
function collective_finity_platform_review_categories() {
	return array(
		'design'    => __( 'Design & Visual Appeal', 'collective-finity' ),
		'ease'      => __( 'Ease of Use', 'collective-finity' ),
		'features'  => __( 'Feature Set', 'collective-finity' ),
		'audio'     => __( 'Audio Quality', 'collective-finity' ),
		'community' => __( 'Community & Support', 'collective-finity' ),
		'overall'   => __( 'Overall Experience', 'collective-finity' ),
	);
}

/**
 * Resolve the published FAQ page (by slug or template).
 *
 * @return WP_Post|null
 */
function collective_finity_get_faq_page() {
	$page = get_page_by_path( 'faq' );
	if ( $page && 'publish' === $page->post_status ) {
		return $page;
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 'page-faq.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	return ! empty( $pages[0] ) ? $pages[0] : null;
}

/**
 * Whether a post/page is the FAQ page used for platform reviews.
 *
 * @param int|WP_Post|null $post Post.
 * @return bool
 */
function collective_finity_is_faq_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'page' !== $post->post_type ) {
		return false;
	}

	if ( 'faq' === $post->post_name || 'page-faq.php' === get_page_template_slug( $post->ID ) ) {
		return true;
	}

	return false;
}

/**
 * Create the FAQ page when missing (idempotent).
 */
function collective_finity_create_faq_page() {
	if ( get_page_by_path( 'faq', OBJECT, 'page' ) ) {
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'FAQ', 'collective-finity' ),
			'post_name'    => 'faq',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'comment_status' => 'open',
			'meta_input'   => array(
				'_wp_page_template' => 'page-faq.php',
			),
		),
		true
	);

	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-faq.php' );
	}
}

/**
 * Run FAQ page creation once.
 */
function collective_finity_maybe_create_faq_page() {
	if ( get_option( 'cf_faq_page_created' ) ) {
		return;
	}

	collective_finity_create_faq_page();
	update_option( 'cf_faq_page_created', 1 );
}
add_action( 'after_switch_theme', 'collective_finity_maybe_create_faq_page' );
add_action( 'admin_init', 'collective_finity_maybe_create_faq_page' );

/**
 * Keep comments open on the FAQ page so platform reviews work.
 *
 * @param bool $open    Whether comments are open.
 * @param int  $post_id Post ID.
 * @return bool
 */
function collective_finity_force_comments_open_for_faq( $open, $post_id ) {
	if ( collective_finity_is_faq_page( $post_id ) ) {
		return true;
	}
	return $open;
}
add_filter( 'comments_open', 'collective_finity_force_comments_open_for_faq', 10, 2 );

/**
 * Sanitize submitted category slugs against the allowed list.
 *
 * @param mixed $raw Raw POST value.
 * @return string[]
 */
function collective_finity_sanitize_review_categories( $raw ) {
	$allowed = array_keys( collective_finity_platform_review_categories() );
	$raw     = is_array( $raw ) ? $raw : array();
	$out     = array();

	foreach ( $raw as $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( in_array( $slug, $allowed, true ) ) {
			$out[] = $slug;
		}
	}

	return array_values( array_unique( $out ) );
}

/**
 * Format category labels for a platform review comment.
 *
 * @param int $comment_id Comment ID.
 * @return string[]
 */
function collective_finity_get_review_category_labels( $comment_id ) {
	$slugs  = get_comment_meta( $comment_id, 'cf_review_categories', true );
	$slugs  = is_array( $slugs ) ? $slugs : array();
	$map    = collective_finity_platform_review_categories();
	$labels = array();

	foreach ( $slugs as $slug ) {
		if ( isset( $map[ $slug ] ) ) {
			$labels[] = $map[ $slug ];
		}
	}

	return $labels;
}

/**
 * Persist platform-review categories when submitted via classic comment POST.
 *
 * @param int        $comment_id Comment ID.
 * @param int|string $approved   Approval status.
 */
function collective_finity_save_platform_review_meta( $comment_id, $approved ) {
	unset( $approved );

	if ( empty( $_POST['cf_platform_review'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || ! collective_finity_is_faq_page( (int) $comment->comment_post_ID ) ) {
		return;
	}

	add_comment_meta( $comment_id, 'cf_platform_review', 1, true );

	$categories = array();
	if ( isset( $_POST['cf_review_categories'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$categories = collective_finity_sanitize_review_categories( wp_unslash( $_POST['cf_review_categories'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
	if ( ! empty( $categories ) ) {
		add_comment_meta( $comment_id, 'cf_review_categories', $categories, true );
	}
}
add_action( 'comment_post', 'collective_finity_save_platform_review_meta', 20, 2 );

/**
 * Build HTML for a single platform review card.
 *
 * @param WP_Comment $comment Comment object.
 * @return string
 */
function collective_finity_platform_review_card_markup( $comment ) {
	$rating     = (int) get_comment_meta( $comment->comment_ID, 'cf_rating', true );
	$labels     = collective_finity_get_review_category_labels( $comment->comment_ID );
	$date_label = sprintf(
		/* translators: %s: human-readable time difference, e.g. "3 days". */
		__( '%s ago', 'collective-finity' ),
		human_time_diff( (int) get_comment_time( 'U', true, false, $comment ), time() )
	);

	ob_start();
	?>
	<article class="cf-faq-review" id="comment-<?php echo esc_attr( (string) $comment->comment_ID ); ?>">
		<?php echo collective_finity_review_avatar( $comment, 36 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="cf-faq-review__body">
			<div class="cf-faq-review__head">
				<span class="cf-faq-review__name"><?php echo esc_html( $comment->comment_author ); ?></span>
				<?php if ( $rating >= 1 && $rating <= 5 ) : ?>
					<?php echo collective_finity_stars_markup( $rating, 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
				<span class="cf-faq-review__date"><?php echo esc_html( $date_label ); ?></span>
			</div>
			<?php if ( ! empty( $labels ) ) : ?>
				<ul class="cf-faq-review__cats" aria-label="<?php esc_attr_e( 'Review topics', 'collective-finity' ); ?>">
					<?php foreach ( $labels as $label ) : ?>
						<li><?php echo esc_html( $label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="cf-faq-review__text"><?php echo wp_kses_post( wpautop( $comment->comment_content ) ); ?></div>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * AJAX: submit a platform review on the FAQ page (logged-in users only).
 */
function collective_finity_ajax_submit_platform_review() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array( 'message' => __( 'Please log in to leave a review.', 'collective-finity' ) ),
			401
		);
	}

	check_ajax_referer( 'cf_platform_review', 'nonce' );

	$faq_page = collective_finity_get_faq_page();
	if ( ! $faq_page ) {
		wp_send_json_error(
			array( 'message' => __( 'FAQ page not found.', 'collective-finity' ) ),
			404
		);
	}

	$post_id = isset( $_POST['comment_post_ID'] ) ? (int) $_POST['comment_post_ID'] : 0;
	if ( $post_id !== (int) $faq_page->ID ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid review target.', 'collective-finity' ) ),
			400
		);
	}

	if ( ! comments_open( $post_id ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Reviews are closed.', 'collective-finity' ) ),
			403
		);
	}

	$rating = isset( $_POST['cf_rating'] ) ? (int) $_POST['cf_rating'] : 0;
	if ( $rating < 1 || $rating > 5 ) {
		wp_send_json_error(
			array( 'message' => __( 'Please select a star rating from 1 to 5.', 'collective-finity' ) ),
			400
		);
	}

	$message = isset( $_POST['comment'] ) ? trim( wp_unslash( $_POST['comment'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$message = sanitize_textarea_field( $message );
	if ( strlen( $message ) < 10 ) {
		wp_send_json_error(
			array( 'message' => __( 'Please write at least 10 characters.', 'collective-finity' ) ),
			400
		);
	}

	$categories = array();
	if ( isset( $_POST['cf_review_categories'] ) ) {
		$categories = collective_finity_sanitize_review_categories( wp_unslash( $_POST['cf_review_categories'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	$user = wp_get_current_user();

	$comment_data = array(
		'comment_post_ID'      => $post_id,
		'comment_content'      => $message,
		'comment_author'       => $user->display_name ? $user->display_name : $user->user_login,
		'comment_author_email' => $user->user_email,
		'comment_author_url'   => $user->user_url,
		'user_id'              => (int) $user->ID,
		'comment_type'         => 'comment',
		'comment_parent'       => 0,
		// Platform reviews await moderation unless the author can moderate.
		'comment_approved'     => current_user_can( 'moderate_comments' ) ? 1 : 0,
	);

	$comment_id = wp_new_comment( $comment_data, true );
	if ( is_wp_error( $comment_id ) ) {
		wp_send_json_error(
			array( 'message' => $comment_id->get_error_message() ),
			400
		);
	}

	add_comment_meta( $comment_id, 'cf_rating', $rating, true );
	add_comment_meta( $comment_id, 'cf_platform_review', 1, true );
	if ( ! empty( $categories ) ) {
		add_comment_meta( $comment_id, 'cf_review_categories', $categories, true );
	}

	$comment  = get_comment( $comment_id );
	$approved = $comment && '1' === (string) $comment->comment_approved;

	$response = array(
		'message'  => $approved
			? __( 'Thanks — your review is live.', 'collective-finity' )
			: __( 'Thanks — your review was submitted and is awaiting approval.', 'collective-finity' ),
		'approved' => $approved,
	);

	if ( $approved && $comment ) {
		$response['html'] = collective_finity_platform_review_card_markup( $comment );
	}

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_cf_submit_platform_review', 'collective_finity_ajax_submit_platform_review' );

/**
 * Accordion Q&A content for the FAQ page template.
 *
 * @return array<int, array{title:string, eyebrow:string, items:array<int, array{question:string, answer:string}>}>
 */
function collective_finity_get_faq_sections() {
	return array(
		array(
			'eyebrow' => __( '01 / ABOUT', 'collective-finity' ),
			'title'   => __( 'About FF Collective', 'collective-finity' ),
			'items'   => array(
				array(
					'question' => __( 'What is Collective Finity?', 'collective-finity' ),
					'answer'   => __( 'Collective Finity is a cinematic music platform and creative hub built around AI-assisted production. It combines a curated music library, articles on the craft, and a community path for listeners and creators who care about music that feels intentional — not generic.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Who is behind Collective Finity?', 'collective-finity' ),
					'answer'   => __( 'Right now it is founded and run by Wael Safan — writing, producing, and publishing the catalog and articles. The “Collective” name reflects the long-term vision: a platform other AI-assisted artists can eventually join.', 'collective-finity' ),
				),
				array(
					'question' => __( 'What makes Collective Finity different?', 'collective-finity' ),
					'answer'   => __( 'The focus is curation and ear, not volume. Tracks are selected from extensive generation and iteration, shaped with a cinematic, story-driven approach across genres — and paired with transparent writing about how the music is actually made.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Is everything free to listen to?', 'collective-finity' ),
					'answer'   => __( 'Yes. The music library and articles are free to explore. Optional support (such as donations) helps sustain the project, but listening and reading do not require payment.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Will other artists be able to join?', 'collective-finity' ),
					'answer'   => __( 'That is the plan. The near-term focus is growing the catalog, articles, and community on this site. Longer term, the goal is a dedicated app where AI-assisted artists can publish and reach listeners.', 'collective-finity' ),
				),
			),
		),
		array(
			'eyebrow' => __( '02 / USING THE SITE', 'collective-finity' ),
			'title'   => __( 'How to Use', 'collective-finity' ),
			'items'   => array(
				array(
					'question' => __( 'How do I find and play music?', 'collective-finity' ),
					'answer'   => __( 'Open Music Library or Albums from the footer or navigation, browse tracks by release or genre, and use the persistent player at the bottom of the page to play, pause, and move between tracks without leaving the page.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Can I create playlists?', 'collective-finity' ),
					'answer'   => __( 'Yes — when you are logged in, you can save tracks to playlists from the player and library UI. Guests are prompted to register or log in so playlists stay linked to their account.', 'collective-finity' ),
				),
				array(
					'question' => __( 'How do favorites work?', 'collective-finity' ),
					'answer'   => __( 'Logged-in members can favorite tracks and revisit them from their profile. Favorites sync with your account so you can pick up where you left off across sessions.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Where can I read about the production process?', 'collective-finity' ),
					'answer'   => __( 'Visit the Blog for articles on prompts, workflow decisions, and what actually improves AI-assisted music results — practical notes from building the Collective Finity catalog.', 'collective-finity' ),
				),
				array(
					'question' => __( 'How do I join the community?', 'collective-finity' ),
					'answer'   => __( 'Create an account, explore Join Community for Discord and social spaces, and leave reviews on articles or this FAQ when you want to share feedback about the platform.', 'collective-finity' ),
				),
			),
		),
		array(
			'eyebrow' => __( '03 / FEATURES', 'collective-finity' ),
			'title'   => __( 'Features & Technical', 'collective-finity' ),
			'items'   => array(
				array(
					'question' => __( 'What audio quality should I expect?', 'collective-finity' ),
					'answer'   => __( 'Tracks are streamed in high-quality web-friendly formats optimized for the on-site player. Exact bitrate can vary by release, but the goal is clear, full listening suitable for focused listening sessions.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Which browsers are supported?', 'collective-finity' ),
					'answer'   => __( 'Collective Finity works best in current versions of Chrome, Firefox, Edge, and Safari on desktop and mobile. For the most reliable playback and account features, keep your browser up to date.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Does the site work on mobile?', 'collective-finity' ),
					'answer'   => __( 'Yes. The layout adapts to smaller screens, and the footer player remains available so you can browse and listen on phones and tablets.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Is there a public API?', 'collective-finity' ),
					'answer'   => __( 'There is no general public API for third-party apps yet. The site’s music and account features are designed for use on Collective Finity itself. If API access becomes available later, it will be announced on the site.', 'collective-finity' ),
				),
				array(
					'question' => __( 'Can I download tracks?', 'collective-finity' ),
					'answer'   => __( 'Streaming is the primary experience today. Download or licensing options may expand as the platform grows; until then, enjoy listening on-site and share the project with others who care about cinematic AI-assisted music.', 'collective-finity' ),
				),
			),
		),
	);
}
