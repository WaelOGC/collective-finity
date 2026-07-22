<?php
/**
 * Template Name: All Reviews
 * Description: Combined FAQ (platform) and Blog (article) reviews with rating and type filters, paginated.
 *
 * @package Collective_Finity
 */

$cf_filter_rating = isset( $_GET['rating'] ) ? absint( $_GET['rating'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$cf_filter_type   = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$cf_paged         = isset( $_GET['reviews_page'] ) ? max( 1, absint( $_GET['reviews_page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( $cf_filter_rating > 5 ) {
	$cf_filter_rating = 0;
}

if ( ! in_array( $cf_filter_type, array( 'all', 'faq', 'article' ), true ) ) {
	$cf_filter_type = 'all';
}

$cf_per_page = 32;

$cf_all_comments = get_comments(
	array(
		'status'     => 'approve',
		'type'       => 'comment',
		'number'     => 500,
		'orderby'    => 'comment_date',
		'order'      => 'DESC',
		'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => 'cf_rating',
				'value'   => array( 1, 5 ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			),
		),
	)
);

$cf_review_cards = array();
foreach ( $cf_all_comments as $cf_review_comment ) {
	$cf_review_post = get_post( (int) $cf_review_comment->comment_post_ID );
	if ( ! $cf_review_post ) {
		continue;
	}

	$cf_is_article  = ( 'post' === $cf_review_post->post_type );
	$cf_is_platform = ( 'page' === $cf_review_post->post_type && function_exists( 'collective_finity_is_faq_page' ) && collective_finity_is_faq_page( $cf_review_post ) );

	// Article reviews + FAQ platform reviews only.
	if ( ! $cf_is_article && ! $cf_is_platform ) {
		continue;
	}

	$cf_review_rating = (int) get_comment_meta( $cf_review_comment->comment_ID, 'cf_rating', true );
	if ( $cf_review_rating < 1 || $cf_review_rating > 5 ) {
		continue;
	}

	$cf_review_cards[] = array(
		'rating'  => $cf_review_rating,
		'excerpt' => wp_trim_words( wp_strip_all_tags( $cf_review_comment->comment_content ), 22, '&hellip;' ),
		'author'  => $cf_review_comment->comment_author,
		'url'     => get_comment_link( $cf_review_comment ),
		'source'  => $cf_is_platform ? 'platform' : 'article',
		'comment' => $cf_review_comment,
	);
}

// Apply rating filter (exact match).
if ( $cf_filter_rating > 0 ) {
	$cf_review_cards = array_values(
		array_filter(
			$cf_review_cards,
			static function ( $cf_card ) use ( $cf_filter_rating ) {
				return (int) $cf_card['rating'] === $cf_filter_rating;
			}
		)
	);
}

// Apply type filter.
if ( 'faq' === $cf_filter_type ) {
	$cf_review_cards = array_values(
		array_filter(
			$cf_review_cards,
			static function ( $cf_card ) {
				return 'platform' === $cf_card['source'];
			}
		)
	);
} elseif ( 'article' === $cf_filter_type ) {
	$cf_review_cards = array_values(
		array_filter(
			$cf_review_cards,
			static function ( $cf_card ) {
				return 'article' === $cf_card['source'];
			}
		)
	);
}

$cf_total_reviews = count( $cf_review_cards );
$cf_total_pages   = max( 1, (int) ceil( $cf_total_reviews / $cf_per_page ) );
if ( $cf_paged > $cf_total_pages ) {
	$cf_paged = $cf_total_pages;
}

$cf_page_cards = array_slice( $cf_review_cards, ( $cf_paged - 1 ) * $cf_per_page, $cf_per_page );

/**
 * Build a reviews page filter URL preserving the other active filter.
 *
 * @param int    $rating Rating filter (0 = all).
 * @param string $type   Type filter ('all' | 'faq' | 'article').
 * @return string
 */
$cf_reviews_filter_url = static function ( $rating, $type ) {
	$args = array();
	if ( (int) $rating > 0 ) {
		$args['rating'] = (int) $rating;
	}
	if ( 'all' !== $type ) {
		$args['type'] = $type;
	}
	$url = get_permalink();
	return empty( $args ) ? $url : add_query_arg( $args, $url );
};

$cf_page_url = get_permalink();
$cf_add_args = array();
if ( $cf_filter_rating > 0 ) {
	$cf_add_args['rating'] = $cf_filter_rating;
}
if ( 'all' !== $cf_filter_type ) {
	$cf_add_args['type'] = $cf_filter_type;
}

get_header();
?>

<main id="primary" class="site-main cf-page-shell cf-reviews-page">
	<div class="cf-page-container">

		<section class="cf-reviews-hero" aria-labelledby="cf-reviews-hero-heading">
			<div class="cf-reviews-hero__border" aria-hidden="true"></div>
			<div class="cf-reviews-hero__center-glow" aria-hidden="true"></div>
			<div class="cf-reviews-hero__content">
				<span class="cf-reviews-page-eyebrow"><?php esc_html_e( 'COMMUNITY', 'collective-finity' ); ?></span>
				<h1 id="cf-reviews-hero-heading" class="cf-reviews-page-title"><?php the_title(); ?></h1>
				<p class="cf-reviews-page-lead">
					<?php esc_html_e( 'Browse every platform and article review — filter by rating or type.', 'collective-finity' ); ?>
				</p>

				<div class="cf-reviews-filters" role="search" aria-label="<?php esc_attr_e( 'Review filters', 'collective-finity' ); ?>">
					<div class="cf-reviews-filter-group">
						<span class="cf-reviews-filter-label"><?php esc_html_e( 'Rating', 'collective-finity' ); ?></span>
						<div class="cf-reviews-filter-pills" role="list">
							<a
								class="cf-reviews-filter-pill<?php echo 0 === $cf_filter_rating ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( $cf_reviews_filter_url( 0, $cf_filter_type ) ); ?>"
								role="listitem"
								<?php echo 0 === $cf_filter_rating ? 'aria-current="true"' : ''; ?>
							><?php esc_html_e( 'All', 'collective-finity' ); ?></a>
							<?php for ( $cf_star = 5; $cf_star >= 1; $cf_star-- ) : ?>
								<a
									class="cf-reviews-filter-pill<?php echo $cf_filter_rating === $cf_star ? ' is-active' : ''; ?>"
									href="<?php echo esc_url( $cf_reviews_filter_url( $cf_star, $cf_filter_type ) ); ?>"
									role="listitem"
									<?php echo $cf_filter_rating === $cf_star ? 'aria-current="true"' : ''; ?>
								><?php echo esc_html( (string) $cf_star ); ?> ★</a>
							<?php endfor; ?>
						</div>
					</div>

					<div class="cf-reviews-filter-group">
						<span class="cf-reviews-filter-label"><?php esc_html_e( 'Type', 'collective-finity' ); ?></span>
						<div class="cf-reviews-filter-pills" role="list">
							<a
								class="cf-reviews-filter-pill<?php echo 'all' === $cf_filter_type ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( $cf_reviews_filter_url( $cf_filter_rating, 'all' ) ); ?>"
								role="listitem"
								<?php echo 'all' === $cf_filter_type ? 'aria-current="true"' : ''; ?>
							><?php esc_html_e( 'All', 'collective-finity' ); ?></a>
							<a
								class="cf-reviews-filter-pill<?php echo 'faq' === $cf_filter_type ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( $cf_reviews_filter_url( $cf_filter_rating, 'faq' ) ); ?>"
								role="listitem"
								<?php echo 'faq' === $cf_filter_type ? 'aria-current="true"' : ''; ?>
							><?php esc_html_e( 'FAQ', 'collective-finity' ); ?></a>
							<a
								class="cf-reviews-filter-pill<?php echo 'article' === $cf_filter_type ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( $cf_reviews_filter_url( $cf_filter_rating, 'article' ) ); ?>"
								role="listitem"
								<?php echo 'article' === $cf_filter_type ? 'aria-current="true"' : ''; ?>
							><?php esc_html_e( 'Articles', 'collective-finity' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php if ( ! empty( $cf_page_cards ) ) : ?>
			<div class="cf-reviews-page-grid">
				<?php foreach ( $cf_page_cards as $cf_card ) : ?>
					<a class="cf-home-review-card" href="<?php echo esc_url( $cf_card['url'] ); ?>">
						<?php if ( 'platform' === $cf_card['source'] ) : ?>
							<span class="cf-home-review-tag cf-home-review-tag--platform"><?php esc_html_e( 'PLATFORM', 'collective-finity' ); ?></span>
						<?php else : ?>
							<span class="cf-home-review-tag cf-home-review-tag--article"><?php esc_html_e( 'ARTICLE', 'collective-finity' ); ?></span>
						<?php endif; ?>
						<div class="cf-home-review-stars">
							<?php echo collective_finity_stars_markup( $cf_card['rating'], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<p class="cf-home-review-excerpt"><?php echo esc_html( $cf_card['excerpt'] ); ?></p>
						<div class="cf-home-review-author">
							<?php echo collective_finity_review_avatar( $cf_card['comment'], 36 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="cf-home-review-author__name"><?php echo esc_html( $cf_card['author'] ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>

			<?php if ( $cf_total_pages > 1 ) : ?>
				<?php
				$cf_links = paginate_links(
					array(
						'base'      => esc_url_raw( untrailingslashit( $cf_page_url ) ) . '%_%',
						'format'    => '?reviews_page=%#%',
						'current'   => $cf_paged,
						'total'     => $cf_total_pages,
						'type'      => 'array',
						'add_args'  => $cf_add_args,
						'prev_text' => __( '&larr; Prev', 'collective-finity' ),
						'next_text' => __( 'Next &rarr;', 'collective-finity' ),
					)
				);
				?>
				<?php if ( $cf_links ) : ?>
					<nav class="cf-reviews-pagination" aria-label="<?php esc_attr_e( 'Reviews pagination', 'collective-finity' ); ?>">
						<?php foreach ( $cf_links as $cf_link ) : ?>
							<?php echo wp_kses_post( $cf_link ); ?>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
			<?php endif; ?>
		<?php else : ?>
			<div class="cf-home-empty"><?php esc_html_e( 'No reviews match this filter yet.', 'collective-finity' ); ?></div>
		<?php endif; ?>

	</div>
</main>

<style>
	.cf-reviews-page.cf-page-shell {
		padding: 2.5rem 5px 5px;
		max-width: 100%;
		min-width: 0;
		box-sizing: border-box;
	}

	.cf-reviews-page .cf-page-container {
		max-width: 100%;
	}

	@property --cf-hero-border-angle {
		syntax: '<angle>';
		initial-value: 0deg;
		inherits: false;
	}

	.cf-reviews-hero {
		position: relative;
		text-align: center;
		padding: clamp(48px, 7vw, 80px) clamp(20px, 4vw, 40px) clamp(56px, 8vw, 88px);
		border-radius: 18px;
		background: #0B0B0B;
		border: 1px solid rgba(30, 30, 30, 0.9);
		overflow: hidden;
		min-width: 0;
		max-width: 100%;
		width: 100%;
		margin: 0 auto 32px;
		box-sizing: border-box;
	}

	.cf-reviews-hero__border {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		padding: 1.5px;
		pointer-events: none;
		z-index: 2;
		background: conic-gradient(
			from var(--cf-hero-border-angle),
			transparent 0%,
			transparent 72%,
			rgba(255, 183, 0, 0.05) 80%,
			rgba(255, 183, 0, 0.35) 86%,
			var(--cf-accent, #FFB700) 90%,
			#FFD060 93%,
			rgba(255, 183, 0, 0.2) 96%,
			transparent 100%
		);
		-webkit-mask:
			linear-gradient(#fff 0 0) content-box,
			linear-gradient(#fff 0 0);
		-webkit-mask-composite: xor;
		mask-composite: exclude;
		animation: cfReviewsBorderTravel 5.5s linear infinite;
		filter: drop-shadow(0 0 6px rgba(255, 183, 0, 0.35));
	}

	@keyframes cfReviewsBorderTravel {
		to { --cf-hero-border-angle: 360deg; }
	}

	.cf-reviews-hero__center-glow {
		position: absolute;
		left: 50%;
		top: 46%;
		width: min(70%, 520px);
		aspect-ratio: 1;
		transform: translate(-50%, -50%);
		pointer-events: none;
		z-index: 0;
		border-radius: 50%;
		background: radial-gradient(
			circle,
			rgba(255, 183, 0, 0.14) 0%,
			rgba(255, 183, 0, 0.05) 38%,
			transparent 70%
		);
		animation: cfReviewsCenterGlow 8.2s ease-in-out infinite;
		will-change: transform, opacity;
	}

	@keyframes cfReviewsCenterGlow {
		0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
		50% { transform: translate(-50%, -50%) scale(1.08); opacity: 0.7; }
	}

	.cf-reviews-hero__content {
		position: relative;
		z-index: 3;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 14px;
	}

	.cf-reviews-page-eyebrow {
		margin: 0;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.08em;
		color: var(--cf-accent, #ffb700);
	}

	.cf-reviews-page-title {
		margin: 0;
		font-size: clamp(28px, 4vw, 36px);
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
	}

	.cf-reviews-page-lead {
		margin: 0;
		max-width: 540px;
		font-size: 14.5px;
		line-height: 1.6;
		color: var(--cf-text-2, #b3b3b3);
	}

	.cf-reviews-filters {
		display: flex;
		flex-direction: column;
		gap: 16px;
		margin-top: 8px;
		width: 100%;
		align-items: center;
	}

	.cf-reviews-filter-group {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 10px;
		min-width: 0;
	}

	.cf-reviews-filter-label {
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.06em;
		color: var(--cf-text-3, #7a7a7a);
		text-transform: uppercase;
	}

	.cf-reviews-filter-pills {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 8px;
	}

	.cf-reviews-filter-pill {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 7px 14px;
		border-radius: 999px;
		border: 1px solid var(--cf-border, #232323);
		background: rgba(255, 255, 255, 0.04);
		color: var(--cf-text-2, #b3b3b3);
		font-size: 12.5px;
		font-weight: 600;
		text-decoration: none;
		white-space: nowrap;
		transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease;
	}

	.cf-reviews-filter-pill:hover,
	.cf-reviews-filter-pill:focus-visible {
		color: #fff;
		border-color: rgba(255, 183, 0, 0.35);
	}

	.cf-reviews-filter-pill.is-active {
		border-color: var(--cf-accent, #ffb700);
		background: rgba(255, 183, 0, 0.12);
		color: var(--cf-accent, #ffb700);
		font-weight: 700;
	}

	.cf-reviews-page-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
		gap: 16px;
		min-width: 0;
	}

	.cf-reviews-page .cf-home-review-card {
		display: flex;
		flex-direction: column;
		gap: 10px;
		padding: 20px;
		border: 1px solid var(--cf-border, #232323);
		border-radius: 12px;
		background: var(--cf-bg-card, #141414);
		text-decoration: none;
		color: #fff;
		transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
	}

	.cf-reviews-page .cf-home-review-card:hover,
	.cf-reviews-page .cf-home-review-card:focus-visible {
		transform: translateY(-3px);
		border-color: rgba(255, 183, 0, 0.35);
		box-shadow: 0 14px 28px -14px rgba(255, 183, 0, 0.2);
	}

	.cf-reviews-page .cf-home-review-tag {
		align-self: flex-start;
		padding: 4px 10px;
		border-radius: 999px;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-size: 10px;
		font-weight: 700;
		letter-spacing: 0.06em;
	}

	.cf-reviews-page .cf-home-review-tag--article {
		background: rgba(255, 183, 0, 0.12);
		color: #ffb700;
		border: 1px solid rgba(255, 183, 0, 0.28);
	}

	.cf-reviews-page .cf-home-review-tag--platform {
		background: rgba(255, 255, 255, 0.06);
		color: #e4e4e4;
		border: 1px solid rgba(255, 255, 255, 0.14);
	}

	.cf-reviews-page .cf-home-review-excerpt {
		margin: 0;
		font-size: 13.5px;
		line-height: 1.6;
		color: var(--cf-text-2, #b3b3b3);
		flex: 1;
	}

	.cf-reviews-page .cf-home-review-author {
		display: flex;
		align-items: center;
		gap: 10px;
		margin-top: 2px;
	}

	.cf-reviews-page .cf-home-review-author .cf-review-avatar {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		border-radius: 10px;
		overflow: hidden;
		flex-shrink: 0;
	}

	.cf-reviews-page .cf-home-review-author .cf-review-avatar-img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
	}

	.cf-reviews-page .cf-home-review-author .cf-review-avatar--initial {
		background: linear-gradient(135deg, #FFB700, #8a6200);
		color: #0D0D0D;
		font-family: var(--cf-mono, 'Space Mono', monospace);
		font-weight: 700;
		font-size: 14px;
	}

	.cf-reviews-page .cf-home-review-author__name {
		font-size: 12px;
		font-weight: 600;
		color: #fff;
		min-width: 0;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.cf-reviews-page .cf-home-review-stars .cf-star {
		color: #4a4a4a;
	}

	.cf-reviews-page .cf-home-review-stars .cf-star.is-on {
		color: var(--cf-accent, #ffb700);
	}

	.cf-reviews-pagination {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-top: 28px;
	}

	.cf-reviews-pagination a,
	.cf-reviews-pagination span {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-width: 38px;
		height: 38px;
		padding: 0 12px;
		border-radius: 9px;
		border: 1px solid var(--cf-border, #232323);
		background: var(--cf-bg-card, #141414);
		color: var(--cf-text-2, #b3b3b3);
		text-decoration: none;
		font-size: 13px;
	}

	.cf-reviews-pagination a:hover,
	.cf-reviews-pagination a:focus-visible {
		color: #fff;
		border-color: rgba(255, 183, 0, 0.35);
	}

	.cf-reviews-pagination .current {
		background: rgba(255, 183, 0, 0.12);
		border-color: var(--cf-accent, #ffb700);
		color: var(--cf-accent, #ffb700);
		font-weight: 700;
	}

	.cf-reviews-pagination .dots {
		border-color: transparent;
		background: transparent;
	}

	@media (prefers-reduced-motion: reduce) {
		.cf-reviews-hero__border {
			animation: none;
			--cf-hero-border-angle: 210deg;
			filter: drop-shadow(0 0 3px rgba(255, 183, 0, 0.2));
			opacity: 0.55;
		}

		.cf-reviews-hero__center-glow {
			animation: none;
			opacity: 0.4;
			transform: translate(-50%, -50%) scale(0.95);
		}

		.cf-reviews-page .cf-home-review-card,
		.cf-reviews-filter-pill {
			transition: none;
		}

		.cf-reviews-page .cf-home-review-card:hover,
		.cf-reviews-page .cf-home-review-card:focus-visible {
			transform: none;
		}
	}
</style>

<?php
get_footer();
