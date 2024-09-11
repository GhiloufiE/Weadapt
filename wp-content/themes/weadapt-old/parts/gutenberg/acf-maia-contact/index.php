<?php
/**
 * Contribute Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<div class="">

	<div class="container">
		<div class="contribute__title-wrap">
			<?php
			echo $block_object->title('contribute__heading', 'h2');
			echo $block_object->desc('contribute__description');
			?>
		</div>

		<?php if ( have_rows( 'contribute_cards' ) ): ?>
			<div class="row">
				<?php
					$index = 1;

					while ( have_rows( 'contribute_cards' ) ) : the_row();
						$args = array(
							'index'           => $index,
							'button_icon'     => 'icon-arrow-right-button',
						);

						$index++;
					?>
						<div class="contribute__card-wrap col-12 ">
							
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

			.contact-maia{
				padding-top: 9.375rem;
   				padding-bottom: 2rem;
			}
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
			.wp-block-button--template{
			display: -webkit-inline-box !important;
			padding-right: 15px;
				}

			.icon-text-card__content p {
				margin:0 !important; 
				padding-top: 1rem;
			}
			.icon-text-card {
			border-radius: 1.25rem;}
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
			<?php if ( ! empty( $follow_us = get_sub_field( 'follow_us_text' ) ) ) : ?>
				<?php echo $follow_us ?>
			<?php endif; ?>
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
						</div>
					<?php endwhile;
				?>
				<div class="contribute__card-wrap contribute__card-wrap__message col-12"><?php _e( 'No match', 'weadapt' ); ?></div>
			</div>
		<?php endif; ?>
	</div>
	</div>
</section>