<?php
/**
 * Theme index file.
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$type             = ! empty( $args['type'] ) ? $args['type'] : '';
$part_name        = ! empty( $type ) && file_exists( get_theme_file_path("/parts/archive/$type/") ) ? $type : 'blog';
$query_post_types = ! empty( $args['query_post_types'] ) ? $args['query_post_types'] : [];
$show_post_types  = isset( $args['show_post_types'] ) ? wp_validate_boolean( $args['show_post_types'] ) : true;
$show_filters     = isset( $args['show_filters'] ) ? wp_validate_boolean( $args['show_filters'] ) : true;
?>

<main id="page-content" class="page-content page-content--archive">
	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
		<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
		<?php load_inline_styles_shared( 'archive' ); ?>
		<?php load_inline_styles_shared( 'cpt-list-item' ); ?>

		<?php get_part( 'components/archive-hero/index' ); ?>

		<section class="archive-main" aria-labelledby="main-heading">
			<div class="archive-main__container container">
				<?php get_part("archive/$part_name/breadcrumbs"); ?>
				<?php get_part("archive/$part_name/filter-tabs"); ?>

				<div class="archive-main__row row">
					<div class="archive-main__content">
						<?php get_part("archive/$part_name/content", [
							'query_post_types' => $query_post_types,
							'show_post_types'  => $show_post_types,
							'show_filters'     => $show_filters,
						]); ?>
					</div>

					<aside class="archive-main__aside">
						<?php get_part("archive/$part_name/aside"); ?>
					</aside>
				</div>
			</div>
		</section>

		<?php if ( is_active_sidebar( 'content-area-bottom' ) ) {
			dynamic_sidebar( 'content-area-bottom' );
		} ?>
	</div>
</main>

<button id="back-to-top" class="wp-block-button__link has-background">Add forum discussion</button>
<style>
#back-to-top {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: none;
  padding: 10px;
  font-size: 16px;
  text-align: center;
  cursor: pointer;
  z-index: 1000;
}

#back-to-top:hover {
  background-color: #fff; /* Optional hover effect */
}
</style>
<script>

window.onscroll = function() {
  var button = document.getElementById("back-to-top");
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    button.style.display = "block";
  } else {
    button.style.display = "none";
  }
};

document.getElementById("back-to-top").addEventListener("click", function(event){
  event.preventDefault();
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
<?php
get_footer();