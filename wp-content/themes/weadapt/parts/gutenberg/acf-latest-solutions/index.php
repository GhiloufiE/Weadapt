<?php
/**
 * Block Latest Solutions
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();
$posts_per_page = get_field( 'number_of_results' );

$query_args = array(
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
	'post_type'      => 'solutions-portal',
	'theme_query'         => true, // multisite fix
);

$query 	= new WP_Query( $query_args );

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

wp_reset_postdata();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading", 'h2' ); ?>
		<div class="<?php echo $name; ?>-solutions__row">
			<?php if ( ! empty( $all_posts ) ) : ?>
				<? foreach ( $all_posts as $resource ) : ?>
					<?php
						echo get_part( 'components/alt-resource-item/index', [
							'resource_ID' 			=> $resource['id'],
							'resource_type' 		=> 'solution',
							'resource_cta_label' 	=> 'Read Article'
						]);
					?>
				<?php endforeach; ?>
			<? endif; ?>
		</div>
	</div>
</section>