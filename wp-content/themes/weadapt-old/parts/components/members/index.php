<?php
/**
 * Members
 *
 * @package WeAdapt
 */

$members_IDs = ! empty( $args['IDs'] ) ? $args['IDs'] : [];
$title       = ! empty( $args['title'] ) ? $args['title'] : __( 'Members', 'weadapt' );
$description = ! empty( $args['description'] ) ? $args['description'] : __( 'Connect with peers working on similar issues.', 'weadapt' );
$has_search  = ! empty( $args['has_search'] );

if ( ! empty( $members_IDs ) ) :
?>

<div class="members">
	<?php load_inline_styles( __DIR__, 'members' ); ?>

	<div class="cpt-content-heading">
		<h2 class="cpt-content-heading__title"><?php echo $title; ?></h2>
		<p class="cpt-content-heading__text"><?php echo $description; ?></p>
	</div>

	<?php if ( $has_search ) :
		get_part('components/cpt-search/index', [ 'type' => 'members' ]);
	endif; ?>

	<div class="members__row">
		<?php
			foreach ( $members_IDs as $member_ID ) {
				get_part( 'components/member-item/index', ['member_ID' => $member_ID] );
			}
		?>
	</div>

	<div class="wp-block-button wp-block-button--template  cpt-more">
		<button type="button" class="wp-block-button__link cpt-more__btn">
			<?php _e('Load more', 'weadapt'); ?>
		</button>
	</div>
</div>

<?php endif;