<?php
/**
 * Contributors Item (ALT)
 *
 * @package WeAdapt
 */
$resource_ID  	        = ! empty( $args['resource_ID'] ) ? $args['resource_ID'] : 0;
$resource_type			= ! empty( $args['resource_type'] ) ? $args['resource_type'] : 'resource';
$resource_cta_label		= ! empty( $args['resource_cta_label'] ) ? $args['resource_cta_label'] : 'View Resource';

if ( ! empty( $resource_ID ) ) :
	$resource_url  		= get_the_permalink( $resource_ID );
	$resource_image   	= get_the_post_thumbnail( $resource_ID, 'large' );
	$resource_content 	= get_the_content(null, true, $resource_ID);
	$resource_excerpt 	= get_the_excerpt( $resource_ID );
	$resource_title   	= get_the_title( $resource_ID );
	$resource_contributors  = get_field( 'people_contributors', $resource_ID ) ? get_field( 'people_contributors', $resource_ID ) : [];
	$post_type = get_post_type($resource_ID);
	endif;

?>

<div class="resources-list__resource">
	<?php load_inline_styles( __DIR__, 'alt-resource-item' ); ?>
	<div class="resources-list__resource-container">
		 <div class="resources-list__resource--image">
		 	<?php if($post_type === 'solutions-portal') {
		 		$status = get_field('status', $resource_ID);
		 		$status_test = $status === 'pilot' ? 'Short Solution' : 'Detailed Solution';
				echo '<span class="resources-list__resource--status">';
				echo $status_test;
                echo '</span>';
			} ?>
			<?php echo $resource_image; ?>
		 </div>
		 <div class="resources-list__resource--text">
		 	<?php if ( ! empty( $resource_title ) ) : ?>
				<h5 class="resources-list__resource--title" >
					<?php echo $resource_title; ?>
				</h5>
			<?php endif; ?>

			<?php if ( $post_type == 'event' ) :
				 $date_html = get_event_formatted_date($resource_ID);
				 if ( ! empty( $date_html ) ) : ?>
					<div class="resources-list__resource--date" >
						<?php echo $date_html; ?>
					</div>
				<?php endif;
				endif;
			?>

			<?php if ($resource_type === 'resource') :
				if ( ! empty( $resource_excerpt ) ) : ?>
					<div class="resources-list__resource--excerpt" >
						<?php echo wp_trim_words( $resource_excerpt, 30, '...' ); ?>
					</div>
				<?php elseif( ! empty($resource_content) ) : ?>
					<div class="resources-list__resource--excerpt" >
						<?php echo wp_trim_words( $resource_content, 30, '...' ); ?>
					</div>
				<?php endif;
			elseif ($resource_type === 'solution') : ?>
				<?php if( !empty($resource_contributors) ) : ?>
					<div class="resources-list__resource--contributors" >
						<div class="resources-list__resource--contributors--avatar">
							<a href="<?php echo get_author_posts_url( $resource_contributors[0] ); ?>">
								<?php echo get_avatar( $resource_contributors[0], 80 ); ?>
							</a>
						</div>
						<div class="resources-list__resource--contributors--text">
							<div class="resources-list__resource--contributors--info">
 								<?php _e( 'Added by ', 'weadapt' ); ?>
 								<?php if ( count( $resource_contributors ) > 1 ) :
 									_e( 'Multiple Authors', 'weadapt' );
								else :
									_e( get_user_name($resource_contributors[0]), 'weadapt' );
								endif; ?>
							</div>
							<div class="resources-list__resource--type">
								<?php $resource_status = get_field('status', $resource_ID );
								if($resource_status === 'pilot' ) :
									_e( 'Short Solution', 'weadapt' );
								else:
									_e( 'Detailed Solution', 'weadapt' );
								endif;
								?>
							</div>
						</div>
					</div>
				<?php endif;
			endif; ?>
		 </div>
		 <?php
		 	 $class = 'resources-list__resource--btn';
		 	 $icon  = 'icon-arrow-right-button';
		 	 $style = 'default';
			 $link  = [
				'url' => $resource_url,
				'title' => __( $resource_cta_label, 'weadapt' ),
				'target' => '',
				'icon' => 'icon-arrow-right-button',
			];
			if ( ! empty( $link ) ) {
				echo get_button($link, $style, $class, $icon);
			}
		?>
	</div>
</div>
