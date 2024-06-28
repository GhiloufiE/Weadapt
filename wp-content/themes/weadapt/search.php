<?php
/**
 * Theme search file.
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$search_query = get_search_query();
$args = array(
    's' => $search_query,
    'post_type' => get_allowed_post_types( [ 'blog', 'article', 'course', 'event', 'case-study', 'theme', 'network', 'organisation', 'solutions-portal', 'forum', 'forums' ] ),
	'ignore_sticky_posts' => true,
	'sentence'            => true,
    'theme_query' => true,
);
$search_query_thing = new WP_Query( $args );
$found_posts  = ! empty( $search_query_thing->found_posts ) ? $search_query_thing->found_posts : 0;
?>

<main id="page-content" class="page-content page-content--search">
	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
		<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
		<?php load_inline_styles_shared( 'archive' ); ?>
		<?php load_inline_styles_shared( 'cpt-list-item' ); ?>

		<section class="archive-main" aria-labelledby="main-heading">
			<div class="archive-main__container container">

				<h1 class="archive-main__search-title"><?php echo sprintf( '<b>%d</b> %s <b>“%s”</b>', $found_posts, __( 'Search results for', 'weadapt' ), $search_query ); ?></h1>

				<div class="archive-main__row row">
					<div class="archive-main__content">
						<?php
                        	$query_args = $args;

                        	$additional_query_args = [
                        		'post_status'         => 'publish',
                        		'ignore_sticky_posts' => true,
                        		'sentence'            => true,
                        	];

							get_part( 'components/cpt-query/index', [
								'query_args'      		=> array_merge( $query_args, $additional_query_args ),
								'show_post_types' 		=> true,
								'show_filters'    		=> true,
								'initial_empty_sort_by'	=> true,
							]);
						?>
					</div>

					<aside class="archive-main__aside">
						<?php
							get_part( 'components/tags/index', [
								'title'        => __( 'Tags matching', 'weadapt' ),
								'search_query' => $search_query
							] );

							$user_query = new WP_User_Query( [
								'search'         => "*$search_query*",
								'fields'         => 'ID',
								'search_columns' => ['display_name'],
								'number'         => 4,
							] );

							get_part('components/members-widget/index', [
								'title'     => __( 'Users matching', 'weadapt' ),
								'search_query' => $search_query,
								'members_IDs'   => $user_query->results,
							]);
						?>
					</aside>
				</div>
			</div>
		</section>


		<?php if ( is_active_sidebar( 'content-area-bottom' ) ) {
			dynamic_sidebar( 'content-area-bottom' );
		} ?>
	</div>
</main>

<?php
get_footer();
