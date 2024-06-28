<?php
/**
 * Single resources
 *
 * @package WeAdapt
 */
?>

<?php load_inline_styles( __DIR__, 'single-resources' ); ?>

<?php if ( ! empty( $document_list = get_field( 'document_list' ) ) && count( $document_list ) > 1 ) :
	$resources_list = array_slice( $document_list, 1 );
?>

<?php if ( ! empty( $resources_list ) ) : ?>
	<div class="single-resources">
		<div class="single-resources__row">
			<?php foreach ( $resources_list as $resources_item ) :
				if ( ! empty( $file_ID = $resources_item['file'] ) ) :
			?>
				<div class="single-resources__box">
					<span class="single-resources__box-icon"><?php echo get_img( 'icon-download' ); ?></span>

					<?php if ( ! empty( $description = $resources_item['description'] ) ) : ?>
						<h3 class="single-resources__box-title"><?php echo wp_kses_post( $description ); ?></h3>
					<?php endif; ?>

					<span class="single-resources__box-text" data-resource-id="<?php echo esc_attr( $file_ID ); ?>">
						<?php echo get_download_count( $file_ID ); ?>
					</span>

					<?php
						echo get_button( [
							'url'        => wp_get_attachment_url( $file_ID ),
							'title'      => __( 'Download', 'weadapt' ),
							'target'     => '',
							'attributes' => [
								'download'     => get_the_title( $file_ID ),
								'data-file-id' => $file_ID,
							]
						], '', 'download', 'icon-download-light-small' );
					?>
				</div>
			<?php endif;
				endforeach;
			?>
		</div>
	</div>
<?php endif; ?>
<?php endif; ?>