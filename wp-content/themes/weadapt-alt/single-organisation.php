<?php
/**
 * Organisation Post template. - Adaptation at Altitude
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$post_type = ! empty( $args['type'] ) ? $args['type'] : get_post_type();
$part_name = file_exists( get_theme_file_path("/parts/single/$post_type/") ) ? $post_type : 'blog';
?>

<main id="page-content" class="page-content page-content--single organisation-template">
	<div id="content" tabindex="-1" class="page-content__wrapper">
	<?php
		load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading');
		load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph');
		load_inline_styles_shared( 'cpt-list-item' );
	?>

	<div class="container">
		<?php get_part("single/$part_name/breadcrumbs"); ?>
	</div>

	<section class="archive-main" aria-labelledby="main-heading">
		<div class="archive-main__container">
			<?php get_part("single/$part_name/hero/index"); ?>
			<?php get_part("single/$part_name/content/index"); ?>
		</div>
	</section>

	<?php if ( is_active_sidebar( 'content-area-bottom' ) ) {
		dynamic_sidebar( 'content-area-bottom' );
	} ?>
	</div>
</main>
<?php
get_footer();