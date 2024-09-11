<?php
/**
 * Block CTA
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

$form = get_field( 'form' );
$use_search = get_field( 'use_search' );
if ( $use_search ) {
	$use_search = 'form';
} else {
	$use_search = 'blocks';
}
?>

<section <?php echo $attr; ?>>
	<?php load_inline_styles_shared( 'forms' ); ?>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="<?php echo esc_attr( $name ); ?>__header">
		<div class="container">
			<?php if ( ! empty( $form['section_image'] ) ) : ?>
				<div class="<?php echo esc_attr( $name ); ?>__image">
					<?php echo get_img( $form['section_image'], 'large' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="container">
		<form role="search" aria-label="<?php esc_attr_e( 'Search Form', 'weadapt' ); ?>" class="<?php echo esc_attr( $name ); ?>-form" data-location="<?php echo esc_attr( $use_search); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'weadapt' ); ?></span>

			<?php if ( ! empty( $form['title'] ) ) : ?>
				<h2 class="<?php echo esc_attr( $name ); ?>-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
			<?php endif; ?>

			<div class="<?php echo esc_attr( $name ); ?>-form__field">
				<input type="search" placeholder="<?php esc_attr_e( 'Search', 'weadapt' ); ?>" class="<?php echo esc_attr( $name ); ?>-form__input">
				<div class="<?php echo esc_attr( $name ); ?>-form__icon"><?php echo get_img( 'icon-search' ); ?></div>
				<button type="reset" class="faq-form__reset" aria-label="<?php esc_attr_e( 'Reset', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
			</div>

			<?php if ( ! empty( $form['section_description'] ) ) : ?>
				<div class="<?php echo esc_attr( $name ); ?>-form__description">
					<?php echo $form['section_description']; ?>
				</div>
			<?php endif; ?>
		</form>

		<?php if ( have_rows( 'faqs' ) ) : ?>
			<div class="<?php echo esc_attr( $name ); ?>__row row">
				<?php while ( have_rows( 'faqs' ) ) : the_row(); ?>
					<div class="<?php echo esc_attr( $name ); ?>__col col-12 col-md-4">
						<?php if ( ! empty ( $title = get_sub_field( 'title' ) ) ) : ?>
							<h4 class="<?php echo esc_attr( $name ); ?>__title">
								<?php echo esc_html( $title ) ; ?>
							</h4>
						<?php endif; ?>

						<?php if ( ! empty ( $list = get_sub_field( 'list' ) ) ) : ?>
							<ul class="<?php echo esc_attr( $name ); ?>__list">
								<?php foreach( $list as $key => $item_id) : ?>
									<li class="<?php echo esc_attr( $name ); ?>__item <?php echo ( $key > 2 ) ? 'hidden' : ''; ?>">
										<a href="<?php echo get_the_permalink( $item_id ); ?>">
											<?php echo get_the_title( $item_id ); ?>
										</a>
									</li>
								<?php endforeach; ?>

								<li class="<?php echo esc_attr( $name ); ?>__list__message">
									<span><?php _e( 'No match', 'weadapt' ); ?></span>
								</li>
							</ul>

							<?php if ( count( $list ) > 3 ) : ?>
								<?php
									echo get_button( array(
										'url' => '#',
										'target' => '_self',
										'title'  => "
											<span class='view-all'>". __( 'View all questions', 'weadapt' ) ."</span>
											<span class='minimize-all'>". __( 'Minimize all questions', 'weadapt' ) ."</span>
										"
									), 'icon-small', esc_attr( $name ) . '__button', 'icon-chevron-down');
								?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>