<?php
/**
 * Block Case Studies Map
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'map' );
$name = $block_object->name();

$marker_data = [
	'currentUrl' => get_home_url( null, '/placemarks/maps/' ),
];
if ( ! empty( get_query_var('case-study') ) ) {
	$marker_data['currentMarker'] = get_the_ID();
}
if ( ! empty( get_query_var('cip_station_id') ) ) {
	$marker_data['currentCipMarker'] = get_query_var('cip_station_id');
}

wp_localize_script( "weadapt/$name", 'markerData', $marker_data);

$map_colors = apply_filters( 'google_maps_vars', [] );

// $_GET variables
$search_value = ! empty( $_GET['search'] ) ? $_GET['search'] : '';
$filter_value = ! empty( $_GET['filter'] ) ? intval( $_GET['filter'] ) : 'all';

?>

<section <?php echo $attr; ?>>
    <?php
		load_inline_styles_shared( 'custom-select' );
		load_inline_styles( __DIR__, $name );

		load_blocks_script( 'button-join', 'weadapt/button-join' );
		load_blocks_script( 'single-references', 'weadapt/single-references' );
	?>
    <div class="map__header container">
        <?php
			echo $block_object->subtitle('subtitle');
			echo $block_object->title('title', 'h2');
			echo $block_object->desc('description');
		?>
    </div>
    <form class="map__controls">
        <div class="map__search">
            <input type="search" name="search" placeholder="<?php _e( 'Search', 'weadapt' ); ?>"
                value="<?php echo esc_attr( $search_value ); ?>">
            <button type="submit"
                aria-label="<?php esc_attr_e( 'Search', 'weadapt' ); ?>"><?php echo get_img( 'icon-search' ); ?></button>
        </div>

        <?php
				$themes_networks = new WP_Query( [
					'post_status'         => 'publish',
					'post_type'           => get_allowed_post_types( [ 'theme', 'network' ] ),
					'fields'              => 'ids',
					'orderby'             => 'title',
					'order'               => 'ASC',
					'posts_per_page'      => -1,
					'ignore_sticky_posts' => true,
					'theme_query'         => true, // multisite fix
				] );

				if ( ! empty( $themes_networks->posts ) ) :
			?>
        <div class="map__select">
				<select name="theme_network">
					<option disabled value="all" selected><?php _e( 'Any Theme or Network...', 'weadapt' ); ?></option>
					<?php foreach( $themes_networks->posts as $post_ID ) : ?>
						<option value="<?php echo intval( $post_ID ); ?>" <?php selected( $filter_value, $post_ID ); ?>><?php echo get_the_title( $post_ID ); ?></option>
					<?php endforeach; ?>
				</select>
				<div class="theme-select"></div>
			</div>
        <?php endif; ?>
        <!-- select content -->
        <div class="map__select">
            <select name="select_content">
					<option disabled value="all"  selected>Select content</option>
					<option value="organisation">Organization</option>
					<option value="case_study" >Projects</option>
					<option value="members" >Citizens</option>
					<option value="solution" >Solutions</option>
            </select>
            <div class="theme-select"></div>
        </div>
        <div class="map__select">
            <select name="select_country">
                <option disabled value="all" selected>Select Country</option>
                <option value="Germany">Germany</option>
                <option value="France">France</option>
                <option value="Spain">Spain</option>
                <option value="Sweden">Sweden</option>
            </select>
            <div class="theme-select"></div>
        </div>


        <?php /*
			<button class="view-cip-data button" data-show-text="<?php _e( 'View climate stations', 'weadapt' ); ?>"
        data-hide-text="<?php _e( 'Hide climate stations', 'weadapt' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 49.3 64.001">',
            <path d="M24.6 63C18.1 56.7 1 39.4 1 24.6a23.6 23.6 0 1 1 47.3 0c0 14.6-17 32.1-23.7 38.4Z"
                fill="<?php echo $map_colors['markerCipBgColor']; ?>"
                stroke="<?php echo $map_colors['markerCipColor']; ?>" stroke-linejoi="round" stroke-width="1" />
            <path
                d="M10.9,25.9h3V28h-3c-0.6,0-1.1-0.5-1.1-1.1C9.8,26.4,10.3,25.9,10.9,25.9z M35.5,25.9h3c0.6,0,1,0.5,1,1V27c0,0.6-0.4,1-1,1h-3v-2V25.9z M14.1,16.4h0.1c0.4-0.4,1-0.4,1.4,0l2.3,2.2l-1.5,1.5l-2.3-2.3C13.7,17.4,13.7,16.8,14.1,16.4z M24.7,12.2c0.6,0,1,0.4,1,1v3h-2v-3C23.7,12.6,24.1,12.2,24.7,12.2z M31.6,18.6l2.3-2.2c0.4-0.4,1.1-0.4,1.5,0s0.4,1.1,0,1.5L33.1,20l-1.5-1.5V18.6z M24.6,20.1c3.8,0,6.9,3.1,6.9,6.9S28.4,34,24.6,34s-6.9-3.1-6.9-6.9S20.8,20.1,24.6,20.1 M24.6,17.6c-5.2,0-9.4,4.2-9.4,9.4s4.2,9.4,9.4,9.4s9.4-4.2,9.4-9.4S29.8,17.6,24.6,17.6L24.6,17.6z M35.4,37.6h-0.1c-0.4,0.4-1,0.4-1.4,0l-2.3-2.2l1.5-1.5l2.3,2.3C35.8,36.6,35.8,37.2,35.4,37.6z M24.8,41.8c-0.6,0-1-0.4-1-1v-3h2v3C25.8,41.4,25.4,41.8,24.8,41.8z M17.9,35.4l-2.3,2.2c-0.4,0.4-1.1,0.4-1.5,0c-0.4-0.4-0.4-1.1,0-1.5l2.3-2.1l1.5,1.5V35.4z"
                fill="<?php echo $map_colors['markerCipColor']; ?>" />
        </svg>
        <span><?php _e( 'View climate stations', 'weadapt' ); ?></span>
        </button>
        */ ?>
        <button type="reset" class="button"><?php _e( 'Reset all filters', 'weadapt' ); ?></button>

        <div class="map__loader"><?php echo get_img( 'loader' ); ?></div>
    </form>
    <div class="map__area">
        <div class="map__wrap">
            <div class="acf-map">
                <?php

							
				// Case Studies
				$locations = get_transient( 'map_locations' );

				if ( false === $locations ) {
					$locations = [];

					$case_studies = new WP_Query([
						'post_type'       => get_allowed_post_types( [ 'case-study' ] ),
						'posts_per_page'  => -1,
						'fields'          => 'ids',
						'no_found_rows'   => true,
						'meta_query'      => [
							'relation' => 'AND',
							[
								'key'     => 'location',
								'value'   => ':"lat";',
								'compare' => 'LIKE'
							],
							[
								'key'     => 'location',
								'value'   => ':"lng";',
								'compare' => 'LIKE'
							]
						],
						'theme_query'     => true, // multisite fix
					]);

					if ( ! empty( $case_studies->posts ) ) {
						foreach( $case_studies->posts as $post_ID ) {
							$location = get_field( 'location', $post_ID );


							if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
								$locations[$post_ID] = [
									'lat'   => $location['lat'],
									'lng'   => $location['lng'],
									'title' => get_the_title( $post_ID ),
									'slug'  => get_post_field( 'post_name', $post_ID )
								];
							}
						}
					}

					set_transient( 'map_locations', $locations, 60 );
				}

				if ( ! empty( $locations ) ) {
					foreach ( $locations as $post_ID => $location ) {
						echo sprintf( '<div class="marker" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s" data-slug="%s"></div>',
							esc_attr( $post_ID ),
							esc_attr( $location['title'] ),
							esc_attr( $location['lat'] ),
							esc_attr( $location['lng'] ),
							esc_attr( $location['slug'] ),
						);
					}
				}
 

				// Organisation
				$locations_org = get_transient( 'map_locations_org' );

				if ( false === $locations_org ) {
					$locations_org = [];

					$organisation = new WP_Query([
						'post_type'       => get_allowed_post_types( [ 'organisation' ] ),
						'posts_per_page'  => -1,
						'fields'          => 'ids',
						'no_found_rows'   => true,
						'meta_query'      => [
							'relation' => 'AND',
							[
								'key'     => 'address_location_org',
								'value'   => ':"lat";',
								'compare' => 'LIKE'
							],
							[
								'key'     => 'address_location_org',
								'value'   => ':"lng";',
								'compare' => 'LIKE'
							]
						],
						'theme_query'     => true, // multisite fix
					]);
					

					if ( ! empty( $organisation->posts ) ) {
						foreach( $organisation->posts as $post_ID ) {
							$location = get_field( 'address_location_org', $post_ID );


							if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
								$locations_org[$post_ID] = [
									'lat'   => $location['lat'],
									'lng'   => $location['lng'],
									'title' => get_the_title( $post_ID ),
									'slug'  => get_post_field( 'post_name', $post_ID )
								];
							}
						}
					}
					

					set_transient( 'map_locations_org', $locations_org, 60 );
				}

				if ( ! empty( $locations_org ) ) {
					foreach ( $locations_org as $post_ID => $location ) {
						echo sprintf( '<div class="marker-org" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s" data-slug="%s"></div>',
							esc_attr( $post_ID ),
							esc_attr( $location['title'] ),
							esc_attr( $location['lat'] ),
							esc_attr( $location['lng'] ),
							esc_attr( $location['slug'] ),
						);
					}
				}

                // stakeholders
				$location_stakeholders = get_transient( 'map_location_stakeholders' );

				if ( false === $location_stakeholders ) {
					$location_stakeholders = [];

					$stakeholders = new WP_Query([
						'post_type'       => get_allowed_post_types( [ 'stakeholders' ] ),
						'posts_per_page'  => -1,
						'fields'          => 'ids',
						'no_found_rows'   => true,
						'meta_query'      => [
							'relation' => 'AND',
							[
								'key'     => 'location_stakeholders',
								'value'   => ':"lat";',
								'compare' => 'LIKE'
							],
							[
								'key'     => 'location_stakeholders',
								'value'   => ':"lng";',
								'compare' => 'LIKE'
							]
						],
						'theme_query'     => true, // multisite fix
					]);
					

					if ( ! empty( $stakeholders->posts ) ) {
						foreach( $stakeholders->posts as $post_ID ) {
							$location = get_field( 'location_stakeholders', $post_ID );


							if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
								$location_stakeholders[$post_ID] = [
									'lat'   => $location['lat'],
									'lng'   => $location['lng'],
									'title' => get_the_title( $post_ID ),
									'slug'  => get_post_field( 'post_name', $post_ID )
								];
							}
						}
					}
					

					set_transient( 'map_location_stakeholders', $stakeholders, 60 );
				}

				if ( ! empty( $location_stakeholders ) ) {
					foreach ( $location_stakeholders as $post_ID => $location ) {
						echo sprintf( '<div class="marker-stake" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s" data-slug="%s"></div>',
							esc_attr( $post_ID ),
							esc_attr( $location['title'] ),
							esc_attr( $location['lat'] ),
							esc_attr( $location['lng'] ),
							esc_attr( $location['slug'] ),
						);
					}
				}


                // members
// members
$location_members = get_transient( 'map_location_users' );

if ( false === $location_members ) {
	$location_members = [];

	// Use WP_User_Query instead of WP_Query
	$members = new WP_User_Query([
		'number'         => -1, // Equivalent to 'posts_per_page' => -1
		'fields'         => 'ID', // Return only the user IDs
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'     => 'location_users',
				'value'   => ':"lat";',
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_users',
				'value'   => ':"lng";',
				'compare' => 'LIKE'
			]
		]
	]);

	// Check if the query returned any users
	if ( ! empty( $members->get_results() ) ) {
		foreach( $members->get_results() as $user_id ) {
			// Fetch the 'location_users' user meta
			$location = get_user_meta( $user_id, 'location_users', true );

			if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
				$location_members[$user_id] = [
					'lat'   => $location['lat'],
					'lng'   => $location['lng'],
					'title' => get_user_by( 'ID', $user_id )->display_name, // Use display name as the title
					'slug'  => get_user_by( 'ID', $user_id )->user_nicename // Use the user_nicename as a slug
				];
			}
		}
	}

	// Cache the results for 60 seconds
	set_transient( 'map_location_users', $location_members, 60 );
}

if ( ! empty( $location_members ) ) {
	foreach ( $location_members as $user_id => $location ) {
		echo sprintf( '<div class="marker-members" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s" data-slug="%s"></div>',
			esc_attr( $user_id ),
			esc_attr( $location['title'] ),
			esc_attr( $location['lat'] ),
			esc_attr( $location['lng'] ),
			esc_attr( $location['slug'] )
		);
	}
}

				// solution
				$location_solution = get_transient( 'map_location_solution' );

				if ( false === $location_solution ) {
					$location_solution = [];

					$solution = new WP_Query([
						'post_type'       => get_allowed_post_types( [ 'solutions-portal' ] ),
						'posts_per_page'  => -1,
						'fields'          => 'ids',
						'no_found_rows'   => true,
						'meta_query'      => [
							'relation' => 'AND',
							[
								'key'     => 'location_solution',
								'value'   => ':"lat";',
								'compare' => 'LIKE'
							],
							[
								'key'     => 'location_solution',
								'value'   => ':"lng";',
								'compare' => 'LIKE'
							]
						],
						'theme_query'     => true, // multisite fix
					]);
					

					if ( ! empty( $solution->posts ) ) {
						foreach( $solution->posts as $post_ID ) {
							$location = get_field( 'location_solution', $post_ID );


							if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
								$location_solution[$post_ID] = [
									'lat'   => $location['lat'],
									'lng'   => $location['lng'],
									'title' => get_the_title( $post_ID ),
									'slug'  => get_post_field( 'post_name', $post_ID )
								];
							}
						}
					}
					

					set_transient( 'map_location_solution', $location_solution, 60 );
				}

				if ( ! empty( $location_solution ) ) {
					foreach ( $location_solution as $post_ID => $location ) {
						echo sprintf( '<div class="marker-solution" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s" data-slug="%s"></div>',
							esc_attr( $post_ID ),
							esc_attr( $location['title'] ),
							esc_attr( $location['lat'] ),
							esc_attr( $location['lng'] ),
							esc_attr( $location['slug'] ),
						);
					}
				}



				// Cip stations
				$cip_locations = get_transient( 'map_cip_locations' );

				if ( false === $cip_locations ) {
					$cip_locations = [];

					foreach ( [136, 217] as $dataset ) {
						$response = wp_remote_get( 'https://cip.csag.uct.ac.za/geoserver/wfs?service=WFS&version=1.0.0&request=GetFeature&maxFeatures=5000&outputFormat=json&typeName=CIP:extents-dataset' . $dataset );

						if ( ! is_wp_error( $response ) ) {
							$body = json_decode( wp_remote_retrieve_body( $response ), true );

							if ( ! empty( $body['features'] ) ) {
								foreach ( $body['features'] as $feature ) {
									if (
										! empty( $feature['geometry']['coordinates'][0] ) &&
										! empty( $feature['geometry']['coordinates'][1] ) &&
										! empty( $feature['properties']['id'] ) &&
										! empty( $feature['properties']['description'] )
									) {
										$cip_locations[$feature['properties']['id']] = [
											'lat'   => $feature['geometry']['coordinates'][1],
											'lng'   => $feature['geometry']['coordinates'][0],
											'title' => $feature['properties']['description'],
										];
									}
								}
							}
						}
					}

					set_transient( 'map_cip_locations', $cip_locations, DAY_IN_SECONDS );
				}

				if ( ! empty( $cip_locations ) ) {
					foreach ( $cip_locations as $feature_ID => $location ) {
						echo sprintf( '<div class="marker-cip" data-id="%s" data-title="%s" data-lat="%s" data-lng="%s"></div>',
							esc_attr( $feature_ID ),
							esc_attr( $location['title'] ),
							esc_attr( $location['lat'] ),
							esc_attr( $location['lng'] ),
						);
					}
				}
			?>
            </div>
            <div class="map__overlay close">
                <div class="map__info">
                    <div class="close"><?php echo get_img( 'icon-close' ); ?></div>
                    <div class="map__info__bg"></div>
                    <div class="map__info__bg map__info__bg--cip"></div>
                    <div class="map__info__content">
                        <h3 class="map__info__title"></h3>
                        <div class="map__info__position__wrap">
                            <div class="map__info__position">
                                <div class="map__info__position__title"><?php _e( 'Latitude', 'weadapt' ); ?></div>
                                <div class="map__info__position__value map__info__position__value--lat"></div>
                            </div>
                            <div class="map__info__position">
                                <div class="map__info__position__title"><?php _e( 'Longitude', 'weadapt' ); ?></div>
                                <div class="map__info__position__value map__info__position__value--lng"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="map__content__wrap">
            <?php
				load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading');
				load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph');
				load_inline_styles_shared( 'archive' );
				load_inline_styles_shared( 'cpt-list-item' );
			?>
            <div class="map__content"></div>
            <?php echo get_img( 'loader' ); ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('.map__info .close');

    closeButtons.forEach(closeButton => {
        closeButton.addEventListener('click', function() {
            const mapBlockNode = closeButton.closest('.case-studies-map');
            mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');
            const initialUrl = markerData.currentUrl;
            window.history.pushState({}, '', initialUrl);
        });
    });

    const overlay = document.querySelector('.map__overlay');
    if (overlay) {
        overlay.addEventListener('click', function(event) {
            const mapBlockNode = overlay.closest('.case-studies-map');
            if (event.target === overlay) {
                mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');
                const initialUrl = markerData.currentUrl;
                window.history.pushState({}, '', initialUrl);
            }
        });
    }
});
</script>