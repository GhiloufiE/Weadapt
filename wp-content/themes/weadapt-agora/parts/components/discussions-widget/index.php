<?php
/**
 * Discussions widget
 *
 * @package WeAdapt
 */
if ( ! empty( $forum_id = get_post_forum( get_the_ID() ) ) ) :
	$query_args = array(
		'post_status'    => 'publish',
		'post_type'      => get_allowed_post_types( [ 'forum' ] ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => 2,
		'meta_query'     => [[
			'key'      => 'forum',
			'value'    => $forum_id
		]],
		'ignore_sticky_posts' => true,
		'theme_query'         => true, // multisite fix
	);

	?>
	<div class="discussions-widget">
		<?php load_inline_styles( __DIR__, 'discussions-widget' ); ?>
		<h2 class="discussions-widget__title widget-title"><?php _e( 'Discussions', 'weadapt' ); ?></h2>
		<div class="discussions-widget__list">
			<?php
				get_part( 'components/cpt-query/index', [
					'query_args'      => $query_args,
					'show_filters'    => false,
					'show_loadmore'   => false,
				]);
			?>
		</div>
		<div class="discussions-widget__more">
			<a class="discussions-widget__more-link" href="<?php echo get_permalink( $forum_id ); ?>"><?php _e( 'View all discussions', 'weadapt' ); ?></a>
		</div>
	</div>
<?php
endif;