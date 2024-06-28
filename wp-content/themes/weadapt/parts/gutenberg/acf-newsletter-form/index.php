<?php
/**
 * Block Newsletter Form
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();
?>

<section id="newsletter-form" <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<header class="section-header has-text-align-left">
			<?php
				echo $block_object->subtitle( "{$name}__subtitle" );
				echo $block_object->title( "{$name}__heading" );
			?>
		</header>

		<?php if ( ! empty( $form_shortcode = get_field( 'form_shortcode' ) ) ) : ?>
			<?php echo do_shortcode( $form_shortcode ); ?>
		<?php endif;?>
	</div>
</section>