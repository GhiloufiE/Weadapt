<?php
/**
 * Block Colored Cards
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) . ' title-' . get_field( 'title_size' ) );
$max_columns  = get_field('max_columns_number');
$name         = $block_object->name();
$cards = get_field( 'cards' );

?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">

		<header class="section-header has-text-align-left">
			<?php
				echo $block_object->subtitle( "{$name}__subtitle" );
				echo $block_object->title( "{$name}__heading" );
				echo $block_object->desc( "{$name}__descriprion" );
			?>
		</header>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="row <?php echo esc_attr( $name );?>__row">
				<?php if ( ! empty( $cards ) ) : ?>
						<?php foreach ($cards as $card) : ?>
                            <div class="col-12 col-md-6 <?php if ($max_columns == 3) { echo 'col-lg-4'; } ?>">
                                <?php echo get_part('components/awb-image-card-item/index', $card ); ?>
                            </div>
                        <?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
