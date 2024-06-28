<?php
/**
 * Single Blog Content
 *
 * @package WeAdapt
 */
?>

<div class="archive-main__entry">
	<?php
		if ( 'solutions-portal' === get_post_type() ) {
			get_part('components/single-solutions-portal-content/index');
		}
		else {
			if ( empty( get_the_content() ) ) {
				_e( 'There is no content.', 'weadapt' );
			}
			else {
				the_content();

				if ( get_field( 'additional_resources' ) ) {
					?><div class="additional-resources"><?php
						the_field( 'additional_resources' );
					?></div><?php
				}
			}
		}
	?>
</div>

<?php
get_part('components/single-references/index');
get_part('components/single-resources/index');

if ( apply_filters( 'show_related_single_blog_content', true ) ) {
	get_part('components/related-content/index');
}

