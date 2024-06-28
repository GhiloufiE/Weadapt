<?php

/**
 * Load CIP Content
 */
function load_cip_content() {
	$cip_id    = ! empty( $_POST['cip_id'] ) ? intval( $_POST['cip_id'] ) : 0;
	$cip_title = ! empty( $_POST['cip_title'] ) ? esc_attr( $_POST['cip_title'] ) : '';

	ob_start();
	?>
	<main id="page-content" class="page-content page-content--cip">
		<div id="content" tabindex="-1" class="page-content__wrapper">
			<h1 class="cip__title"><?php echo sprintf( __( 'Climate information portal data for %s', 'weadapt' ), $cip_title ); ?></h1>

			<iframe src="https://cip.csag.uct.ac.za/webclient2/nodes/weadapt?standalone=true&folder_id=24&extent=<?php echo $cip_id; ?>" frameborder="0"></iframe>

			<h2 class="cip__subtitle"><?php _e( 'More Information and Future Projections', 'weadapt' ); ?></h2>
			<p><?php _e( 'If you would like to get more information on the observed climate for this location and explore projections of future climate conditions, downscaled from the outputs of multiple GCMs, then follow the link below, which will take you into the Climate Information Portal.', 'weadapt' ); ?></p>
			<p><a href="http://cip.csag.uct.ac.za/webclient2/datasets/africa-merged/#nodes/seasonality-cmip3?folder_id=24&extent=<?php echo $cip_id; ?>" target="_blank"><?php _e( 'View on CIP', 'weadapt' ); ?></a></p>
		</div>
	</main>
	<?php
	$output_html = ob_get_clean();

	echo json_encode( [
		'output_html' => $output_html
	] );

	die();
}
add_action( 'wp_ajax_load_cip_content', 'load_cip_content' );
add_action( 'wp_ajax_nopriv_load_cip_content', 'load_cip_content' );