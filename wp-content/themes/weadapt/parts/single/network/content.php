<?php
/**
 * Single Network Content
 *
 * @package WeAdapt
 */

// Get the tab parameter from the URL
$activeTab = isset($_GET['tab']) ? 'tab-' . sanitize_text_field($_GET['tab']) : null;

// Default check for the 'about' tab on a specific page
$is_about_default = (get_the_ID() == 28392);

// Array to map tab IDs to their corresponding panels
$tab_panels = [
    'tab-latest'       => 'tab-latest-panel',
    'tab-about'        => 'tab-about-panel',
    'tab-editors'      => 'tab-editors-panel',
    'tab-members'      => 'tab-members-panel',
    'tab-organisations'=> 'tab-organisations-panel',
    'network-forum'    => 'tab-forum-panel',
];

// Determine the active panel based on the URL parameter or fallback to the default logic
$active_panel = $activeTab && isset($tab_panels[$activeTab]) ? $tab_panels[$activeTab] : ($is_about_default ? 'tab-about-panel' : 'tab-latest-panel');
?>

<section id="tab-latest-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-latest-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-latest-panel') echo 'hidden'; ?>>
    <?php
    $query_args = array(
        'post_status'    => 'publish',
        'post_type'      => get_allowed_post_types(['article', 'blog', 'course', 'case-study', 'event']),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => [[
            'key'     => 'relevant_main_theme_network',
            'value'   => get_the_ID(),
            'compare' => 'LIKE',
        ]],
        'ignore_sticky_posts' => true,
        'theme_query'         => true, // multisite fix
    );

    get_part('components/cpt-query/index', [
        'query_args'      => $query_args,
        'show_post_types' => true,
        'show_categories' => true
    ]);

    if (apply_filters('show_related_single_network_content', true)) {
        get_part('components/related-content/index');
    }

    if (!empty(get_allowed_post_types(['forums']))) {
        get_part('components/forum-cta/index');
    }
    ?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-about-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-about-panel') echo 'hidden'; ?>>
    <div class="archive-main__entry archive-main__entry--smaller">
        <?php
        if (empty(get_the_content())) {
            _e('There is no content.', 'weadapt');
        } else {
            the_content();
        }
        ?>
    </div>
</section>

<section id="tab-editors-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-editors-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-editors-panel') echo 'hidden'; ?>>
    <?php get_part('components/contact-cols/index'); ?>
</section>

<section id="tab-members-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-members-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-members-panel') echo 'hidden'; ?>>
    <?php
    if (!empty($followed_users = get_followed_users(get_the_ID(), get_post_type()))) :
        $query_args = [
            'include' => $followed_users,
            'fields'  => 'ID',
            'number'  => get_option('posts_per_page'),
        ];

        get_part('components/cpt-search-query/index', [
            'title'       => __('Members', 'weadapt'),
            'description' => __('Connect with peers working on similar issues.', 'weadapt'),
            'query_type'  => 'user_query',
            'query_args'  => $query_args,
        ]);
    ?>
    <?php else : ?>
        <p class="cpt-content-heading__text">
            <?php _e('There are no members', 'weadapt'); ?>
        </p>
    <?php endif; ?>
</section>

<section id="tab-organisations-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-organisations-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-organisations-panel') echo 'hidden'; ?>>
    <?php
    $organisation = get_field('relevant_organizations');

    $query_args = !empty($organisation) ? [
        'post_status'         => 'publish',
        'post_type'           => get_allowed_post_types(['organisation']),
        'post__in'            => $organisation,
        'orderby'             => 'post__in',
        'fields'              => 'ids',
        'ignore_sticky_posts' => true,
        'theme_query'         => true, // multisite fix
    ] : [];

    get_part('components/cpt-search-query/index', [
        'title'       => __('Participating Organisations', 'weadapt'),
        'description' => __('Connect with organizations working on similar issues.', 'weadapt'),
        'query_args'  => $query_args,
    ]);
    ?>
</section>

<section id="tab-forum-panel" role="tabpanel" aria-hidden="<?php echo $active_panel === 'tab-forum-panel' ? 'false' : 'true'; ?>" <?php if ($active_panel !== 'tab-forum-panel') echo 'hidden'; ?>>
    <?php
    global $wpdb;
    $wpdb->save_queries = true;
    $show_search = get_field('show_search') ? get_field('show_search') : true;
    $theme_id = get_the_ID();

    $sql = $wpdb->prepare("
        SELECT wp_posts.ID
        FROM wp_posts
        INNER JOIN {$wpdb->prefix}network_forum_relationship
        ON wp_posts.ID = {$wpdb->prefix}network_forum_relationship.forum_id
        WHERE 
        wp_posts.post_status = 'publish'
        AND {$wpdb->prefix}network_forum_relationship.network_id = %d
        ORDER BY wp_posts.post_date DESC
        LIMIT 0, 10
    ", $theme_id);

    $post_ids = $wpdb->get_col($sql);
    if ($wpdb->last_error) {
        error_log('SQL Query: ' . $sql);
    }
    error_log('Post IDs: ' . print_r($post_ids, true));
    if (empty($post_ids)) {
		echo 'No forum topics available';
	} else {
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types(['forum']),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'     => 'forum',
					'value'   => $post_ids,  
					'compare' => 'IN',       
				),
			),
			'ignore_sticky_posts' => true,
			'theme_query'         => true,  
			'categories'          => array(),
		);

		get_part('components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => false,
			'show_search'	=> $show_search,
			'show_categories' => false,
			'query_type'      => 'forum_query',
			'title'       => __('Forum Topics', 'weadapt'),
			'description' => __('Discover the forum topics in this theme.', 'weadapt'),
		]);

		$query_args = array(
			'post__in' => $post_ids,
			'post_status' => 'publish',
			'orderby' => 'post_date',
			'order' => 'DESC',
			'ignore_sticky_posts' => true,
		);
		$query = new WP_Query($query_args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				get_part('components/cpt-search-query/index', [
					'query_args'      => $query_args,
					'show_post_types' => true,
					'show_categories' => true
				]);
			}
			wp_reset_postdata();
		}
		if (apply_filters('show_related_single_theme_content', true)) {
			get_part('components/related-content/index');
		}

		if (!empty(get_allowed_post_types(['forums']))) {
			get_part('components/forum-cta/index');
		}
		get_part('components/forum-cta/index');
	}
    ?>
</section>
