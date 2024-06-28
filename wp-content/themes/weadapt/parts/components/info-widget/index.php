<?php
/**
 * Info Widget
 *
 * @package WeAdapt
 */
$type    = ! empty( $args['type'] ) ? $args['type']: '';
$IDs     = ! empty( $args['IDs'] ) ? $args['IDs']  : [];
$is_user = ! empty( $args['is_user'] ) ? $args['is_user'] : false;

if ( ! is_array( $IDs ) ) {
	$temp_ID = $IDs;
	$IDs = [$temp_ID];
}

$class = 'info-widget';

if ( $type === 'theme' ) {
	$class .= ' info-widget--cpt';
}

if ( ! empty( $IDs ) ) :

foreach ( $IDs as $ID ) :

?>

<div class="<?php echo $class; ?>">
	<?php load_inline_styles( __DIR__, 'info-widget' ); ?>

		<?php if ( ! empty( $image ) ) : ?>
			<div class="info-widget__img"><a href="#" class="info-widget__img__link"><?php echo $image; ?></a></div>
		<?php endif; ?>

		<?php if ( isset($fields['img']) && !empty($fields['img']) ): ?>
			<div class="info-widget__img"><?php echo $fields['img']; ?></div>
		<?php endif; ?>

		<?php
			if ( isset($fields['btn']) && !empty($fields['btn']) ){
				echo get_button( [
					'url' => '#',
					'title' => 'Theme',
					'target' => '',
				], 'outline-small', 'info-widget__btn' );
			}
			?>
			<?php if ( isset($fields['title']) && !empty($fields['title']) ): ?>
				<h2 class="info-widget__name"><?php echo $fields['title'] ?></h2>
			<?php endif; ?>

			<?php if ( isset($fields['add_title']) && !empty($fields['add_title']) ): ?>
				<span class="info-widget__title"><?php echo $fields['add_title']; ?></span>
			<?php endif; ?>

			<?php if ( isset($fields['text']) && !empty($fields['text']) ): ?>
				<p class="info-widget__text"><?php echo $fields['text']; ?></p>
			<?php endif; ?>

			<?php if ( isset($fields['meta']) && !empty($fields['meta']) ): ?>
				<ul class="info-widget__meta">
				<?php foreach ( $fields['meta'] as $meta ): ?>
					<li class="info-widget__meta-single">
						<span class="icon"><?php echo get_img($meta['icon']); ?></span>
						<span class="text"><?php echo $meta['text']; ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( isset($fields['buttons']) && !empty($fields['buttons']) ) : ?>
				<div class="info-widget__actions">
				<?php
					foreach ( $fields['buttons'] as $button ) {
						echo get_button($button[0], $button[1], $button[2], $button[3]);
					}
				?>
				</div>
			<?php endif;
		?>
	</div>

<?php
	endforeach;
	endif;
?>