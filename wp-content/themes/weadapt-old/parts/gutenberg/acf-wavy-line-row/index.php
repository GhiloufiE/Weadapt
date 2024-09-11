<?php
/**
 * Block Wavy Line List
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<header class="section-header has-text-align-left">
			<?php
				echo $block_object->subtitle( "{$name}__subtitle" );
				if ( ! empty( $subtitle_desc = get_field( 'subtitle_description' ) ) ) : ?>
					<div class="wavy-line-row__subtitle-desc"><?php echo $subtitle_desc; ?></div>
				<?php endif;
				echo $block_object->title( "{$name}__heading" );
				echo $block_object->desc( "{$name}__descriprion" );
			?>
		</header>

		<?php if ( have_rows( 'row' ) ) : ?>
			<div class="row <?php echo esc_attr( $name ); ?>__row">
				<?php while( have_rows( 'row' ) ) : the_row();
					$image       = get_sub_field( 'image' );
					$title       = get_sub_field( 'title' );
					$description = get_sub_field( 'description' );
					$link        = get_sub_field( 'link' );
				?>
					<div class="col-12 col-md-4 <?php echo esc_attr( $name ); ?>__col">
						<div class="item">
							<?php if ( ! empty( $image ) ) : ?>
								<div class="item__image"><?php echo get_img( $image, 'thumbnail' ); ?></div>
							<?php endif; ?>

							<?php if ( ! empty( $title ) ) : ?>
								<h3 class="item__title"><?php echo $title; ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $description ) ) : ?>
								<div class="item__description"><?php echo $description; ?></div>
							<?php endif; ?>

							<?php
								if( $link ):
									$link_url = $link['url'];
									$link_title = $link['title'];
									$link_target = $link['target'] ? $link['target'] : '_self';
							?>
								<div class="item__link">
									<a class="item__link-item" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>