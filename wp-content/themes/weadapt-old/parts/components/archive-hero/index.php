<?php
load_inline_styles( __DIR__, 'button-comment' );
load_blocks_script( 'button-comment', 'weadapt/button-comment' );
wp_enqueue_script( 'comment-reply' );

$post_ID    = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;
$class      = 'wp-block-button wp-block-button--template has-icon-left';
$link_class = 'wp-block-button__link';

add_action( 'popup-content', function() {
    echo get_part( 'components/popup/index', [ 'template' => 'post-creation' ] );
} );

$type = isset( $args['type'] ) ? $args['type'] : '';

$queried_object = get_queried_object();

$title    = ! empty( $queried_object->term_id ) ? $queried_object->name : get_field( 'section_title' );
$excerpt  = ! empty( $queried_object->term_id ) ? $queried_object->description : get_field( 'section_description' );
$main_img = get_img( get_field( 'section_image' ) );

if ( empty( $main_img ) && ! empty( $queried_object->term_id ) ) {
    $main_img = get_img( get_field( 'image', $queried_object ) );
}

if ( empty( $main_img ) && ! empty( $queried_object->term_id ) ) {
    $main_img = get_img( get_field( "image_$queried_object->taxonomy", 'options' ) );
}

$post_ID    = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;
$class      = 'wp-block-button wp-block-button--template has-icon-left';
$link_class = 'wp-block-button__link';
?>

<section class="archive-hero">
    <?php load_inline_styles( __DIR__, 'archive-hero' ); ?>
    <div class="archive-hero__container container">
        <div class="archive-hero__row row">
            <div class="col-12 col-lg-6 archive-hero__left">
                <div class="archive-hero__left-inner">
                    <h1 class="archive-hero__title" id="main-heading"><?php echo $title; ?></h1>

                    <?php if ( ! empty( $excerpt ) ) : ?>
                        <div class="archive-hero__excerpt"><?php echo $excerpt; ?></div>
                    <?php endif; ?>

                    <div class="<?php echo esc_attr( $class ); ?>">
                        
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 archive-hero__right">
                <?php if ( ! empty( $main_img ) ) : ?>
                    <figure class="archive-hero__image">
                        <?php echo $main_img; ?>
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.querySelector('[data-popup="post-creation"]');
        const popup = document.querySelector('.popup[data-popup-content="post-creation"]');

        if (button && popup) {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default button action
                popup.classList.add('active');
                popup.setAttribute('aria-hidden', 'false');
            });
        }
    });
</script>