<?php
function enqueue_child_theme_scripts() {
    wp_enqueue_script(
        "child-theme-transparent-bg",
        get_stylesheet_directory_uri() . "/parts/components/header/transparent-bg.js",
        [],
        '1.0',
        true
    );
    if ( get_post_type() === 'article' ) {
      wp_enqueue_script(
            "article-image-text",
            get_stylesheet_directory_uri() . "/parts/single/blog/image.js",
            [],
            '1.0',
            true
        );
    }
}
add_theme_support( 'align-wide' );
add_action('wp_enqueue_scripts', 'enqueue_child_theme_scripts');



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