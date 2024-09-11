<?php
/**
 * Contact cols
 *
 * @package WeAdapt
 */

$people_editors = get_field( 'people_editors' );
$people_contacts = get_field( 'people_contacts' );

if ( ! empty( $people_contacts ) || ! empty( $people_editors ) ) :
?>

<div class="contact-cols">
	<?php load_inline_styles( __DIR__, 'contact-cols' ); ?>

	<?php if ( ! empty( $people_editors ) ): ?>
		<div class="cpt-content-heading">
			<h2 class="cpt-content-heading__title"><?php _e( 'Editors', 'weadapt' ); ?></h2>
		</div>

		<div class="contact-cols__row row">
			<?php foreach ( $people_editors as $user_ID ) : ?>
				<div class="contact-cols__col col-12 col-lg-6">
					<?php echo get_part( 'components/info-widget-user/index', ['user_ID' => $user_ID] ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; 

	if ( ! empty( $people_contacts ) ): ?>
		<div class="cpt-content-heading">
			<h2 class="cpt-content-heading__title"><?php _e( 'Contacts from the Programme', 'weadapt' ); ?></h2>
		</div>

		<div class="contact-cols__row row">
			<?php foreach ( $people_contacts as $user_ID ) : ?>
				<div class="contact-cols__col col-12 col-lg-6">
					<?php echo get_part( 'components/info-widget-user/index', ['user_ID' => $user_ID] ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<?php else: ?>

<p class="cpt-content-heading__text">
	<?php _e( 'No contacts selected', 'weadapt' ); ?>
</p>

<?php endif;