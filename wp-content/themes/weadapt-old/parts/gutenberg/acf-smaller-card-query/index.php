<?php

/**
 * Query Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );

$name  = $block_object->name();
$attr  = $block_object->attr( 'background-' . get_field( 'background_color' ) );

$tax_query = [
	'relation' => 'AND'
];

$meta_query = [
	'relation' => 'AND'
];

$per_page    = get_field( 'posts_per_page' ) ? get_field( 'posts_per_page' ) : 6;
$post_type   = get_field( 'post_type' ) ? get_field( 'post_type' ) : ['article'];
$query_args  = array(
	'post_status'         => 'publish',
	'post_type'           => $post_type,
	'posts_per_page'      => $per_page,
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
);


// Categories
if ( ! empty( $include_categories = get_field( 'categories' ) ) ) {
	$query_args['category__in'] = wp_parse_id_list( $include_categories );
}
if ( ! empty( $exclude_categories = get_field( 'exclude_categories' ) ) ) {
	$query_args['category__not_in'] = wp_parse_id_list( $exclude_categories );
}

// Tags
if ( ! empty( $include_tags = get_field( 'tags' ) ) ) {
	$tax_query[] = [
		'taxonomy' => 'tags',
		'terms'    => wp_parse_id_list( $include_tags ),
		'operator' => 'AND'
	];
}
if ( ! empty( $exclude_tags= get_field( 'exclude_tags' ) ) ) {
	$tax_query[] = [
		'taxonomy' => 'tags',
		'terms'    => wp_parse_id_list( $exclude_tags ),
		'operator' => 'NOT IN'
	];
}

// Featured
if ( ! empty( $featured_posts = get_field( 'featured_posts' ) ) ) {
	$meta_query[] = [
		'key'   => 'is_featured',
		'value' => $featured_posts
	];
}


// Tax Query
if ( ! empty( $tax_query ) ) {
	$query_args['tax_query'] = $tax_query;
}

// Events Meta
if ( count( $post_type ) == 1 && in_array( 'event', $post_type ) ) {
	$event_type = get_field( 'event_type' ) ? get_field( 'event_type' ) : 'all';
	$types      = get_field( 'types' ) ? get_field( 'types' ) : 'none';

	if ( $event_type !== 'all' ) {
		$query_args['orderby']   = 'meta_value';
		$query_args['meta_key']  = 'start_date';
		$query_args['meta_type'] = 'DATETIME';
	}

	if ( $event_type === 'upcoming' ) {
		$meta_query[] = [
			'relation'          => 'AND',
			array(
				'key'       => 'start_date',
				'compare'   => 'EXISTS'
			),
			array(
				'key'       => 'start_date',
				'value'     => date('Y-m-d H:i:s'),
				'compare'   => '>',
				'type'      => 'DATETIME'
			)
		];
	}
	else if ( $event_type === 'past' ) {
		$meta_query[] = [
			'key'           => 'start_date',
			'compare'       => '<=',
			'value'         => date('Y-m-d H:i:s'),
			'type'          => 'DATETIME',
		];
	}

	if ( ! empty( $types ) && $types !== 'none' ) {
		$meta_query[] = [
			'key'   => 'type',
			'value' => $types
		];
	}
}

// Meta Query
if ( ! empty( $meta_query ) ) {
	$query_args['meta_query'] = $meta_query;
}

$query = new WP_Query( $query_args );

?>
<section <?php echo $attr; ?>>
	<?php
		load_inline_styles_shared( 'archive' );
		load_inline_styles_shared( 'cpt-list-item' );
		load_inline_styles( __DIR__, $name );
	?>
	<div class="container query__container">
		<?php echo $block_object->title( 'query__heading' ); ?>
		<?php echo $block_object->desc( 'query__description' ); ?>

		<?php if ( $query->have_posts() ) : ?>
			<div class="cpt-latest row--ajax row" data-paged="1" data-pages="<?php echo $query->max_num_pages; ?>">
				<?php
					while ( $query->have_posts() ) : $query->the_post();
						$post_type = get_post_type();
						$part_name = file_exists( get_theme_file_path( "/parts/archive/templates/grid/$post_type.php" ) ) ? $post_type : 'blog';

						?>
							<div class="col-12 col-md-6 col-lg-4">
								<?php get_part( "archive/templates/grid/blog", [
									'post_ID' => get_the_ID()
								] ); ?>
							</div>
						<?php

					endwhile;
				?>
			</div>

			<?php if ( ( $query->max_num_pages > 1 ) && get_field( 'show_load_more_button' ) ) : ?>
				<div class="wp-block-button wp-block-button--template cpt-more is-style-outline">
					<input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="query_args" />

					<button type="button" class="wp-block-button__link cpt-more__btn">
						<?php _e('Load more', 'weadapt'); ?>
					</button>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="<?php echo esc_attr( $name ); ?>__empty-results"><?php _e( 'Nothing found.', 'weadapt' ); ?></div>
		<?php endif; ?>

		<?php echo $block_object->button( '', 'query__button' ); ?>
	</div>
</section>
