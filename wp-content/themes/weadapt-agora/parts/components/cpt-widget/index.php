<?php
/**
 * CPT Widget
 *
 * @package WeAdapt
 */
$title     = ! empty( $args['title'] )     ? $args['title']     : '';
$cpt_IDs   = ! empty( $args['cpt_IDs'] )   ? $args['cpt_IDs']   : [];
$buttons   = ! empty( $args['buttons'] )   ? $args['buttons']   : [];
$more_link = ! empty( $args['more_link'] ) ? $args['more_link'] : [];

if ( ! is_array( $cpt_IDs ) ) {
	$temp_cpt_ID = $cpt_IDs;
	$cpt_IDs = [$temp_cpt_ID];
}

$current_blog_ID = get_current_blog_id();
$published_cpt_IDs = array_filter($cpt_IDs, function($cpt_ID) use ($current_blog_ID) {
	$post_status = get_post_status($cpt_ID);
	$publish_to = get_field('publish_to', $cpt_ID);
	return $post_status === 'publish' && (empty($publish_to) || in_array($current_blog_ID, $publish_to));
});

if ( ! empty( $published_cpt_IDs ) ) :
?>

<div class="cpt-widget">
	<?php load_inline_styles( __DIR__, 'cpt-widget' ); ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h2 class="cpt-widget__title widget-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<div class="cpt-widget__row">
		<?php foreach ( $cpt_IDs as $cpt_ID ) {
			$post_status = get_post_status($cpt_ID);
			$cpt_title = get_the_title($cpt_ID);
			$cpt_link  = get_permalink($cpt_ID);

			// Check if the CPT is published
			if ( in_array($cpt_ID, $published_cpt_IDs) ) {
				// Published: Show with link
				echo get_part( 'components/cpt-cta/index', [
					'cpt_ID'  => $cpt_ID,
					'buttons' => $buttons
				] );
			} else {
				// Not published: Show title without the link (no <a> tag)
				$data = [
					'cpt_ID'  => $cpt_ID,
					'buttons' => $buttons
				];
				$html = get_part( 'components/cpt-cta/index', $data );
				$html_no_link = preg_replace('#<a\s+href="[^"]+"[^>]*>([^<]+)</a>#i', '$1', $html);
				echo $html_no_link;
			}
		} ?>
	</div>

	<?php if ( ! empty( $more_link ) ) : ?>
		<div class="cpt-widget__more">
			<a class="cpt-widget__more-link" href="<?php echo esc_url( $more_link[0] ); ?>"><?php echo esc_html( $more_link[1] ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php endif; ?><?php
/**
 * CPT Widget
 *
 * @package WeAdapt
 */
$title     = ! empty( $args['title'] )     ? $args['title']     : '';
$cpt_IDs   = ! empty( $args['cpt_IDs'] )   ? $args['cpt_IDs']   : [];
$buttons   = ! empty( $args['buttons'] )   ? $args['buttons']   : [];
$more_link = ! empty( $args['more_link'] ) ? $args['more_link'] : [];

if ( ! is_array( $cpt_IDs ) ) {
	$temp_cpt_ID = $cpt_IDs;
	$cpt_IDs = [$temp_cpt_ID];
}

$current_blog_ID = get_current_blog_id();
$published_cpt_IDs = array_filter($cpt_IDs, function($cpt_ID) use ($current_blog_ID) {
	$post_status = get_post_status($cpt_ID);
	$publish_to = get_field('publish_to', $cpt_ID);
	return $post_status === 'publish' && (empty($publish_to) || in_array($current_blog_ID, $publish_to));
});

if ( ! empty( $published_cpt_IDs ) ) :
?>

<div class="cpt-widget">
	<?php load_inline_styles( __DIR__, 'cpt-widget' ); ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h2 class="cpt-widget__title widget-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<div class="cpt-widget__row">
		<?php foreach ( $cpt_IDs as $cpt_ID ) {
			$post_status = get_post_status($cpt_ID);
			$cpt_title = get_the_title($cpt_ID);
			$cpt_link  = get_permalink($cpt_ID);

			// Check if the CPT is published
			if ( in_array($cpt_ID, $published_cpt_IDs) ) {
				// Published: Show with link
				echo get_part( 'components/cpt-cta/index', [
					'cpt_ID'  => $cpt_ID,
					'buttons' => $buttons
				] );
			} else {
				// Not published: Show title without the link (no <a> tag)
				$data = [
					'cpt_ID'  => $cpt_ID,
					'buttons' => $buttons
				];
				$html = get_part( 'components/cpt-cta/index', $data );
				$html_no_link = preg_replace('#<a\s+href="[^"]+"[^>]*>([^<]+)</a>#i', '$1', $html);
				echo $html_no_link;
			}
		} ?>
	</div>

	<?php if ( ! empty( $more_link ) ) : ?>
		<div class="cpt-widget__more">
			<a class="cpt-widget__more-link" href="<?php echo esc_url( $more_link[0] ); ?>"><?php echo esc_html( $more_link[1] ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php endif; ?>