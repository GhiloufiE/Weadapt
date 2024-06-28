<?php
/**
 * Logos Block
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
		<?php if ( have_rows( 'logos' ) ): ?>
			<div class="row logos__row">
				<?php while ( have_rows( 'logos' ) ) : the_row(); ?>
					<?php if ( ! empty( $image_id = get_sub_field( 'logo', 'medium' ) ) ) : ?>
						<div class="logos__col col-4 col-md-3">
							<span>
								<?php echo get_img( $image_id ); ?>
							</span>
						</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>