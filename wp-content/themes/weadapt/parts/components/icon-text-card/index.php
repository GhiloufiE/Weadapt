<?php

$button_icon     = isset( $args['button_icon'] ) ? $args['button_icon'] : '';
$index 			 = isset( $args['index'] ) ? $args['index'] : 0;

$cards_numbering = get_field( 'cards_numbering' );

load_inline_styles( __DIR__, 'icon-text-card' ); ?>

<div class="icon-text-card">
	<div class="icon-text-card__wrapper">
		<?php if ( $cards_numbering ) :
			$before_text = get_sub_field( 'before_text' )
		?>
			<div class="icon-text-card__num<?php echo $before_text ? ' has-before-text' : ''; ?>">
				<span>
					<?php if ( ! empty( $before_text ) ) : ?>
						<span class="before-text">
							<?php echo esc_html( $before_text ); ?>
						</span>
					<?php endif; ?>

					<?php echo $index; ?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $icon_id = get_sub_field( 'icon' ) ) ) : ?>
			<div class="icon-text-card__icon">
				<?php echo get_img( $icon_id ); ?>
			</div>
		<?php endif; ?>
		<style>
			.icon-social-card__links a:not([class]){
				background-image: none !important;
			}
			.icon-social-card__links{
			    list-style: none;
				padding: 0px;
				display: -webkit-inline-box;
				
			}
			.icon-social-card__links li{
				padding-right: 15px;
			}
			.icon-text-card__content .wp-block-button {
   			 margin-top: 0.5rem;
			}
			.wp-block-button--template{
			display: -webkit-inline-box !important;
			padding-right: 15px;
			float: inline-start;
				}
		</style>
		<?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
			<h4 class="icon-text-card__title"><?php echo $title ?></h4>
		<?php endif; ?>
		
		<div class="icon-text-card__content">
			<?php if ( ! empty( $text = get_sub_field( 'text' ) ) ) : ?>
				<?php echo $text ?>
			<?php endif; ?>

			<?php if ( have_rows( 'links' ) ) : ?>
				<ul class="icon-text-card__links">
					<?php while ( have_rows('links') ) : the_row();
						$link = get_sub_field( 'link' );

						if ( ! empty( $link ) ) :
							$target = $link['target'] ? $link['target'] : '_self';
					?>
						<li>
							<a href="<?php echo esc_url( $link['url'] ); ?>" class="link" target="<?php echo esc_attr( $target ); ?>">
								<?php echo esc_html( $link['title'] ); ?>
							</a>
						</li>
					<?php endif; endwhile; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $button = get_sub_field( 'button' ) ) ) {
				if ( ! empty( $button_icon ) ) {
					echo get_button( $button, '', 'is-style-icon-small', $button_icon );
				}
				else {
					echo get_button( $button, '', 'is-style-small' );
				}
			} ?>
			<?php if ( have_rows( 'images' ) ) : ?>
			<ul class="icon-social-card__links">
					<?php while ( have_rows('images') ) : the_row();
						$url_social = get_sub_field( 'social_media_link' );
						$image = get_sub_field( 'image' );
					?>
					<?php
					if( $url_social ):
					$image_social = $image['url'];
					$url = $url_social['url'];?>
					<li>
					<a href="<?php echo esc_url($url); ?>">
						<img src="<?php echo esc_url($image_social); ?>"  />
					</a> 
					</li>
					<?php endif; ?>
					<?php  endwhile; ?>
		</ul>
		<?php endif; ?>
		</div>
	</div>
</div>