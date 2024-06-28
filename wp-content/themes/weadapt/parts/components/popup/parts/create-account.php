<div class="popup__header">
	<button class="back" data-popup="sign-in" aria-label="<?php _e( 'Back', 'weadapt' ); ?>"><?php echo get_img( 'icon-arrow-left' ); ?></button>
	<button class="close" data-popup="create-account" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<h2 class="popup__header__title" id="create-account"><?php _e( 'Create an account', 'weadapt' ); ?></h2>
</div>
<div class="popup__content">
	<?php the_field( 'popup_create_an_account', 'options' ); ?>

	<?php echo get_part( 'components/sign-up/index', [ 'template' => 'create-account' ] ); ?>

	<?php echo get_part('components/popup/parts/powered-by'); ?>
</div>
