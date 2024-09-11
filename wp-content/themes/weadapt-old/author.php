<?php
/**
 * Template Name: Author Page
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

$template_args = [
	'is_profile' => false,
	'user_ID'    => get_the_author_meta( 'ID' )
];
?>
<main id="page-content" class="page-content page-content--author">
	<div id="content" tabindex="-1" class="page-content__wrapper">
		<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
		<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
		<?php load_inline_styles_shared( 'archive' ); ?>
		<?php load_inline_styles_shared( 'cpt-list-item' ); ?>

		<?php get_part('components/user-hero/index'); ?>

		<section class="archive-main" aria-labelledby="main-heading">
			<div class="archive-main__container container">
				<?php
					$breadcrumbs = [];

					foreach ( [
						'connect',
						'people'
					] as $template_name ) {
						if ( ! empty( $page_ID = get_page_id_by_template( $template_name ) ) ) {
							$breadcrumbs[] = ['url' => get_permalink( $page_ID ), 'label' => get_the_title( $page_ID ) ];
						}
					}
					$breadcrumbs[] = ['url' => '' , 'label' => get_the_author_meta( 'display_name' ) ];

					get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);
				?>

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