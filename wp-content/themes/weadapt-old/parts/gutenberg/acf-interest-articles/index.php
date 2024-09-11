<?php
/**
 * Block Articles of Interest
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();
$posts_per_page = 3;

$articles_of_interest_array = get_field( 'interest_articles');

$query_args = array (
  'post__in' 		=> $articles_of_interest_array,
  'posts_per_page'  => $posts_per_page,
  'orderby'	 		=> 'post__in',
  'post_type' 		=> 'article',
  'post_status' 	=> 'publish',
  'theme_query' 	=> true, //multisite fix
);

?>
<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading", 'h2' ); ?>
		<?php
			get_part( 'components/cpt-resources-query/index', [
				'query_args' => $query_args
			]);
		?>
	</div>
</section>