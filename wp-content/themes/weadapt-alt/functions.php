<?php
add_action( 'wp_enqueue_scripts', function() {
	$theme_path = str_replace( get_home_url( 1 ), get_home_url(), get_theme_file_uri() );

	$styles = "
	@font-face {
		font-family: 'NeueHaas Display';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskDisplayPro65Medium.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskDisplayPro65Medium.woff') format('woff');
		font-weight: 700;
		font-display: swap;
	}

	@font-face {
		font-family: 'NeueHaas';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskTextPro65Medium.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskTextPro65Medium.woff') format('woff');
		font-weight: 500;
		font-display: swap;
	}

	@font-face {
		font-family: 'NeueHaas';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskTextPro55Roman.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskTextPro55Roman.woff') format('woff');
		font-weight: 400;
		font-display: swap;
	}
	";

	wp_add_inline_style( 'main', $styles );
}, 50 );


add_filter( 'register_cpt_default_args', function( $default_settings ) {
	$default_settings['has_archive'] = false;

	return $default_settings;
} );add_filter( 'wpmu_signup_user_notification_email', 'custom_wpmu_signup_user_notification_email', 10, 4 );
function custom_wpmu_signup_user_notification_email( $user_email, $message, $blogname, $blog_id ) {
    $roles = get_editable_roles();
    $role = $roles[ $_REQUEST['role'] ];

    if ( '' !== get_bloginfo( 'name' ) ) {
        $site_title = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
    } else {
        $site_title = parse_url( home_url(), PHP_URL_HOST );
    }

    $additional_message = "
        Hello everyone,
        You can disregard this email if you have no interest in joining '{$site_title}' at " . home_url() . " with the role of " . wp_specialchars_decode( translate_user_role( $role['name'] ) ) . ".
        Please note that this invitation will expire in a few days.
        Welcome to the Agora Community Hub!
        This is a weADAPT microsite which connects to the weADAPT community and its content to amplify the impact of your work.
        Here you can learn about climate adaptation issues, share your knowledge and experience, connect with citizens, researchers, practitioners and decision-makers, and create adaptation action around the world.
        We encourage you to upload a profile image that portrays climate change adaptation challenges or opportunities in your region. Please fill in the information on your profile page as much as possible, as this will make it more easily searchable and will ensure you see content that is relevant to you.
        Sharing information on the Agora Community Hub is simple - just follow these step-by-step guidelines for adding content.
        To keep up to date with the latest news and content, you can join us on [LinkedIn](https://www.linkedin.com/company/adaptation-agora/), [Twitter/X](https://twitter.com/AgoraAdaptation), or [Facebook](https://www.facebook.com/profile.php?id=100090701292038), and [join the community](https://adaptationagora.eu/join-our-community/).
        We use these outlets to share your content widely and give it the greatest visibility.
        If you require assistance or would like training on the use of the platform, please get in touch with us at [info@weadapt.org](mailto:info@weadapt.org).
        We welcome your feedback!
        Kind regards,
        The Agora Community Hub Team
    ";

    return $additional_message;
}