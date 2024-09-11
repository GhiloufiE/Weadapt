<?php


/**
 * Bulk Edit CPT
 */
add_action( 'restrict_manage_posts', function( $post_type, $which ) {
	if ( ! current_user_can( 'edit_posts' )  ) {
		return;
	}

	if ( in_array( $post_type, [ 'blog', 'article', 'course', 'event', 'case-study' ] ) ) {
		$select_name   = 'theme_network_' . $which;
		$theme_network = isset( $_GET[$select_name] ) ? intval( $_GET[$select_name] ) : 0;
		?>
			<select style="float: left;" name="<?php echo esc_attr( $select_name ); ?>">
				<option value="0"><?php _e( 'All Themes/Networks' ); ?></option>

				<?php
					foreach ( ['theme', 'network'] as $cpt_name ) {
						echo sprintf( '<optgroup label="%s">', ucfirst( $cpt_name ) );

						$cpt_query = new WP_Query( [
							'post_type'           => [$cpt_name],
							'orderby'             => 'title',
							'order'               => 'ASC',
							'posts_per_page'      => -1,
							'ignore_sticky_posts' => true,
							'theme_query'         => true, // multisite fix
						] );

						if( ( $cpt_query->have_posts() ) ) {
							while ( $cpt_query->have_posts() ) {
								$cpt_query->the_post();

								$post_ID = get_the_id();

								echo sprintf( '<option value="%s" %s>%s</option>', $post_ID, selected( $theme_network, $post_ID ), get_the_title() );
							}

							wp_reset_postdata();
						}
						echo '</optgroup>';
					}
				?>
			</select>
		<?php
	}

	if ( in_array( $post_type, [ 'blog', 'article', 'course', 'event', 'case-study', 'page' ] ) ) {
		$blog_select_name = 'blog_' . $which;
		$blog_value       = isset( $_GET[$blog_select_name] ) ? intval( $_GET[$blog_select_name] ) : 0;
		?>
			<select style="float: left;" name="<?php echo esc_attr( $blog_select_name ); ?>">
				<option value="0"><?php _e( 'All Sites' ); ?></option>

				<?php
					foreach ( get_sites() as $key => $site ) {
						echo sprintf( '<option value="%s" %s>%s</option>', $site->blog_id, selected( $blog_value, $site->blog_id ), get_blog_details( $site->blog_id )->blogname );
					}
				?>
			</select>
		<?php
	}
}, 10, 2 );


/**
 * Rename Publish Button
 */
add_action( 'init', function() {
	global $pagenow;

	if (
		isset( $pagenow ) &&
		'post-new.php' === $pagenow &&
		! current_user_can( 'publish_posts' )
	) {
		add_action( 'admin_print_footer_scripts', function() {
			if ( wp_script_is( 'wp-i18n' ) ) {
				?>
				<script>
					wp.i18n.setLocaleData( {
						'Publish': [ 'Submit draft', 'weadapt' ]
					} );
				</script>
				<?php
			}
		} );
	}
} );
