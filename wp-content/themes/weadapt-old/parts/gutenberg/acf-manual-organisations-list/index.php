<?php
/**
 * Block Manual Organisations List
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();

$posts_per_page   = get_field( 'number_of_results' );
$ids 		  	  = get_field( 'organisations' );
$show_description = get_field( 'show_description' );
$all_posts        = [];
$show_button      = true;

/* Query the database for organisations */
$query_args = [
	'post_status'         => 'publish',
	'posts_per_page'      => $posts_per_page,
	'post_type'           => get_allowed_post_types( [ 'organisation' ] ),
	'post__in' 			  => $ids,
	'orderby'             => 'title',
	'order'               => 'ASC',
	'fields'              => 'ids',
];

$query = new WP_Query( $query_args );

if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();

		$all_posts[] = [
			'type'  => 'post',
			'id'    => get_the_ID(),
			'title' => str_replace( '”', '"', get_the_title() ),
		];
	}
}

$filtered_posts = $all_posts;

$offset_organisation = count($filtered_posts);
$hide_button = ! ( $query->found_posts > $offset_organisation );
wp_reset_postdata();

?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container smaller-container">
        <div class="<?php echo esc_attr( $name ); ?>--text">
            <div class="<?php echo esc_attr( $name ); ?>__content">
                <?php
                    echo $block_object->title( "{$name}__heading", 'h2' );
                    echo $block_object->desc( "{$name}__description" );
                ?>
            </div>
        </div>
		<div class="<?php echo esc_attr( $name ); ?>__organisations">
			<div class="organisations__row row--ajax <?php echo esc_attr( $name ); ?>__organisations-list"
			data-offset-organisation="<?php echo $offset_organisation; ?>"
			data-show-description="<?php echo $show_description ? 'true' : 'false'; ?>">
				<?php if ( ! empty( $filtered_posts ) ) :
					foreach ( $filtered_posts as $organisation ) : ?>
						<?php
							echo get_part( 'components/alt-organisation-item/index', [
								'org_ID'      		=> $organisation['id'],
								'show_description' 	=> json_encode($show_description),
							]);
						?>
					<?php endforeach;
				endif; ?>
			</div>
		</div>

        <div class="wp-block-button wp-block-button--template load-more-organisations cpt-more<?php echo $hide_button ? ' hidden' : ''; ?>">
            <button type="button" class="wp-block-button__link cpt-more__btn">
                <?php _e('Load more', 'weadapt'); ?>
            </button>
        </div>

        <input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="query_args" />
	</div>
</section>