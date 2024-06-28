<?php
/**
 * CPT CTA
 *
 * @package WeAdapt
 */

$cpt_ID  = ! empty( $args['cpt_ID'] )  ? $args['cpt_ID']  : 0;
$buttons = ! empty( $args['buttons'] ) ? $args['buttons'] : [];

if ( ! empty( $cpt_ID ) ) :
	$link_url  = get_the_permalink( $cpt_ID );
	$members = get_members_count( $cpt_ID );
?>

<div class="cpt-cta">
	<?php load_inline_styles( __DIR__, 'cpt-cta' ); ?>

	<?php if ( ! empty( $image = get_the_post_thumbnail( $cpt_ID, 'thumbnail' ) ) ) : ?>
		<div class="cpt-cta__img">
			<a href="<?php echo $link_url; ?>" class="cpt-cta__img-link"><?php echo $image; ?></a>
		</div>
	<?php endif; ?>

	<div class="cpt-cta__content">
		<?php if ( ! empty( $title = get_the_title( $cpt_ID ) ) ) : ?>
			<h3 class="cpt-cta__title">
				<a class="cpt-cta__title__link" href="<?php echo $link_url; ?>"><?php echo $title; ?>
				</a>
			</h3>
		<?php endif; ?>

		<?php if ( has_excerpt( $cpt_ID ) ) : ?>
			<div class="cpt-cta__text"><?php echo get_the_excerpt( $cpt_ID ); ?></div>
		<?php endif; ?>

		<div class="cpt-cta__members">
			<span class="icon"><?php echo get_img('icon-user'); ?></span>
			<span class="text">
				<?php echo $members; ?>
			</span>
		</div>

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
								'url' => get_the_permalink($cpt_ID),
								'title' => __( 'Read more' . get_img('icon-arrow-right-button') , 'weadapt' ),
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
