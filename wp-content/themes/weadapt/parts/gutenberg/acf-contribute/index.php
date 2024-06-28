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
						<div class="contribute__card-wrap col-12 col-md-6 col-lg-4">
							<?php get_part( 'components/icon-text-card/index', $args ); ?>
						</div>
					<?php endwhile;
				?>
				<div class="contribute__card-wrap contribute__card-wrap__message col-12"><?php _e( 'No match', 'weadapt' ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>