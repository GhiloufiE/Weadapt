<?php

$relevant = get_field( 'relevant' );
$organisations = ! empty( $relevant['organizations'] ) ? $relevant['organizations'] : [];

if ( ! empty( $organisations ) ) :
?>

<div class="organisations">
	<?php load_inline_styles( __DIR__, 'organisations' ); ?>

	<div class="cpt-content-heading">
		<h2 class="cpt-content-heading__title"><?php _e( 'Participating Organisations', 'weadapt' ); ?></h2>
		<p class="cpt-content-heading__text"><?php _e( 'Connect with organizations working on similar issues.', 'weadapt' ); ?></p>
	</div>

	<?php get_part('components/cpt-search/index', [ 'type' => 'organisations' ]); ?>

	<div class="organisations__row row">
		<?php foreach ( $organisations as $organisation ) : ?>
			<div class="organisations__col col-12 col-lg-6">
				<?php echo get_part('components/info-widget-cpt/index', [
					'cpt_ID'  => $organisation,
					'cpt_buttons' => [ 'find-out-more' ]
				]); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="wp-block-button wp-block-button--template  cpt-more">
		<button type="button" class="wp-block-button__link cpt-more__btn">
			<?php _e('Load more', 'weadapt'); ?>
		</button>
	</div>
</div>
<?php
endif;