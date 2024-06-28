<?php
load_blocks_script( 'cpt-resources-query', 'weadapt/cpt-resources-query' );
load_inline_styles( __DIR__, 'cpt-resources-query' );

$query_args     = ! empty( $args['query_args'] ) ? $args['query_args'] : [];
$query 			= new WP_Query( $query_args );

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
$offset_resource = is_array($filtered_posts) ? count($filtered_posts) : 0;
$hide_button = ! ( $query->found_posts > $offset_resource );
wp_reset_postdata();
?>


<?php if ( ! empty( $filtered_posts ) ) : ?>
	<div class="single-organisation__resources">
		<div class="resources__row row--ajax single-organisation__resources-list" data-offset-resource="<?php echo $offset_resource; ?>">
			<? foreach ( $filtered_posts as $resource ) : ?>
				<?php
					$post_type = get_post_type($resource['id']);
					echo get_part( 'components/alt-resource-item/index', [
						'resource_ID' 			=> $resource['id'],
						'resource_type' 		=> 'resource',
						'resource_cta_label' 	=> $post_type == 'event' ?  'View Event' :  'View Resource',
					]);
				?>
			<?php endforeach; ?>
		</div>

		<div class="wp-block-button wp-block-button--template load-more-resources cpt-more<?php echo $hide_button ? ' hidden' : ''; ?>">
			<button type="button" class="wp-block-button__link cpt-more__btn">
				<?php _e('Load more', 'weadapt'); ?>
			</button>
		</div>
		<input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="query_args" />
	</div>
<?php else : ?>
	<p><?php _e('No results found.', 'weadapt'); ?></p>
<? endif; ?>

