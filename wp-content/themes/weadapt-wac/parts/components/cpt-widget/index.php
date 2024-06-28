<?php
/**
 * CPT Widget
 *
 * @package WeAdapt
 */
$title     = ! empty( $args['title'] )     ? $args['title']     : '';
$cpt_IDs   = ! empty( $args['cpt_IDs'] )   ? $args['cpt_IDs']   : [];
$buttons   = ! empty( $args['buttons'] )   ? $args['buttons']   : [];
$more_link = ! empty( $args['more_link'] ) ? $args['more_link'] : [];
$colored_bg = ! empty( $args['colored_bg'] ) ? $args['colored_bg'] : false;

if ( ! is_array( $cpt_IDs ) ) {
	$temp_cpt_ID = $cpt_IDs;

	$cpt_IDs = [$temp_cpt_ID];
}

if ( ! empty( $cpt_IDs )  ) :
?>

<div class="cpt-widget">
	<?php load_inline_styles( __DIR__, 'cpt-widget' ); ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h2 class="cpt-widget__title widget-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<div class="cpt-widget__row <?php echo $colored_bg ? 'colored-bg' : ''; ?>">
		<?php foreach ( $cpt_IDs as $cpt_ID ) {
			echo get_part( 'components/cpt-cta/index', [
				'cpt_ID'  => $cpt_ID,
				'buttons' => $buttons
			] );
		} ?>
	</div>

	<?php if ( ! empty( $more_link ) ) : ?>
		<div class="cpt-widget__more">
			<a class="cpt-widget__more-link" href="<?php echo esc_url( $more_link[0] ); ?>"><?php echo esc_html( $more_link[1] ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php endif;
