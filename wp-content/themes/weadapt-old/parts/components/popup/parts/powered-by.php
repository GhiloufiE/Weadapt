<?php $poweredByText = get_field( 'popup_powered_by', 'options' ); ?>

<?php if(!empty($poweredByText) and !is_main_site()) : ?>
	<div class="popup__separator"></div>
	<div class="popup__powered-by">
		<div class="popup__powered-by_content">
			<?php echo $poweredByText; ?>
			<?php
				 $link = [
					'url' => network_home_url(),
					'title' => __( 'Visit weADAPT', 'weadapt' ),
					'target' => '_blank',
				];
				if ( ! empty( $link ) ) {
					echo get_button($link);
				}
			?>
		</div>
	</div>
<?php endif; ?>
