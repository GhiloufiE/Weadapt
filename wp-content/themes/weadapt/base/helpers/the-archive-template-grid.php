<?php

/**
 * The Archive Template Grid
 */
if ( ! function_exists( 'the_archive_template_grid' ) ) :
	function the_archive_template_grid( $folder ="list" ) {
		$folder = apply_filters( 'cpt_archive_template_folder', $folder );

		$post_type     = get_post_type();
		$template_part = file_exists( get_theme_file_path( "/parts/archive/templates/$folder/$post_type.php" ) ) ? $post_type : 'blog';

		get_part( "archive/templates/$folder/$template_part", [
			'post_ID' => get_the_ID()
		] );
	}

endif;
