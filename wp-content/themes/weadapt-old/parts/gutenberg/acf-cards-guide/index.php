<?php
/**
 * Cards Guide Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="cards-guide__main-row">
			<div class="cards-guide__left">

				<div class="cards-guide__title-wrap">
					<?php
					echo $block_object->title('cards-guide__heading', 'h3');
					echo $block_object->desc('cards-guide__description');
					echo $block_object->button();
					?>
				</div>

				<?php if ( have_rows( 'main_cards' ) ): ?>
					<div class="cards-guide__sub-row">
						<?php while ( have_rows( 'main_cards') ): the_row();
							$args = array(
								'title'       => get_sub_field( 'title' ),
								'title_size'  => 'small',
								'text'        => get_sub_field( 'text' ),
								'button'      => get_sub_field( 'button' ),
								'button_icon' => 'icon-arrow-right-button',
							); ?>
							<div class="cards-guide__main-card-wrap">
								<?php get_part('components/icon-text-card/index', $args); ?>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="cards-guide__right">
				<?php if ( have_rows( 'side_cards' ) ):
					while ( have_rows( 'side_cards' ) ): the_row();
						$args = array(
							'title'      => get_sub_field( 'title' ),
							'title_size' => 'small',
							'text'       => get_sub_field( 'text' ),
							'button'     => get_sub_field( 'button' ),
						); ?>
						<div class="cards-guide__side-card-wrap">
							<?php get_part('components/icon-text-card/index', $args); ?>
						</div>
					<?php endwhile;
				endif; ?>
			</div>
		</div>
	</div>
</section>