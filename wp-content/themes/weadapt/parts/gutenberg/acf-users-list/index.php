<?php
/**
 * Block Contributors List
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();

$posts_per_page = get_field( 'number_of_results' );
$ids = get_field( 'users' );
$all_posts      = [];
$show_button    = true;

/* Query the database for contributors */

$user_query_args = [
	'number'  => $posts_per_page,
	'include' => $ids,
	'orderby' => 'display_name',
	'order'   => 'ASC',
	'fields'  => 'ID',
];

$user_query = new WP_User_Query( $user_query_args );

foreach ( $user_query->get_results() as $user_ID ) {
    $all_posts[] = [
        'type'  => 'user',
        'id'    => $user_ID,
        'title' => get_user_name( $user_ID ),
    ];
}

$filtered_posts = $all_posts;

$offset_user = count($filtered_posts);
$hide_button = ! ( $user_query->total_users > $offset_user );
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
		<div class="<?php echo esc_attr( $name ); ?>__contributors">
			<div class="contributors__row row--ajax <?php echo esc_attr( $name ); ?>__contributors-list" data-offset-user="<?php echo $offset_user; ?>">
				<?php if ( ! empty( $filtered_posts ) ) :
					foreach ( $filtered_posts as $post ) : ?>
						<?php
							echo get_part( 'components/alt-contributor-item/index', [
								'user_ID'    => $post['id'],
							] );
						?>
					<?php endforeach;
				endif; ?>
			</div>
		</div>

        <div class="wp-block-button wp-block-button--template load-more-contributors cpt-more<?php echo $hide_button ? ' hidden' : ''; ?>">
            <button type="button" class="wp-block-button__link cpt-more__btn">
                <?php _e('Load more', 'weadapt'); ?>
            </button>
        </div>

        <input type="hidden" value="<?php echo esc_attr( json_encode( $user_query_args ) ); ?>" name="user_query_args" />
	</div>
</section>
