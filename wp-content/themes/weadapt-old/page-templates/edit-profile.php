<?php
/**
 * Template Name: Page Edit Profile
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

?>
<main id="page-content" class="page-content page-content--edit-profile">
	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
		<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
		<?php load_inline_styles_shared( 'archive' ); ?>
		<?php load_inline_styles_shared( 'cpt-list-item' ); ?>

		<?php get_part('components/user-hero/index'); ?>
		<?php get_part("components/edit-profile/index"); ?>
		<?php get_part("components/save/index");?>
	</div>
</main>
<?php
get_footer();