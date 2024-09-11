<?php
/**
 * 404 Page template
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();
?>
<main id="page-content" class="page-content page-content--404">
	<div class="container">
		<div class="page-content__wrapper">
			<?php echo get_part( '404/index' ); ?>
		</div>
	</div>
</main>
<?php
get_footer();
