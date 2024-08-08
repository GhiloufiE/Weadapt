<?php
/**
 * Single Blog Content
 *
 * @package WeAdapt
 */
$excerpt = has_excerpt() ? get_the_excerpt() : '';
    if ( $excerpt ) : ?>
        <style>
            .single-hero__excerpt {
                color: black;
                background-color: #A7CE7F;
                padding: 25px;
            }
            .single-hero__excerpt a {
                color: #fff;
            }
        </style>
      	<?php if ( 'solutions-portal' === get_post_type() ) { ?>
       
    <?php } endif; ?>
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

