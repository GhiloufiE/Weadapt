<?php
/**
 * CPT Widget
 *
 * @package WeAdapt
 */

$title        = ! empty( $args['title'] ) ? $args['title'] : '';
$search_query = ! empty( $args['search_query'] ) ? $args['search_query'] : '';
$members_IDs  = ! empty( $args['members_IDs'] ) ? $args['members_IDs'] : [];
$more_link    = ! empty( $args['more_link'] ) ? $args['more_link'] : [];

if (
	is_search() ||
	( ! is_search() && ! empty( $members_IDs ) )
) : ?>

<div class="members-widget">
	<?php load_inline_styles( __DIR__, 'members' ); ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h2 class="members-widget__title widget-title">
			<?php echo wp_kses_post( $title ); ?>

			<?php if ( ! empty( $search_query ) ) : ?>
				<b>“<?php echo $search_query; ?>”</b>
			<?php endif; ?>
		</h2>
	<?php endif; ?>

	<div class="members-widget__row">
		<?php if ( ! empty( $members_IDs ) ) :
			foreach ( $members_IDs as $members_ID ) {
				echo get_part( 'components/member-item/index', [
					'member_ID'   => $members_ID
				] );
			} ?>
		<?php else: ?>
			<p><?php _e( 'Nothing found.', 'weadapt' ); ?></p>
		<?php endif;?>

	</div>

	<?php if ( ! empty( $more_link ) ) : ?>
		<div class="members-widget__more">
			<a class="members-widget__more-link" href="<?php echo esc_url( get_permalink( $more_link[0] ) ); ?>"><?php echo esc_html( $more_link[1] ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php endif;