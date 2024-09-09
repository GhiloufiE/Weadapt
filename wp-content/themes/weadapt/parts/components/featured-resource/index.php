<?php
/**
 * Featured Resource
 *
 * @package WeAdapt
 */
?>
<?php if ( ! empty( $document_list = get_field( 'document_list' ) ) ) :
$document_item = $document_list[0];

if ( ! empty( $file_ID = $document_item['file'] ) ) : ?>
<div class="featured-resource">
    <?php load_inline_styles( __DIR__, 'featured-resource' ); ?>
    <?php load_blocks_script( 'featured-resource', 'weadapt/featured-resource' ); ?>

    <h2 class="featured-resource__title widget-title"><?php _e( 'Featured Resource', 'weadapt' )?></h2>

    <div class="featured-resource__row">
        <div class="single-resource">
            <?php
                $image = wp_get_attachment_image_src( $file_ID, 'medium' );

                if ( ! empty( $image ) ) : ?>
                    <a href="<?php echo wp_get_attachment_url( $file_ID ); ?>" target="_blank" class="single-resource__img">
                        <?php echo '<img src="' . $image[0] . '" alt="' . get_post_meta( $file_ID, '_wp_attachment_image_alt', true ) . '">'; ?>
                    </a>
                <?php endif;
            ?>

            <div class="single-resource__content">
                <?php if ( ! empty( $description = $document_item['description'] ) ) : ?>
                    <h3 class="single-resource__title">
                        <a style="color:black" href="<?php echo wp_get_attachment_url( $file_ID ); ?>" target="_blank">
                            <?php echo wp_kses_post( $description ); ?>
                        </a>
                    </h3>
                <?php endif; ?>

                <div class="single-resource__text" data-resource-id="<?php echo esc_attr( $file_ID ); ?>">
                    <?php echo get_download_count( $file_ID ); ?>
                </div>

                <?php
                    echo get_button( [
                        'url'        => wp_get_attachment_url( $file_ID ),
                        'title'      => __( 'Download', 'weadapt' ),
                        'target'     => '_blank', // Corrected attribute name
                        'attributes' => [
                            'data-file-id' => $file_ID,
                        ]
                    ], '', 'download', 'icon-download-light-small' );
                ?>
            </div>
        </div>
    </div>
</div>

<?php endif; endif; ?>
