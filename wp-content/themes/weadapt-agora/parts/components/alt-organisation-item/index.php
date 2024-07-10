<?php
/**
 * Contributors Item (ALT)
 *
 * @package WeAdapt
 */
$organisation_ID           = ! empty( $args['org_ID'] ) ? $args['org_ID'] : 0;
$show_description          = isset( $args['show_description'] ) ? $args['show_description'] : 'true';

if ( ! empty( $organisation_ID ) ) :
	$organisation_url  		= get_the_permalink( $organisation_ID );
	$organisation_image   	= get_the_post_thumbnail( $organisation_ID, 'medium' );
	$organisation_excerpt 	= has_excerpt() ? get_the_excerpt() : '';
	$organisation_content 	= get_the_content(null, true, $organisation_ID);
endif;
?>

<div class="organisations-list__organisation">
	<?php load_inline_styles( __DIR__, 'alt-organisation-item' ); ?>
	<div class="organisations-list__organisation-container">
		 <div class="organisations-list__organisation--image">
			<?php echo $organisation_image; ?>
		 </div>
		 <?php if ($show_description === 'true') : ?>
			 <div class="organisations-list__organisation--text">
				<?php if ( ! empty( $organisation_excerpt ) ) : ?>
					<div class="organisations-list__organisation--excerpt" >
						<?php echo $organisation_excerpt; ?>
					</div>
				<?php elseif( ! empty($organisation_content) ) : ?>
					<div class="organisations-list__organisation--excerpt" >
						<?php echo wp_trim_words( $organisation_content, 18, '...' ); ?>
					</div>
				<?php endif; ?>
			 </div>
		 <?php endif; ?>
		 <?php
		 	 $class = 'organisations-list__organisation--btn';
			 $link = [
				'url' => get_permalink( $organisation_ID ),
				'title' => __( 'Read more', 'weadapt' ),
				'target' => '',
			];
			if ( ! empty( $link ) ) {
				echo get_button($link, $class);
			}
		?>
	</div>
</div>