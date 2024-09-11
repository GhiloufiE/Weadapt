<?php
	$current_user = wp_get_current_user();
?>

<section class="archive-main" aria-labelledby="main-heading">
	<?php
		load_inline_styles_shared( 'custom-select' );
		load_inline_styles_shared( 'forms' );
		load_inline_styles( __DIR__, 'edit-profile' );

		load_blocks_script( 'edit-profile', 'weadapt/edit-profile', ['select'] );
	?>

	<div class="archive-main__container container">
		<div class="archive-main__row archive-main__row--edit-profile row ">
			<div class="archive-main__content">
				<div class="cpt-content-heading">
					<h2 class="cpt-content-heading__title"><?php _e( 'Edit Profile', 'weadapt' ); ?></h2>
				</div>

				<?php get_part("components/edit-profile/parts/filter-tabs"); ?>

				<form class="edit-profile" enctype="multipart/form-data">
					<?php wp_nonce_field( 'ajax-user-edit-nonce', 'ajax_user_edit_nonce' ); ?>
					<input type="hidden" name="action" value="theme_ajax_edit_profile">
					<input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">

					<section id="tab-account-settings-panel" role="tabpanel" aria-hidden="false">
						<?php get_part("components/edit-profile/parts/account-settings", [ 'user' => $current_user ]); ?>
					</section>

					<section id="tab-tell-us-more-panel" role="tabpanel"  aria-hidden="true" hidden>
						<?php get_part("components/edit-profile/parts/tell-us-more", [ 'user' => $current_user ]); ?>
					</section>

					<section id="tab-personal-details-panel" role="tabpanel" aria-hidden="true" hidden>
						<?php get_part("components/edit-profile/parts/personal-details", [ 'user' => $current_user ]); ?>
					</section>
				</form>
			</div>

			<aside class="archive-main__aside">
				<?php get_part("components/edit-profile/parts/aside"); ?>
			</aside>
		</div>
	</div>
</section>
