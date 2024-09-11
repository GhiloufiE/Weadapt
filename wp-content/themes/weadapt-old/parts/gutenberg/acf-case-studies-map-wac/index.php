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
?>

<section <?php echo $attr; ?>>
	<?php
		load_inline_styles_shared( 'custom-select' );
		load_inline_styles( __DIR__, $name );

		load_blocks_script( 'button-join', 'weadapt/button-join' );
		load_blocks_script( 'single-references', 'weadapt/single-references' );
	?>

    <form class="map__controls">
        <div class="map__search">
            <input type="search" name="search" placeholder="<?php _e( 'Search case studies', 'weadapt' ); ?>">
            <button type="submit" aria-label="<?php esc_attr_e( 'Search', 'weadapt' ); ?>"><?php echo get_img( 'icon-search' ); ?></button>
        </div>

        <?php
            $themes_networks = new WP_Query( [
                'post_status'         => 'publish',
                'post_type'           => get_allowed_post_types( [ 'theme', 'network' ] ),
                'fields'              => 'ids',
				'orderby'             => 'title',
				'order'               => 'ASC',
                'ignore_sticky_posts' => true,
                'theme_query'         => true, // multisite fix
            ] );

            if ( ! empty( $themes_networks->posts ) ) :
        ?>
        <div class="map__select">
            <select name="theme_network">
                <option value="all" selected><?php _e( 'Any Theme or Network...', 'weadapt' ); ?></option>
                <?php foreach( $themes_networks->posts as $post_ID ) : ?>
                    <option value="<?php echo intval( $post_ID ); ?>"><?php echo get_the_title( $post_ID ); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="theme-select"></div>
        </div>
        <?php endif; ?>

        <button class="view-cip-data button" data-show-text="<?php _e( 'View climate stations', 'weadapt' ); ?>" data-hide-text="<?php _e( 'Hide climate stations', 'weadapt' ); ?>">
            <span><?php _e( 'View climate stations', 'weadapt' ); ?></span>
            <span><?php echo get_img('icon-arrow-right-button') ?></span>
        </button>

         <button class="button show-list">
            <span><?php _e( 'View as a List', 'weadapt' ); ?></span>
            <span><?php echo get_img('icon-arrow-right-button') ?></span>
         </button>
        <button type="reset" class="button is-style-outline"><?php _e( 'Reset all filters', 'weadapt' ); echo get_img('icon-arrow-right-button'); ?></button>

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

					set_transient( 'map_locations', $locations, DAY_IN_SECONDS );
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
			<div class="map__overlay">
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

   <div class="map__studies-list " >
        <?php get_part("components/cpt-query-case-studies/index", [
                'query_args' => array(
                    'post_type'       => get_allowed_post_types( [ 'case-study' ] ),
                    'posts_per_page'  => 9,
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
                ),
                'show_filters' => false,
                'show_search'  => false,
                'show_loadmore' => true
            ]);
        ?>
   </div>

   <div class="container">
        <div class="map__header container">
           <?php
               echo $block_object->subtitle('subtitle');
               echo $block_object->title('title', 'h2');
               echo $block_object->desc('description');
           ?>
        </div>
   </div>

</section>
