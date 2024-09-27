<?php
/**
 * CPT CTA
 *
 * @package WeAdapt
 */

$cpt_ID  = ! empty( $args['cpt_ID'] )  ? $args['cpt_ID']  : 0;
$buttons = ! empty( $args['buttons'] ) ? $args['buttons'] : [];

// Check if the post exists
if ( ! empty( $cpt_ID ) && ! empty( get_post($cpt_ID) ) ) :
	$link_url  = get_the_permalink( $cpt_ID );
	$is_published = ( get_post_status( $cpt_ID ) === 'publish' ); 
	$publish_to   = get_field('publish_to', $cpt_ID); 
	$current_blog_ID = get_current_blog_id(); 
	$should_display_link = $is_published && (empty($publish_to) || in_array($current_blog_ID, $publish_to)); 
	$add_class = ( 'organisation' === get_post_type( $cpt_ID ) && 'draft' === get_post_status( $cpt_ID ) ) ? ' is-draft' : '';
?>

<div class="cpt-cta<?php echo esc_attr( $add_class ); ?>">
	<?php load_inline_styles( __DIR__, 'cpt-cta' ); ?>

	<?php if ( ! empty( $image = get_the_post_thumbnail( $cpt_ID, 'medium' ) ) ) : ?>
		<div class="cpt-cta__img">
			<?php if ( $should_display_link ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="cpt-cta__img-link"><?php echo $image; ?></a>
			<?php else : ?>
				<!-- Display the image without a link for unpublished posts -->
				<?php echo $image; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="cpt-cta__content">
		<?php if ( ! empty( $title = get_the_title( $cpt_ID ) ) ) : ?>
			<h3 class="cpt-cta__title">
				<?php if ( $should_display_link ) : ?>
					<a class="cpt-cta__title__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $title ); ?></a>
				<?php else : ?>
					<!-- Display the title without a link for unpublished posts -->
					<?php echo esc_html( $title ); ?>
				<?php endif; ?>
			</h3>
		<?php endif; ?>

		<?php if ( has_excerpt( $cpt_ID ) ) : ?>
			<div class="cpt-cta__text"><?php echo get_the_excerpt( $cpt_ID ); ?></div>
		<?php endif; ?>

		<?php if ( get_members_count( $cpt_ID, '', true ) ) : ?>
			<div class="cpt-cta__members">
				<span class="icon"><?php echo get_img('icon-user'); ?></span>
				<span class="text">
					<?php echo get_members_count( $cpt_ID ); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="cpt-cta__actions">
				<?php foreach ( $buttons as $button ) {
					$link = [];
					$class = 'small';

					switch ( $button ) {
						case 'join':
							get_part( 'components/button-join/index', [
								'style'   => 'outline-small',
								'join_ID' => $cpt_ID
							] );
							break;

						case 'share':
							get_part( 'components/button-share/index', [
								'style' => 'small',
								'url'   => get_permalink( $cpt_ID ),
								'type'  => get_post_type( $cpt_ID )
							] );
							break;

						case 'contact':
							if ( ! empty( $website_url = get_field( 'website_url', $cpt_ID ) ) ) {
								$link = [
									'url'   => $website_url,
									'title'  => __( 'Contact', 'weadapt' ),
									'target' => '_blank',
								];
							}
							break;

						case 'find-out-more':
							$link = [
								'url' => '#',
								'title' => __( 'Find out More', 'weadapt' ),
								'target' => '',
							];
							break;

						case 'website':
							if ( ! empty( $website_url = get_field( 'website_url', $cpt_ID ) ) ) {
								$link = [
									'url'   => $website_url,
									'title'  => __( 'Website', 'weadapt' ),
									'target' => '_blank',
								];
								$class = 'outline-small';
							}
							break;

						case 'permalink':
							if ( $should_display_link ) {
								$link = [
									'url'   => get_the_permalink( $cpt_ID ),
									'title'  => __( 'Find out more', 'weadapt' ),
									'target' => '_blank',
								];
							}
							break;
					}

					if ( ! empty( $link ) ) {
						echo get_button($link, $class);
					}
				} ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php endif; ?>