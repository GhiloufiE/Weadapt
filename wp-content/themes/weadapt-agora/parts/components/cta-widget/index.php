<?php
/**
 * CTA Widget
 *
 * @package WeAdapt
 */
$template = ! empty( $args['template'] ) ? $args['template'] : '';
$widget   = ! empty( $args['custom_widget'] ) ? $args['custom_widget'] : [];

$content     = get_field( $template, 'options' );
$title       = ! empty( $content['title'] ) ? $content['title']: '';
$description = ! empty( $content['description'] ) ? $content['description'] : '';

$block_class = 'cta-widget';

$link         = [];
$style        = 'small';
$custom_class = '';
$icon         = '';

switch ( $template ) {
	case 'join_the_conversation':
		if ( ! empty( $forum_id = get_post_forum( get_the_ID() ) ) ) {
			$link = [
				'url' => get_permalink( $forum_id ),
				'title' => __( 'Discuss', 'weadapt' ),
				'target' => '',
			];

			$icon = 'icon-arrow-right-button';
		}

		break;

	case 'contribute_now':
		$link = [
			'url'    => admin_url('post-new.php?post_type=article'),
			'title'  => __( 'Add', 'weadapt' ),
			'target' => '_blank',
		];

		$block_class .= ' cta-widget--alt';

		break;

	case 'start_a_discussion':
		$link = [
			'url'    => admin_url('post-new.php?post_type=forum&selected-forum=' . get_the_ID()),
			'title'  => __( 'Create', 'weadapt' ),
			'target' => '_blank',
		];

		break;

	case 'share_your_work':
		$link = [
			'url' => '#',
			'title' => __( 'Create', 'weadapt' ),
			'target' => '',
		];

		$icon = 'icon-arrow-right-button';

		break;

	case 'webinars':
		$link = [
			'url' => '#',
			'title' => __( 'Explore', 'weadapt' ),
			'target' => '',
		];

		$icon = 'icon-arrow-right-button';

		break;

	case 'need_help':
		$link = [
			'url' => get_home_url( null, '/faq' ),
			'title' => __( 'FAQs', 'weadapt' ),
			'target' => '',
		];

		$block_class .= ' cta-widget--big';
		$icon         = 'icon-chevron-right';

		break;

	case 'faqs':
		$link = [
			'url' => get_home_url( null, '/faq' ),
			'title' => __( 'Find out more', 'weadapt' ),
			'target' => '',
		];

		$icon = 'icon-chevron-right';

		break;

	default:
		$title       = ! empty( $widget['title'] ) ? $widget['title'] : '';
		$description = ! empty( $widget['description'] ) ? $widget['description'] : '';
		$link        = ! empty( $widget['button'] ) ? $widget['button'] : [];

		$block_class .= ' cta-widget--page';
		$icon         = 'icon-arrow-right-button';

		break;
}

if ( ! empty( $title ) || ! empty( $description ) ) :
?>

<div class="<?php echo $block_class; ?>">
	<?php load_inline_styles( __DIR__, 'cta-widget' ); ?>

	<?php if ( $title ) : ?>
		<h2 class="cta-widget__title"><?php echo wp_kses_post( $title ); ?></h2>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<div class="cta-widget__content"><?php echo wp_kses_post( $description ); ?></div>
	<?php endif; ?>

	<?php if ( ! empty( $link ) ) : ?>
		<?php echo get_button( $link, $style, $custom_class, $icon ); ?>
	<?php endif; ?>
</div>

<?php endif; ?>
