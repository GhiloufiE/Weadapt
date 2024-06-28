<?php
/**
 * Info Widget CPT
 *
 * @package WeAdapt
 */
$cpt_ID            = ! empty( $args['cpt_ID'] ) ? $args['cpt_ID'] : 0;
$cpt_buttons       = ! empty( $args['cpt_buttons'] ) ? $args['cpt_buttons'] : [];
$hide_empty_fields = ! empty( $args['hide_empty_fields'] ) ? $args['hide_empty_fields'] : true;

if ( ! empty( $cpt_ID ) ) :
	if ( ! empty( $cpt_ID ) ) {
		$cpt_type = get_post_type( $cpt_ID );
		$cpt_url  = get_the_permalink( $cpt_ID );
		$cpt_meta = [];
		$image    = get_the_post_thumbnail( $cpt_ID, 'medium' );
		$excerpt  = has_excerpt() ? get_the_excerpt() : '';
		$title    = get_the_title( $cpt_ID );
	}

	if ( ! empty( $members_count = get_members_count( $cpt_ID, '', $hide_empty_fields ) ) ) {
		$cpt_meta[] = ['icon-user', $members_count];
	}
?>

<div class="info-widget-cpt <?php echo get_post_type( $cpt_ID ); ?>">
	<?php load_inline_styles( __DIR__, 'info-widget-cpt' ); ?>

	<?php if ( ! empty( $image ) ) : ?>
		<div class="info-widget-cpt__image">
			<a href="<?php echo $cpt_url; ?>" class="info-widget-cpt__image__link">
				<?php echo $image; ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="info-widget-cpt__content">
		<div class="info-widget-cpt__type">
			<span><?php echo ucfirst( str_replace( '-', ' ', get_post_type( $cpt_ID ) ) ); ?></span>
		</div>

		<?php if ( ! empty( $title ) ) : ?>
			<h3 class="info-widget-cpt__title">
				<a href="<?php echo $cpt_url; ?>" class="info-widget-cpt__title__link"><?php echo $title; ?></a>
			</h3>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="info-widget-cpt__excerpt">
				<?php echo $excerpt; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $cpt_meta ) ) : ?>
			<ul class="info-widget-cpt__meta">
				<?php foreach ( $cpt_meta as $item ) : ?>
					<li class="info-widget-cpt__meta__item">
						<span class="icon" aria-label="<?php echo esc_attr( $item[0] ); ?>"><?php echo get_img( $item[0] ); ?></span>
						<span class="text"><?php echo $item[1]; ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $cpt_buttons ) ) : ?>
			<div class="info-widget-cpt__actions">
				<?php foreach ( $cpt_buttons as $button ) {
					$link = [];
					$class = '';

					switch ( $button ) {
						case 'join':
							get_part( 'components/button-join/index', [
								'style' => 'outline',
							] );

							break;

						case 'follow':
							get_part( 'components/button-join/index', [
								'style'        => 'outline',
								'title'        => __( 'Follow', 'weadapt' ),
								'unjoin_title' => __( 'Unfollow', 'weadapt' ),
							] );

							break;

						case 'share':
							get_part( 'components/button-share/index', [
								'url'  => $cpt_url,
								'type' => $cpt_type
							] );

							break;

						case 'website':
							$class = 'outline';
							$website_url = get_field( 'website_url' );

							if ( ! empty ( $website_url ) ) {
								$link = [
									'url' => $website_url,
									'title' => __( 'Website', 'weadapt' ),
									'target' => '_blank',
								];
							}

							break;

						case 'find-out-more':
							$link = [
								'url' => get_permalink( $cpt_ID ),
								'title' => __( 'Find out More', 'weadapt' ),
								'target' => '',
							];

							break;

						case 'view-theme':
							$theme_network_ID = get_field( 'relevant_main_theme_network', $cpt_ID );

							if ( ! empty( $theme_network_ID ) ) {
								$class = 'outline';
								$link = [
									'url' => get_permalink( $theme_network_ID ),
									'title' => __( 'View theme', 'weadapt' ),
									'target' => '',
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