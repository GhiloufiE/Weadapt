<?php
/**
 * Cards Download Block
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
		<div class="cards-download__title-wrap">
			<?php
				echo $block_object->title('cards-download__heading', 'h2');
				echo $block_object->desc('cards-download__description');
			?>
		</div>

		<?php if ( have_rows( 'cards' ) ): ?>
			<div class="row">
				<?php while ( have_rows( 'cards' ) ) : the_row(); ?>
					<div class="col-12 col-md-6 col-lg-4">
						<div class="card-download">
							<div class="card-download__wrapper">
								<?php if ( ! empty( $icon_id = get_sub_field( 'icon' ) ) ) : ?>
									<div class="card-download__icon">
										<?php echo get_img( $icon_id ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
									<h4 class="card-download__title"><?php echo $title; ?></h4>
								<?php endif; ?>

								<div class="card-download__content">
									<?php if ( ! empty( $text = get_sub_field( 'text' ) ) ) : ?>
										<?php echo $text; ?>
									<?php endif; ?>

									<?php if ( ! empty( $file = get_sub_field( 'file' ) ) ) :
										echo get_button(
											[
												'url' => $file['url'],
												'title' => __( 'Download', 'weadapt' ),
												'target' => '_blank',
												'attributes' => [
													'download' => $file['filename'],
												]
											],
											'',
											'',
											'icon-arrow-right-button'
										);
									endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>