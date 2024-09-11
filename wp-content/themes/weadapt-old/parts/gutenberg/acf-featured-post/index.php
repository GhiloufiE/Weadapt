<?php
/**
 * Block Image Text
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();

$image_position = get_field( 'image_position' );
$row_alignment  = get_field( 'row_alignment' );
$hide_button_icon    = get_field( 'button_icon' );

$post_ID = get_field('post');
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row alignment-<?php echo esc_attr( $row_alignment ); ?>">
			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image <?php echo esc_attr( $image_position); ?>">
				<?php if( ! $block_object->image() ) : ?>
                     <div class="section-image <?php echo $name;?>__image" ><?php echo get_the_post_thumbnail( $post_ID, 'full', array( 'class' => "{$name}__image" ) ); ?></div>
                <?php else : ?>
                       <?php echo $block_object->image( "{$name}__image" ); ?>
                <?php endif; ?>
			</div>

			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class="<?php echo esc_attr( $name ); ?>__content">

                    <?php  echo $block_object->subtitle( "{$name}__subtitle", 'h4' ); ?>
                    <h2 class="section-title <?php echo esc_attr( $name ); ?>__heading"><?php  echo get_the_title( $post_ID ); ?></h2>

                    <?php
                        $type = ucfirst( get_post_type( $post_ID ) );
                        $date_html = 'Published ' . get_the_date( 'dS M Y', $post_ID );

						if( $type === 'Event' ) {
                            $start_date = get_field( 'start_date', $post_ID );
                            $end_date   = get_field( 'end_date', $post_ID );
                            $timezone   = get_field( 'timezone', $post_ID );
							$timezoneFormatted = insertBeforeLastTwo($timezone);

							if ( ! empty( $start_date ) ) {
								$start_date_obj = new DateTime($start_date);
								$date_html = $start_date_obj->format('d/m/Y') . ' - ' . $start_date_obj->format('H:i');

								if ( ! empty( $end_date )  ) {
									$end_date_obj = new DateTime($end_date);
									$date_html .= ' ' . $timezoneFormatted . ' - ';
									if( $end_date_obj->format('d/m/Y') !== $start_date_obj->format('d/m/Y') ) {
										$date_html .=  $end_date_obj->format('d/m/Y') . ' - ';
									}
									 $date_html .=  $end_date_obj->format('H:i');
								  	 $date_html .=  ' ' . $timezoneFormatted;
								}
							}
						}
					?>

                    <div class="<?php echo $name; ?>__tags" >
                        <span class="type-tag <?php echo get_post_type( $post_ID ); ?>" ><?php echo $type; ?></span>
                        <span class="date-tag" ><?php echo $date_html; ?></span>
                    </div>

                    <div class="<?php echo $name; ?>__description" > <?php  echo get_the_excerpt( $post_ID ); ?></div>

                    <div class="wp-block-button wp-block-button--template <?php echo $name; ?>__button" >
                        <a
                        href="<?php echo get_permalink( $post_ID ); ?>"
                        class="wp-block-button__link"
                        > Read <?php echo $type; ?> <?php echo ! $hide_button_icon ? get_img('icon-arrow-right-button') : '' ?></a>
                    </div>


				</div>
			</div>
		</div>
	</div>
</section>
