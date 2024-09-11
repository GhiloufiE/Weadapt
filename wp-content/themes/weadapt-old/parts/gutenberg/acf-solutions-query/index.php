<?php
/**
 * Block Solutions Query
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name = $block_object->name();
$acf_taxonomies = get_field( 'solutions_taxonomies' );

/* Variables for query */
$per_page    		= get_field( 'number_of_results' ) ? get_field( 'number_of_results' ) : 6;
$show_search 		= get_field( 'show_search' ) ? get_field( 'show_search' ) : false;
$show_sort 			= get_field( 'show_sort' ) ? get_field( 'show_sort' ) : false;
$show_breadcrumbs 	= get_field( 'show_breadcrumbs' ) ? get_field( 'show_breadcrumbs' ) : false;
$show_status 		= get_field( 'show_status' ) ? get_field( 'show_status' ) : false;
$show_filters    	= true;

$post_type = 'solutions-portal';
$query_args  = array(
	'post_status'         => 'publish',
	'post_type'           => [$post_type],
	'posts_per_page'      => $per_page,
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
);
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<style>
		<?php
			load_inline_styles_shared( 'archive' );
			load_inline_styles_shared( 'cpt-list-item' );
		?>
	</style>

	<div class="container">
		<?php
			if($show_breadcrumbs) {
				$breadcrumbs = [];
				$page_ID = get_queried_object_id();
				if ( ! empty( $page_ID ) ) {
					$breadcrumbs[] = ['url' => get_permalink( $page_ID ), 'label' => get_the_title( $page_ID ) ];
				}
				$breadcrumbs[] = ['url' => '' , 'label' => get_the_author_meta( 'display_name' ) ];

				get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);
			}
		?>

		<?php if ( have_rows( 'cta_buttons' ) ) : ?>
			<div class="cta-buttons">
				<?php while ( have_rows( 'cta_buttons' ) ) : the_row(); ?>
					<div class="cta-buttons__item">
					 <?php
					 	$cta_button = get_sub_field( 'section_button' );

						 $icon  = 'icon-arrow-right-button';
						 $style = 'default';
						 $link  = [
							'url' => $cta_button["url"],
							'title' => __( $cta_button["title"], 'weadapt' ),
							'target' => $cta_button["target"],
							'icon' => 'icon-arrow-right-button',
						];
						if ( ! empty( $link ) ) {
							echo get_button($link, $style, '', $icon);
						}

					?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php
			get_part( 'components/cpt-solutions-query/index', [
				'query_args' 		=> $query_args,
				'acf_taxonomies' 	=> $acf_taxonomies,
				'show_search'		=> $show_search,
				'show_sort' 		=> $show_sort,
				'show_status' 		=> $show_status,
			]);
		?>
	</div>
</section>