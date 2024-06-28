<?php
/**
 * The template for displaying all single posts and attachments.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

get_header();
the_post();
?>

<main id="page-content" class="page-content page-content--single">
	<div id="content" tabindex="-1" class="page-content__wrapper">
	<?php
		load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading');
		load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph');

		// Attachment Image
		printf( '<figure class="aligncenter">%s</figure>', wp_get_attachment_image( get_the_ID(), 'full' ) );

		// Title
		printf( '<h1 class="has-large-font-size aligncenter">%s</h1>', get_the_title() );

		// Content
		the_content();
	?>
	</div>
</main>
<?php
get_footer();