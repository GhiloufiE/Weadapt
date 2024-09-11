<?php
/**
 * Block Colored Cards
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();

$cards = get_field( 'cards' );
$cards_left_col = [];
$cards_right_col = [];

if ( ! empty( $cards ) ) {
	$i = 0;

	foreach ( $cards as $card ) {
		if ( $i % 2 === 0 ) {
			$cards_left_col[] = $card;
		}
		else {
			$cards_right_col[] = $card;
		}

		$i++;
	}
}
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
				<?php if ( ! empty( $cards_left_col ) ) : ?>
					<div class="col-12 col-md-6 <?php echo esc_attr( $name );?>__col <?php echo esc_attr( $name );?>__col--left">
						<?php foreach ( $cards_left_col as $card ) :
							echo get_part( 'components/colored-card-item/index', $card );
						endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cards_right_col ) ) : ?>
					<div class="col-12 col-md-6 <?php echo esc_attr( $name );?>__col <?php echo esc_attr( $name );?>__col--right">
						<?php foreach ( $cards_right_col as $card ) :
							echo get_part( 'components/colored-card-item/index', $card );
						endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>