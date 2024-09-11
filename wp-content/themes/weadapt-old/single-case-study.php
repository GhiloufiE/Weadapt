<?php
/**
 * Single Blog Post template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$map_page_ID = url_to_postid( get_home_url( null, '/placemarks/maps/' ) );

if ( 'publish' === get_post_status() && ! empty( $map_page_ID ) ) {
	$map_page = get_post( $map_page_ID );

	?>
	<main id="page-content" class="page-content page-content--default">
		<h1 class="page-title screen-reader-text"><?php the_title(); ?></h1>
		<div id="content" tabindex="-1" class="page-content__wrapper">
			<?php echo apply_filters( 'the_content', $map_page->post_content ); ?>
		</div>
	</main>
	<?php
}
else {
	echo get_template_part( 'single' );
}

get_footer();