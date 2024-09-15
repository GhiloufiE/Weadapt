<?php

/**
 * Load Main Scripts/Styles & localize vars to JS
 */
if ( ! function_exists( 'enqueue_theme_scripts' ) ) :

	function enqueue_theme_scripts() {
		global $custom_settings;

		$deps = [];

		if ( isset( $custom_settings->jquery ) && $custom_settings->jquery == true ) {
			$deps[] = 'jquery';
		}


		// // Scripts
		// wp_enqueue_script(
		// 	'script-load',
		// 	get_theme_file_uri( '/assets/js/bundle-load.min.js' ),
		// 	$deps,
		// 	filemtime( get_theme_file_path( '/assets/js/bundle-load.min.js' ) ),
		// 	false
		// );
		// wp_enqueue_script(
		// 	'script-global',
		// 	get_theme_file_uri( '/assets/js/bundle-global.min.js' ),
		// 	$deps,
		// 	filemtime( get_theme_file_path( '/assets/js/bundle-global.min.js' ) ),
		// 	true
		// );
		// wp_enqueue_script(
		// 	'script',
		// 	get_theme_file_uri( '/assets/js/bundle.min.js' ),
		// 	$deps,
		// 	filemtime( get_theme_file_path( '/assets/js/bundle.min.js' ) ),
		// 	true
		// );

		wp_localize_script( 'script', 'vars', [
			'templateUrl'                    => get_theme_file_uri(),
			
			'ajaxUrl'                        => admin_url( 'admin-ajax.php' ),
			'restSearchUrl'                  => get_rest_url( null, 'weadapt/v1/search' ),
			'restLoadPostsUrl'               => get_rest_url( null, 'weadapt/v1/load-posts' ),
			'restCaseStudiesUrl'             => get_rest_url( null, 'weadapt/v1/load-case-studies' ),
			'restLoadPostsUrlAwb'            => get_rest_url( null, 'weadapt/v1/load-posts-awb' ),
			'restLoadPostsUrlAlt'            => get_rest_url( null, 'weadapt/v1/load-query-posts-alt' ),
			'restLoadQueryPostsUrl'          => get_rest_url( null, 'weadapt/v1/load-query-posts' ),
			'restLoadSearchPostsUrl'         => get_rest_url( null, 'weadapt/v1/load-search-posts' ),
			'restLoadPostUserUrl'            => get_rest_url( null, 'weadapt/v1/load-post-user' ),
			'restLoadContributorUrl'         => get_rest_url( null, 'weadapt/v1/load-contributors-alt' ),
			'restLoadOrganisationUrl'        => get_rest_url( null, 'weadapt/v1/load-organisations-alt' ),
			'restLoadResourceUrl'            => get_rest_url( null, 'weadapt/v1/load-resources-alt' ),
			'restLoadPostContentUrl'         => get_rest_url( null, 'weadapt/v1/load-post-content' ),
			'restSearchCaseStudyMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-case-study-markers' ),
			'restSearchCountryMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-country-markers' ),
			'restSearchThemeMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-theme-markers' ),
			'restSearchAllMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-all-markers' ),
			'restSearchOrganisationMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-organisation-markers' ),
			'restSearchMembersMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-members-markers' ),
			'restSearchSolutionMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-solution-markers' ),
			'restSearchStakeholdersMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-stakeholders-markers' ),
			'restSearchCaseStudySolutionMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-case-study-solution-markers' ),
			'restSearchMarkerstUrl' => get_rest_url( null, 'weadapt/v1/search-markers' ),

			'ajaxJoinNonce'                  => wp_create_nonce( 'join' ),
		] );

		// Styles
		wp_enqueue_style(
			'main',
			get_theme_file_uri( '/assets/css/style.css' ),
			[],
			filemtime( get_theme_file_path( '/assets/css/style.css' ) )
		);

		// Fonts
		$enqueue_google_fonts_url = apply_filters( 'enqueue_google_fonts_url', 'https://fonts.googleapis.com/css2?family=Albert+Sans:wght@700&family=Inter:wght@400;500;600&display=swap' );

		if ( ! empty( $enqueue_google_fonts_url ) ) {
			wp_enqueue_style(
				'google-fonts',
				$enqueue_google_fonts_url,
				[],
				null
			);
		}

		// Youtube API
		wp_register_script( 'youtube-player-api', 'https://www.youtube.com/iframe_api', [], false, true );

		// Vimeo API
		wp_register_script( 'vimeo-player-api', 'https://player.vimeo.com/api/player.js', [], false, true );
	}

endif;

add_action( 'wp_enqueue_scripts', 'enqueue_theme_scripts' );


/**
 * Load Admin Styles
 */
add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style(
		'style-admin',
		get_theme_file_uri( '/assets/css/style-admin.css' ),
		[],
		filemtime( get_theme_file_path( '/assets/css/style-admin.css' ) )
	);
} );


/**
 * Enqueue Third Party Blocks Assets
 */
add_action( 'enqueue_block_assets', function() {
	$local_scripts = [
		'swiper' => '/assets/js/plugins/swiper-navigation-pagination.min.js',
		'select' => '/assets/js/plugins/select-pure.min.js'
	];

	if ( ! empty( $local_scripts ) ) {
		foreach ( $local_scripts as $key => $value ) {
			wp_register_script(
				$key,
				get_theme_file_uri( $value ),
				[],
				filemtime( get_theme_file_path( $value ) ),
				true
			);
		}
	}

	$external_scripts = [
		'google-translate' => 'https://translate.google.com/translate_a/element.js',
	];

	if ( ! empty( $external_scripts ) ) {
		foreach ( $external_scripts as $key => $url ) {
			wp_register_script(
				$key,
				$url,
				[],
				null,
				false
			);
		}
	}
} );
