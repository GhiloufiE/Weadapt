<?php
/**
 * Theme Header
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); theme_popup_attributes(); ?> class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php if ( apply_filters( 'use_google_fonts', true ) ) : ?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php endif; ?>

	<?php wp_head(); ?>

	<?php settings_colors( 'styles' ); ?>
	<?php echo ( ( substr( home_url(), -4 ) === '/web' ) || current_user_can( 'administrator' ) ) ? '' : get_field( 'header_html', 'options' ); ?>
</head>
<?php
	$body_classes = ['using-mouse'];

	if ( isset( $_COOKIE['weadapt-dark-mode'] ) && wp_validate_boolean( $_COOKIE['weadapt-dark-mode'] ) ) {
		$body_classes[] = 'theme--dark';
	}
?>
<body <?php body_class( implode( ' ', $body_classes ) ); ?>>
	<?php echo ( ( substr( home_url(), -4 ) === '/web' ) || current_user_can( 'administrator' ) ) ? '' : get_field( 'body_html', 'options' ); ?>
	<?php wp_body_open(); ?>

	<div id="page">
		<a class="skip-link" href="#content"><?php _e( 'Skip to content', 'weadapt' ); ?></a>
		<?php echo get_part( 'components/header/index' ); ?>