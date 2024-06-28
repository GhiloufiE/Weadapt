<?php
/**
 * Block Video Rows
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<?php if ( have_rows( 'rows' ) ) :
		while( have_rows( 'rows' ) ) : the_row(); ?>
			<?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
				<h3 class="<?php echo esc_attr( $name ); ?>__title"><?php echo $title; ?></h3>
			<?php endif; ?>

			<?php if ( have_rows( 'video' ) ) : ?>
				<div class="<?php echo esc_attr( $name ); ?>__row">
					<?php while( have_rows( 'video' ) ) : the_row(); 
						$video_url   = get_sub_field( 'media' );
						$title       = get_sub_field( 'title' );
						$description = get_sub_field( 'description' );

						get_part( 'components/video-item/index', [
							'video_url'   => $video_url,
							'title'       => $title,
							'description' => $description,
						] );
						
					endwhile; ?>
				</div>
			<?php endif; 
		endwhile;
	endif; ?>
</section>