<?php
    load_blocks_script( 'popup', 'weadapt/popup' );

    $template_name = ! empty( $args['template'] ) ? esc_attr( $args['template'] ) : '';
    $is_active     = ! empty( $args['is_active'] ) ? wp_validate_boolean( $args['is_active'] ) : false;
    $popup_classes = ! empty( $args['popup_classes'] ) ? $args['popup_classes'] : [ 'popup' ];
    $form_data     = ! empty( $args['form_data'] ) ? $args['form_data'] : [];

    if ( $is_active ) {
        $popup_classes[] = 'active';
    }
?>

<div class="<?php echo implode( ' ', $popup_classes ); ?> sidebar-popup" data-popup-content="<?php echo esc_attr( $template_name ); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo esc_attr( $template_name ); ?>" aria-hidden="true">
    <?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>
    <?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
    <?php load_inline_styles( __DIR__, 'popup' ); ?>

    <div class="popup__document" role="document">
        <div class="popup__bg">
            <?php echo get_part( "components/popup/parts/$template_name", [ 'is_active' => $is_active, 'form_data' => $form_data ] ); ?>
        </div>
    </div>
</div>
<style>
.popup[aria-labelledby="post-creation"] .popup__document {
     height: 100%;
     float: right;
}
</style>