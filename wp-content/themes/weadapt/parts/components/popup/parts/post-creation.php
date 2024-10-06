<div class="popup__header" id="new-post">
    <button class="back" data-popup="post-creation" aria-label="<?php _e('Back', 'weadapt'); ?>"><?php echo get_img('icon-arrow-left'); ?></button>
    <button class="close" data-popup="post-creation" aria-label="<?php _e('Close', 'weadapt'); ?>"><?php echo get_img('icon-close'); ?></button>
    <h3 class="popup__header__title" id="post-creation"><?php _e('Start a conversation', 'weadapt'); ?></h3>
</div>
<div class="popup__content sidebar-popup" aria-labelledby="post-creation" id="post-creation-popup">
    <?php if (is_user_logged_in()) : ?>
        <form id="post-creation-form" method="post">
            <?php wp_nonce_field('create_post_nonce'); ?>
            <div class="form-group">
                <label for="post-title"><?php _e('Title', 'weadapt'); ?></label>
                <input type="text" id="post-title" name="post_title" required>
            </div>
            <div class="form-group">
                <label for="post-description"><?php _e('Description', 'weadapt'); ?></label>
                <textarea id="post-description" name="post_description" rows="4" required></textarea>
            </div>

            <div class="theme-network-fields theme-select-wrap" style="display:none;">
                <label for="forum-search"><?php _e('Forum', 'weadapt'); ?></label>
                <input type="text" id="forum-search" placeholder="<?php _e('Search forum...', 'weadapt'); ?>" onkeyup="filterForums()">
                <div id="forum-list" class="forum-list">
                    <?php
                    $forums = get_posts(array('post_type' => 'forum', 'numberposts' => -1));
                    foreach ($forums as $forum) :
                    ?>
                        <div class="forum-item" data-id="<?php echo esc_attr($forum->ID); ?>" data-title="<?php echo esc_attr($forum->post_title); ?>" onclick="selectForum(<?php echo esc_attr($forum->ID); ?>)">
                            <?php echo esc_html($forum->post_title); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="selected_forum" id="selected-forum" value="">
            </div>

            <input type="hidden" name="forum" id="forum-field" value="">
            <input type="hidden" name="page_source" id="page-source-field" value="">
            <input type="hidden" name="post_type" id="post-type-field" value="">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="hidden" name="action" value="create_post">
            <button type="submit" class="wp-block-button__link" style="margin-top: 20px"><?php _e('Create Post', 'weadapt'); ?></button>
        </form>
        <div id="success-message" style="display:none;">
            <p><?php _e('Thank you for your contribution. Your post has been submitted for review and you will receive an email when it\'s approved.', 'weadapt'); ?></p>
        </div>
    <?php else : ?>
        <p><?php _e('You must be logged in to start a conversation.', 'weadapt'); ?></p>
        <button class="wp-block-button__link" data-popup="sign-in"><?php _e('Log in or Create an Account', 'weadapt'); ?></button>
    <?php endif; ?>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function() {
    const forumSearchWrap = document.querySelector('.theme-network-fields');
    const isConnectPage = window.location.href.includes('connect'); 
    if (isConnectPage) {
        forumSearchWrap.classList.add('show-theme-network-fields');
        forumSearchWrap.style.display = 'block';
    } else {
        forumSearchWrap.classList.remove('show-theme-network-fields');
        forumSearchWrap.style.display = 'none';
    }
});

const forumSearch = document.getElementById('forum-search');
const forumList = document.getElementById('forum-list');
const forumItems = forumList.getElementsByClassName('forum-item');
const selectedForumField = document.getElementById('selected-forum');

forumSearch.addEventListener('keyup', function() {
    const filter = forumSearch.value.toUpperCase();
    for (let i = 0; i < forumItems.length; i++) {
        const title = forumItems[i].getAttribute('data-title').toUpperCase();
        if (title.indexOf(filter) > -1) {
            forumItems[i].style.display = 'block';
        } else {
            forumItems[i].style.display = 'none';
        }
    }
});

window.selectForum = function(forumId) {
    for (let i = 0; i < forumItems.length; i++) {
        forumItems[i].classList.remove('active');
    }

    const selectedItem = document.querySelector(`.forum-item[data-id='${forumId}']`);
    selectedItem.classList.add('active');
    selectedForumField.value = forumId;
};
const popup = document.querySelector('.popup__content');
const openPopupButton = document.querySelector('[data-popup="post-creation"]');

if (popup && openPopupButton) {
    openPopupButton.addEventListener('click', function(event) {
        event.preventDefault();
        popup.classList.add('active');
        popup.removeAttribute('aria-hidden');
    });
}
</script>
<style>
    .theme-select-wrap {
        position: relative;
        margin-bottom: 20px;
    }

    #forum-search {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .forum-list {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
    }

    .forum-item {
        padding: 8px;
        margin-bottom: 5px;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }

    .forum-item:hover {
        background-color: #f0f0f0;
    }

    .forum-item.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .popup__bg {
        max-width: 600px;
        /* Adjust as needed */
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
    }

    #success-message {
        padding: 10px;
        margin: 15px 0;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        text-align: center;
        max-width: 100%;
        box-sizing: border-box;
    }

    .popup[aria-labelledby="post-creation"] .popup__bg {
        padding: 5rem 4rem;
        padding-top: 7rem;
        height: 100% !important;
    }

    .theme-network-fields {
        display: none;
    }

    .show-theme-network-fields {
        display: block !important;
    }
</style>