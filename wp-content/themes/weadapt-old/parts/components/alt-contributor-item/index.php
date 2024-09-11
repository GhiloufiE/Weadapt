<?php
/**
 * Contributors Item (ALT)
 *
 * @package WeAdapt
 */
$contributor_ID           = ! empty( $args['user_ID'] ) ? $args['user_ID'] : 0;

if ( ! empty( $contributor_ID ) ) :
	$contributor_url = get_author_posts_url( $contributor_ID );
?>

<div class="contributors-list__contributor">
	<?php load_inline_styles( __DIR__, 'alt-contributor-item' ); ?>
	<div class="contributors-list__contributor-container">
		 <div class="contributors-list__contributor--image">
		 	<a href="<?php echo $contributor_url; ?>" class="info-widget-user__avatar__link">
        		<?php echo get_avatar( $contributor_ID, 98 ); ?>
        	</a>
		 </div>
		 <div class="contributors-list__contributor--text">
		 	<?php if ( ! empty( $contributor_name = get_user_name( $contributor_ID ) ) ) : ?>
		 		<a href="<?php echo $contributor_url; ?>" class="info-widget-user__name__link">
					<h4 class="contributors-list__contributor--name" >
						<?php echo $contributor_name; ?>
					</h4>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $contributor_description = get_user_excerpt( $contributor_ID, 125 ) ) ) : ?>
				<div class="contributors-list__contributor--bio" >
					<?php echo $contributor_description; ?>
				</div>
			<?php endif; ?>
		 </div>
	</div>
</div>

<?php endif; ?>