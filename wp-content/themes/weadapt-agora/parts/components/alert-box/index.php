<?php
/**
 * Alert box
 *
 * @package WeAdapt
 */
$text = isset( $args['text'] ) ? $args['text'] : '';

if ( !empty($text) ) :
?>
<div class="alert-box">
	<?php load_inline_styles( __DIR__, 'alert-box' ); ?>
	<p class="alert-box__text"><?php echo $text; ?></p>
</div>
<?php
endif;