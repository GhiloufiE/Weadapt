<?php
/**
 * Template Name: Page Profile
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$template_args = [
	'is_profile' => true,
	'user_ID'    => get_current_user_id()
];
?>
<main id="page-content" class="page-content page-content--profile">
	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
		<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
		<?php load_inline_styles_shared( 'archive' ); ?>
		<?php load_inline_styles_shared( 'cpt-list-item' ); ?>

		<?php get_part('components/user-hero/index'); ?>

		<section class="archive-main" aria-labelledby="main-heading">
			<div class="archive-main__container container">
				<?php get_part( 'single/author/filter-tabs', $template_args ); ?>

				<div class="archive-main__row row">
					<div class="archive-main__content">
						<?php get_part( 'single/author/content', $template_args ); ?>
					</div>

					<aside class="archive-main__aside">
						<?php get_part( 'single/author/aside', $template_args ); ?>
					</aside>
				</div>
			</div>
		</section>

		<?php if ( is_active_sidebar( 'content-area-bottom' ) ) {
			dynamic_sidebar( 'content-area-bottom' );
		} ?>
	</div>
</main>
<?php
get_footer();