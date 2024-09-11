<?php
/**
 * Member item
 *
 * @package WeAdapt
 */

$member_ID = ! empty( $args['member_ID'] ) ? $args['member_ID'] : 0;

if ( ! empty( $member_ID ) ) :
	$member_url = get_author_posts_url( $member_ID );
	$name       = get_user_name( $member_ID );
	$text       = get_user_excerpt( $member_ID );
?>

<div class="member-item">
	<?php load_inline_styles( __DIR__, 'member-item' ); ?>

	<div class="member-item__img">
		<a href="<?php echo $member_url; ?>" class="member-item__img__link">
			<?php echo get_avatar( $member_ID, 80 ); ?>
		</a>
	</div>

	<?php if ( ! empty( $name ) || ! empty( $text ) ) : ?>
		<div class="member-item__content">
			<?php if ( ! empty( $name ) ) : ?>
				<h3 class="member-item__name">
					<a href="<?php echo $member_url; ?>" class="member-item__name__link"><?php echo $name; ?></a>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $text ) ) : ?>
				<div class="member-item__text">
					<p><?php echo $text; ?></p>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<?php endif;