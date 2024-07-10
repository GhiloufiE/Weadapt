<?php
	$image_id           = isset( $args['image_id'] ) ? $args['image_id']                     : '';
	$post_type          = isset( $args['post_type'] ) ? $args['post_type']                   : '';
	$title              = isset( $args['title'] ) ? $args['title']                           : '';
	$excerpt            = isset( $args['excerpt'] ) ? $args['excerpt']                       : '';
	$members_count      = isset( $args['members_count'] ) ? $args['members_count']           : '';
	$articles_count     = isset( $args['articles_count'] ) ? $args['articles_count']         : '';
	$case_stadies_count = isset( $args['case_stadies_count'] ) ? $args['case_stadies_count'] : '';
	$tag                = isset( $args['tag'] ) ? $args['tag']                               : '';
?>

<article class="cpt-list-item theme-list-item">
	<?php
		load_inline_styles( __DIR__, 'theme-list-item' );
	?>

	<?php if ( ! empty( $image_id ) ) : ?>
		<div class="cpt-list-item__image">
			<a href="#" class="cpt-list-item__image-link">
				<?php // echo get_img( $image_id, 'cpt-list-item' ); ?>

				<img src="<?php echo esc_url( get_template_directory_uri() ) . '/assets/images/temp/article-list-item.jpg'; ?>" alt="">
			</a>
		</div>
	<?php endif; ?>

	<div class="cpt-list-item__content">
		<?php if ( ! empty( $post_type ) ) : ?>
			<div class="cpt-list-item__post-type">
				<a href="#" class="cpt-list-item__post-type-link"><?php echo esc_html ( $post_type ); ?></a>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $title ) ) : ?>
			<h4 class="cpt-list-item__title">
				<a href="#" class="cpt-list-item__link"><?php echo wp_kses_post( $title ); ?></a>
			</h4>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="cpt-list-item__excerpt">
				<?php echo wp_kses_post( $excerpt ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $members_count ) || ! empty( $articles_count ) || ! empty( $case_stadies_count ) ) : ?>
			<div class="cpt-list-item__info">
				<?php if ( ! empty( $members_count ) ) : ?>
					<div class="cpt-list-item__info-item">
						<?php echo get_img( 'icon-user' ); ?>
						<a href="#" class="cpt-list-item__link"><?php printf( '%s %s', esc_html( $members_count ), __( 'Members', 'weadapt' ) ); ?></a>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $articles_count ) ) : ?>
					<div class="cpt-list-item__info-item">
						<?php echo get_img( 'icon-edit-pencil' ); ?>
						<a href="#" class="cpt-list-item__link"><?php printf( '%s %s', esc_html( $articles_count ), __( 'Articles', 'weadapt' ) ); ?></a>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $case_stadies_count ) ) : ?>
					<div class="cpt-list-item__info-item">
						<?php echo get_img( 'icon-glob' ); ?>
						<a href="#" class="cpt-list-item__link"><?php printf( '%s %s', esc_html( $case_stadies_count ), __( 'Case studies', 'weadapt' ) ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $tag ) ) : ?>
			<div class="cpt-list-item__tag">
				<a href="#" class="cpt-list-item__tag-item">
					<?php echo esc_html( $tag ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ( isset($args['buttons']) && !empty($args['buttons']) ) : ?>
			<div class="cpt-list-item__actions">
			<?php
				foreach ( $args['buttons'] as $button ) {
					echo get_button($button[0], $button[1], $button[2], $button[3]);
				}
			?>
			</div>
		<?php endif; ?>
	</div>
</article>