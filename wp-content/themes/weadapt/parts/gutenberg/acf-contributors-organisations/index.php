<?php
/**
 * Contributors And Organisations Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

$posts_per_page = 6;
$all_posts      = [];
$show_button    = true;

$organisation_IDs = get_field( 'organisations' ) ? wp_parse_id_list( get_field( 'organisations' ) ) : [];
$user_IDs         = get_field( 'users' ) ? wp_parse_id_list( get_field( 'users' ) ) : [];

$query_args = [
	'post_status'         => 'publish',
	'posts_per_page'      => $posts_per_page,
	'post_type'           => get_allowed_post_types( [ 'organisation' ] ),
	'orderby'             => 'title',
	'order'               => 'ASC',
	'fields'              => 'ids',
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
];

$query = new WP_Query( $query_args );

if ( ! empty( $organisation_IDs ) ) {
	$query_args['post__not_in'] = $organisation_IDs;
}

$user_query_args = [
	'number'     => $posts_per_page,
	'orderby'    => 'registered',
	'order'      => 'DESC',
	'fields'     => 'ID',
	'meta_query'  => [
		'relation' => 'AND',
		[
			'key'     => 'avatar',
			'value'   => 0,
			'compare' => '!='
		],
		[
			'key'     => 'avatar',
			'value'   => '',
			'compare' => '!='
		]
	]
];

$user_query = new WP_User_Query( $user_query_args );

if ( ! empty( $user_IDs ) ) {
	$user_query_args['exclude'] = $user_IDs;

	foreach ( $user_IDs as $user_ID ) {
		$all_posts[] = [
			'type'  => 'user',
			'id'    => $user_ID,
			'title' => str_replace( '”', '"', get_the_title( $user_ID ) ),
		];
	}
}
else {
	foreach ( $user_query->get_results() as $user_ID ) {
		$user_query_args['exclude'][] = $user_ID;

		$all_posts[] = [
			'type'  => 'user',
			'id'    => $user_ID,
			'title' => get_user_name( $user_ID ),
		];
	}
}


$user_query_args['orderby'] = 'display_name';
$user_query_args['order']   = 'ASC';



$filtered_posts = $all_posts;

usort( $filtered_posts, function( $a, $b ) {
	return strcmp( $a['title'], $b['title'] );
});

if ( empty( $organisation_IDs ) || empty( $user_IDs ) ) {
	$filtered_posts = array_slice( $filtered_posts, 0, $posts_per_page );
}

$type_count          = array_count_values( array_column( $filtered_posts, 'type' ) );
$offset_organisation = ! empty( $organisation_IDs ) ? 0 : $type_count['post'] ?? 0;
$offset_user         = 0;

$hide_button = ! ( $query->found_posts + $user_query->total_users > $offset_organisation + $offset_user );
?>

<section <?php echo $attr; ?>>
	<?php
		load_inline_styles_shared( 'cpt-search-form' );
		load_inline_styles( __DIR__, $name );
	?>

	<div class="container">
		<header class="section-header has-text-align-center">
			<?php
				echo $block_object->subtitle( "{$name}__subtitle" );
				echo $block_object->title( "{$name}__heading" );
				echo $block_object->desc( "{$name}__descriprion" );
			?>

			<form class="cpt-search-form">
				<input type="search" name="search" placeholder="Search people and organisations" class="cpt-search-form__input">

				<button type="submit" class="wp-block-button__link cpt-search-form__button">
					<?php _e( 'Search', 'weadapt' ); ?>
					<?php echo get_img( 'icon-search-small' ); ?>
				</button>
			</form>
		</header>

		<div class="contributors-organisations__row row row--ajax" data-offset-organisation="<?php echo $offset_organisation; ?>" data-offset-user="<?php echo $offset_user; ?>">
			<?php if ( ! empty( $filtered_posts ) ) :
				foreach ( $filtered_posts as $post ) : ?>
					<div class="contributors-organisations__col col-12 col-md-4">
						<?php
							if ( $post['type'] === 'post' ) {
								echo get_part( 'components/info-widget-cpt/index', [
									'cpt_ID'            => $post['id'],
									'cpt_buttons'       => ['find-out-more'],
									'hide_empty_fields' => true,
								]);
							} else {
								echo get_part( 'components/info-widget-user/index', [
									'user_ID'            => $post['id'],
									'show_follow_button' => false,
									'hide_empty_fields'  => true,
								] );
							}
						?>
					</div>
				<?php endforeach;
			endif; ?>
		</div>

		<div class="wp-block-button wp-block-button--template cpt-more<?php echo $hide_button ? ' hidden' : ''; ?>">
			<button type="button" class="wp-block-button__link cpt-more__btn">
				<?php _e('Load more', 'weadapt'); ?>
			</button>
		</div>

		<input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="query_args" />
		<input type="hidden" value="<?php echo esc_attr( json_encode( $user_query_args ) ); ?>" name="user_query_args" />
		<input type="hidden" value="<?php echo esc_attr( json_encode( $organisation_IDs ) ); ?>" name="organisation_IDs" />
		<input type="hidden" value="<?php echo esc_attr( json_encode( $user_IDs ) ); ?>" name="user_IDs" />
		<input type="hidden" value="<?php echo is_user_logged_in() ? 1 : 0; ?>" name="logged_in" />
	</div>
</section>