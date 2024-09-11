<?php
/**
 * Text With Sidebar
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

$cta_widget = get_field( 'cta_widget' );

$has_border = get_field( 'has_border' );
$sticky_sidebar = get_field( 'sticky_sidebar' );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading" ); ?>

		<div class="row <?php echo esc_attr( $name ); ?>__row <?php echo $sticky_sidebar ? 'is-sticky' : ''; ?>">
			<div class="col-12 col-lg-7 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<InnerBlocks />
			</div>

			<?php if ( ! empty( $cta_widget ) ) : ?>
				<div class="col-12 col-lg-5 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--widget <?php echo $has_border ? 'has-border' : ''; ?>">
					<?php
						get_part('components/cta-widget/index', [
							'custom_widget' => $cta_widget
						]);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>