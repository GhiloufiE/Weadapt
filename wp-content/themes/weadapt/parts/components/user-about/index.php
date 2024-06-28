<?php
/**
 * User Description template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$user_ID    = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
$is_profile = ! empty( $args['is_profile'] ) ? wp_validate_boolean( $args['is_profile'] ) : false;

$user_data  = get_userdata( $user_ID );
?>

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

		if ( ! empty( $organizations ) ) {
			?>
			<h2 class="user-about__info__title"><?php echo __( 'Organisation', 'weadapt' ); ?></h2>
			<div class="user-about__info">
				<?php foreach ( $organizations as $post_ID ) :
					$post_link   = get_permalink( $post_ID );
					$post_status = get_post_status( $post_ID );
					?>
					<div class="user-about__info__item--organisation">
						<?php if ( 'draft' === $post_status ) : ?>
							<?php if ( has_post_thumbnail( $post_ID ) ) : ?>
								<div class="organisation__logo"><?php echo get_the_post_thumbnail( $post_ID, 'medium' ); ?></div>
							<?php endif; ?>
							<div class="organisation__title"><?php echo get_the_title( $post_ID ); ?></div>
						<?php else : ?>
							<?php if ( has_post_thumbnail( $post_ID ) ) : ?>
								<div class="organisation__logo">
									<a href="<?php echo $post_link; ?>"><?php echo get_the_post_thumbnail( $post_ID, 'medium' ); ?></a>
								</div>
							<?php endif; ?>
							<div class="organisation__title">
								<a href="<?php echo $post_link; ?>"><?php echo get_the_title( $post_ID ); ?></a>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php
		}
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