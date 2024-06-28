<?php
/**
 * Default Page template
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
get_header();
?>
<main id="page-content" class="page-content page-content--default">
	<h1 class="page-title screen-reader-text"><?php the_title(); ?></h1>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>

	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php the_content(); ?>
	</div>
</main>
<?php
get_footer();
