<?php
/**
 * Single Forum Content
 *
 * @package WeAdapt
 */

$post_ID = get_the_ID();
$user_ID = get_post_meta($post_ID, 'user_id', true);
if (!$user_ID) {
	$user_ID = $post_ID;
}
$user_data  = get_userdata( $user_ID );

?>
<?php

if ($user_ID) {
    $user_info = get_userdata($user_ID);
   
}

?>
<section id="tab-latest-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = array(
			'post_type'           => get_allowed_post_types( [ 'article', 'blog', 'course', 'event', 'case-study' ] ),
			'posts_per_page'      => 5,
			'meta_query'          => [
				'relation' => 'OR',
				[
					'key'         => 'people_contributors',
					'value'       => sprintf( ':"%d";', $user_ID ),
					'compare'     => 'LIKE',
					'compare_key' => 'LIKE'
				],
			],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		);

		get_part( 'components/cpt-query/index', [
			'query_args' => $query_args
		]);
	?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="true" hidden>
	
<div class="user-about archive-main__entry--smaller">
	<?php load_inline_styles( __DIR__, 'user-about' ); ?>

	<div class="user-about__header">
		<h2 class="user-about__title"><?php echo get_user_name( $user_ID ); ?></h2>

		<?php
			if ( $is_profile && ( $edit_profile_ID = get_page_id_by_template( 'edit-profile' ) ) ) {
				echo get_button( [
					'url'    => get_permalink( $edit_profile_ID ),
					'title'  => __('Edit information', 'weadapt'),
					'target' => '',
				], 'outline' );
			}
		?>
	</div>

	<?php if ( ! empty( $job_title = get_field( 'job_title', 'user_' . $user_ID ) ) ) : ?>
		<div class="user-about__job">
			<?php echo wp_kses_post( $job_title ); ?>
		</div>
	<?php endif; ?>

	<?php echo wpautop( get_the_author_meta( 'description', $user_ID ) ); ?>

	<p class="user-about__registered"><?php echo sprintf( __( 'Joined %s', 'weadapt' ), date( 'Y', strtotime( $user_data->user_registered ) ) ); ?></p>

	<?php
		$organizations = get_field( 'organisations', "user_$user_ID" );
		$organisations            = get_field('organisations', 'user_' . $user_ID);
		$cpt_widget_args          = [
			'title' => __( 'Organisations', 'weadapt' ),
			'cpt_IDs' => $organisations,
			'buttons' => [ 'permalink' ]
		];
		get_part('components/cpt-widget/index', $cpt_widget_args);
	?>

	<?php
		$address      = get_field( 'address', "user_$user_ID" );
		$role         = get_field( 'role', "user_$user_ID" );
		$company      = get_field( 'company', "user_$user_ID" );
		$address_data = [];

		if ( ! empty( $address['city'] ) ) {
			$address_data[] = $address['city'];
		}
		if ( ! empty( $address['county'] ) ) {
			$address_data[] = $address['county'];
		}
		if ( ! empty( $address['country']['label'] ) ) {
			$address_data[] = $address['country']['label'];
		}

		if ( ! empty( $address_data ) || ! empty( $job_title ) ) :
		?>
			<div class="user-about__info">
				<?php if ( ! empty( $address_data ) ) : ?>
					<div class="user-about__info__item">
						<h2 class="user-about__info__title"><?php echo __( 'Location', 'weadapt' ); ?></h2>
						<?php echo implode( ', ', $address_data ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $company ) ) : ?>
					<div class="user-about__info__item">
						<h2 class="user-about__info__title"><?php echo __( 'Work', 'weadapt' ); ?></h2>
						<?php echo wp_kses_post( $company ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php
		endif;
	?>

	<?php
		$social_data   = [];

		foreach ( [
			'twitter'   => __( 'Twitter', 'weadapt' ),
			'instagram' => __( 'Instagram', 'weadapt' ),
			'website'   => __( 'Website', 'weadapt' ),
			'linkedin'  => __( 'LinkedIn', 'weadapt' )
		] as $key => $title ) {
			if ( ! empty( $url = get_field( "{$key}_url", "user_$user_ID" ) ) ) {
				$social_data[$key] = [
					'url'    => esc_url( $url ),
					'target' => '_blank',
					'title'  => $title
				];
			}
		}

		if ( ! empty( $social_data ) ) {
			?><div class="user-about__social"><?php
				foreach ( $social_data as $social_key => $social_item ) {
					echo get_button( $social_item, 'outline', 'has-icon-left', "icon-social-$social_key" );
				}
			?></div><?php
		}
	?>
</div>
</section>



