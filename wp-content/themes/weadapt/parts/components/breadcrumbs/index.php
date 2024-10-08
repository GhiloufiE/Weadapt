<?php
global $wpdb;

$breadcrumbs = ! empty( $args['breadcrumbs'] ) ? $args['breadcrumbs'] : [];
$current_post_id = get_the_ID();
$post_type = get_post_type($current_post_id);

if ($post_type === 'forum') {
    $forum_id = get_field('field_653b5c7e6d5f5', $current_post_id);
    $selected_forum_true_id = null;

    if ($forum_id) {
        $theme_forum_id = $wpdb->get_var($wpdb->prepare(
            "SELECT theme_id FROM {$wpdb->prefix}theme_forum_relationship WHERE forum_id = %d", 
            $forum_id
        ));

        if ($theme_forum_id === null) {
            $network_forum_id = $wpdb->get_var($wpdb->prepare(
                "SELECT network_id FROM {$wpdb->prefix}network_forum_relationship WHERE forum_id = %d", 
                $forum_id
            ));
            $selected_forum_true_id = $network_forum_id;
        } else {
            $selected_forum_true_id = $theme_forum_id;
        }
    }

    if ($selected_forum_true_id !== null) {
        $theme_network_title = get_the_title($selected_forum_true_id);
        $theme_network_url = get_permalink($selected_forum_true_id);
        if (isset($breadcrumbs[2])) {
            $breadcrumbs[2] = array(
                'url'   => $theme_network_url,
                'label' => $theme_network_title,
            );
        }
    }
}

?>
<nav class="breadcrumbs breadcrumbs--list" aria-label="Breadcrumb">
    <?php load_inline_styles( __DIR__, 'breadcrumbs' ); ?>
    <ol class="breadcrumbs__list" role="list">
        <?php foreach ($breadcrumbs as $crumb) : ?>
            <li class="breadcrumbs__item<?php if (isset($crumb['current']) && $crumb['current']) echo 'breadcrumbs__item--current'; ?>" role="listitem"<?php if (isset($crumb['current']) && $crumb['current']) echo ' aria-current="page"'; ?>>
                <?php if (!empty($crumb['url'])) : ?>
                    <a href="<?php echo esc_url($crumb['url']); ?>" class="breadcrumbs__link"><?php echo esc_html($crumb['label']); ?></a>
                <?php else : ?>
                    <span class="breadcrumbs__label"><?php echo esc_html($crumb['label']); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>