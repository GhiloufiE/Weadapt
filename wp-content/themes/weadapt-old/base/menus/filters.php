<?php

/**
 * Global Variables
 */
global $menu_item_card, $menu_item_desc, $menu_item_submenu, $has_menu_item_card, $has_menu_item_desc;

$menu_item_card = $menu_item_desc = $menu_item_submenu = $has_menu_item_card = $has_menu_item_desc = false;


/**
 * Add Menu Item Card/Desc for Card_Walker_Nav_Menu
 */
add_filter( 'walker_nav_menu_start_el', function( $item_output, $menu_item, $depth, $args ) {
	if ( in_array( $args->theme_location, array( 'header-top-menu', 'header-main-menu', 'header-main-menu-logged-in' ) ) ) {
		global $menu_item_card, $menu_item_desc, $menu_item_submenu, $has_menu_item_card, $has_menu_item_desc;

		if ( 0 === $depth ) {
			$item_output        = '<span class="menu-item__wrap">' . $item_output;
			$menu_item_card     = get_field( 'card', $menu_item );
			$menu_item_desc     = get_field( 'desc', $menu_item );
			$menu_item_submenu  = get_field( 'submenu', $menu_item );
			$has_menu_item_card = false;
			$has_menu_item_desc = false;
			$display_megamenu   = true;


			// Replace Shortcode [user_login]
			preg_match_all( '/<a href="(.+?)"(.*?)>(.*?\[user_login\])<\/a>/', $item_output, $matches );

			if ( ! empty( $matches[0][0] ) ) {
				ob_start();

				if ( is_user_logged_in() ) :
					$login_url    = ! empty( $matches[1][0] ) ? $matches[1][0] : '#';
					$current_user = wp_get_current_user();

					$messages_count = function_exists( 'fep_get_new_message_number' ) ? fep_get_new_message_number() : 0;
					?>

						<span class="menu-item__avatar" data-messages="<?php echo intval( $messages_count ); ?>"><?php echo get_avatar( $current_user->ID, 26 ); ?></span>

						<a href="<?php echo esc_url( $login_url ); ?>" class="menu-item--user">
							<?php echo $current_user->display_name; ?>
						</a>
					<?php
				else :
					?>
						<button data-popup="sign-in"><?php _e( 'Sign in / Register', 'weadapt' ); ?></button>
					<?php

					add_action( 'popup-content', function() {
						foreach ( [ 'sign-in', 'forgot-password', 'create-account' ] as $template_name ) {
							echo get_part( 'components/popup/index', [ 'template' => $template_name ] );
						}
					} );

					$display_megamenu = false;
				endif;
				$login_html  = ob_get_clean();

				$item_output = preg_replace( '/<a href="(.+?)">(.*?\[user_login\])<\/a>/', $login_html, $item_output, 1 );
			}

			if (
				! empty( $menu_item_card['icon'] ) ||
				! empty( $menu_item_card['title'] ) ||
				! empty( $menu_item_card['description'] ) ||
				! empty( $menu_item_card['button'] )
			) {
				$has_menu_item_card = true;
			}

			if (
				! empty( $menu_item_desc['title'] ) ||
				! empty( $menu_item_desc['description'] )
			) {
				$has_menu_item_desc = true;
			}

			if (
				$display_megamenu && (
					$has_menu_item_card ||
					$has_menu_item_desc ||
					in_array( 'menu-item-has-children', $menu_item->classes
				)
			) ) {
				ob_start();

				?>
					<button class="menu-item__dropdown" aria-expanded="false" aria-haspopup="true" type="button">
						<span class="screen-reader-text"><?php echo sprintf( __( '%s Submenu', 'weadapt' ), $menu_item->post_title ); ?></span>
						<?php echo get_img( 'icon-chevron-down' ); ?>
					</button>

					</span>
				<?php

				// If Has Card/Desc and sub-menu is empty
				if (
					$display_megamenu &&
					( $has_menu_item_card || $has_menu_item_desc ) &&
					false === $args->walker->has_children
				) :

				?>
					<div class="mega-menu">
						<?php
							if ( ! empty( $menu_item_submenu['image'] ) ) :
								$submenu_bg_position = ! empty( $menu_item_submenu['position'] ) ? $menu_item_submenu['position'] : 'left';
						?>
							<div class="mega-menu__bg mega-menu__bg--<?php echo esc_attr( $submenu_bg_position ); ?>"><?php echo get_img( $menu_item_submenu['image'] ); ?></div>
						<?php endif; ?>

						<div class="container">
							<div class="row">
								<?php if ( $has_menu_item_card ) : ?>
									<div class="mega-menu__col mega-menu__col--card">
										<div class="mega-menu__card">
											<?php if ( ! empty( $menu_item_card['icon'] ) ) : ?>
												<?php echo get_img( $menu_item_card['icon'] ); ?>
											<?php endif; ?>

											<?php if ( ! empty( $menu_item_card['title'] ) ) : ?>
												<h4><?php echo $menu_item_card['title']; ?></h4>
											<?php endif; ?>

											<?php if ( ! empty( $menu_item_card['description'] ) ) : ?>
												<p><?php echo $menu_item_card['description']; ?></p>
											<?php endif; ?>

											<?php if ( ! empty( $menu_item_card['button'] ) ) : ?>
												<?php echo get_button( $menu_item_card['button'], 'icon-small', '', 'icon-arrow-right-button' ); ?>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>

								<div class="mega-menu__col mega-menu__col--sub-menu">
									<?php if ( $has_menu_item_desc ) : ?>
										<div class="mega-menu__desc">
											<?php if ( ! empty( $menu_item_desc['title'] ) ) : ?>
												<h4><?php echo $menu_item_desc['title']; ?></h4>
											<?php endif; ?>

											<?php
												$add_description_html = '';

												if ( ! empty( $menu_item_description = $menu_item_desc['description'] ) ) :

													// Replace Shortcode [login_buttons]
													preg_match_all( '/\[login_buttons\]/', $menu_item_description, $matches );

													if ( ! empty( $matches[0][0] ) ) {
														$menu_item_description = preg_replace( '/\[login_buttons\]/', '', $menu_item_description, 1 );

														ob_start();
														?>
															<div class="wp-block-buttons">
																<div class="wp-block-button is-style-outline">
																	<?php echo get_img( 'icon-arrow-left' ); ?>
																	<a data-popup class="wp-block-button__link" href="#forgot-password"><?php _e( 'Request new password', 'weadapt' ); ?></a>
																</div>
																<div class="wp-block-button">
																	<?php echo get_img( 'icon-arrow-left' ); ?>
																	<a data-popup class="wp-block-button__link" href="#sign-out"><?php _e( 'Sign out', 'weadapt' ); ?></a>
																</div>
															</div>
														<?php

														add_action( 'popup-content', function() {
															foreach ( [ 'forgot-password', 'sign-out' ] as $template_name ) {
																echo get_part( 'components/popup/index', [ 'template' => $template_name ] );
															}
														} );

														$add_description_html  = ob_get_clean();
													}

													// Replace Shortcode [green_settings_buttons]
													preg_match_all( '/\[green_settings_buttons\]/', $menu_item_description, $matches );

													if ( ! empty( $matches[0][0] ) ) {
														$menu_item_description = preg_replace( '/\[green_settings_buttons\]/', '', $menu_item_description, 1 );

														ob_start();

														?>
															<div class="true-false-buttons">
																<label for="dark-mode" class="true-false-button" tabindex="0">
																	<input type="checkbox" id="dark-mode" autocomplete="off"<?php echo ( isset( $_COOKIE['weadapt-dark-mode'] ) && wp_validate_boolean( $_COOKIE['weadapt-dark-mode'] ) ) ? ' checked="checked"' : ''; ?>>
																	<span class="icon"></span>
																	<?php _e( 'Dark mode', 'weadapt' ); ?>
																</label>
																<label for="low-quality-images" class="true-false-button" tabindex="0">
  																  <input type="checkbox" id="low-quality-images" autocomplete="off" <?php echo (isset($_COOKIE['weadapt-low-quality-images']) && wp_validate_boolean($_COOKIE['weadapt-low-quality-images'])) ? ' checked="checked"' : ''; ?>>
   																	<span class="icon"></span>
    																<?php _e('Low quality images', 'weadapt'); ?>
																</label>
															</div>
														<?php

														$add_description_html  = ob_get_clean();
													}


													?><p><?php echo $menu_item_description; ?></p><?php


													if ( ! empty( $add_description_html ) ) {
														echo $add_description_html;
													}
												endif;
											?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php

				endif;
				$item_output .= ob_get_clean();
			}
			else {
				$item_output .= '</span>';
			}
		}

		else {
			$item_output = get_img( 'icon-arrow-left' ) . $item_output;
		}

	}

	return $item_output;
}, 10, 4);


/**
 * Card_Walker_Nav_Menu
 */
class Card_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes = array( 'sub-menu' );

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 *
		 * @since 4.8.0
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
		 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );

		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


		// Add Menu Item Mega Menu
		$temp_output = '';

		if ( $depth === 0 ) {
			global $menu_item_card, $menu_item_desc, $menu_item_submenu, $has_menu_item_card, $has_menu_item_desc;

			ob_start();
			?>
			<div class="mega-menu">
				<?php
					if ( ! empty( $menu_item_submenu['image'] ) ) :
						$submenu_bg_position = ! empty( $menu_item_submenu['position'] ) ? $menu_item_submenu['position'] : 'left';
				?>
					<div class="mega-menu__bg mega-menu__bg--<?php echo esc_attr( $submenu_bg_position ); ?>"><?php echo get_img( $menu_item_submenu['image'] ); ?></div>
				<?php endif; ?>

				<div class="container">
					<div class="row">
						<?php if ( $has_menu_item_card ) : ?>
							<div class="mega-menu__col mega-menu__col--card">
								<div class="mega-menu__card">
									<?php if ( ! empty( $menu_item_card['icon'] ) ) : ?>
										<?php echo get_img( $menu_item_card['icon'] ); ?>
									<?php endif; ?>

									<?php if ( ! empty( $menu_item_card['title'] ) ) : ?>
										<h4><?php echo $menu_item_card['title']; ?></h4>
									<?php endif; ?>

									<?php if ( ! empty( $menu_item_card['description'] ) ) : ?>
										<p><?php echo $menu_item_card['description']; ?></p>
									<?php endif; ?>

									<?php if ( ! empty( $menu_item_card['button'] ) ) : ?>
										<?php echo get_button( $menu_item_card['button'], 'icon-small', '', 'icon-arrow-right-button' ); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="mega-menu__col mega-menu__col--sub-menu">
							<?php if ( $has_menu_item_desc ) : ?>
								<div class="mega-menu__desc">
									<?php if ( ! empty( $menu_item_desc['title'] ) ) : ?>
										<h4><?php echo $menu_item_desc['title']; ?></h4>
									<?php endif; ?>

									<?php if ( ! empty( $menu_item_desc['description'] ) ) : ?>
										<p><?php echo $menu_item_desc['description']; ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<ul class="sub-menu">
			<?php
			$temp_output = ob_get_clean();
		}

		if ( ! empty( $temp_output ) ) {
			$output .= "{$n}{$indent}{$temp_output}{$n}";
		}
		else {
			$output .= "{$n}{$indent}<ul$class_names>{$n}";
		}
	}


	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent  = str_repeat( $t, $depth );

		// Closed Custom Menu Card
		if ( $depth === 0 ) {
			$output .= "$indent</ul></div></div></div></div>{$n}";
		}
		else {
			$output .= "$indent</ul>{$n}";
		}
	}
}

/**
 * Add a data-popup attribute to a menu item with has-popup class
 */
add_filter( 'nav_menu_link_attributes', function( $atts, $item, $args ) {
	if ( in_array( 'has-popup', $item->classes ) ) {
		$atts['data-popup'] = str_replace( '#', '', $atts['href'] );
	}

	return $atts;
}, 10, 3 );
