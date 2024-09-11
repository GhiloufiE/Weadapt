<?php
	$form_data = ! empty( $args['form_data'] ) ? $args['form_data'] : [];
?>

<div class="popup__header">
	<button class="close" data-popup="form-popup" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<?php if ( ! empty( $title = $form_data['title'] ) ) : ?>
		<h2 class="popup__header__title" id="form-popup"><?php echo esc_html( $title );?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $description = $form_data['description'] ) ) : ?>
		<?php echo wp_kses_post( $description ); ?>
	<?php endif; ?>
</div>

<?php if ( ! empty( $shortcode = $form_data['shortcode'] ) ) : ?>
	<div class="popup__content">
		<div class="cf7-form" data-i18n="<?php _e( 'cannot be empty', 'weadapt' ); ?>">
			<?php echo do_shortcode( $shortcode ); ?>
		</div>
	</div>
<?php endif; ?>
