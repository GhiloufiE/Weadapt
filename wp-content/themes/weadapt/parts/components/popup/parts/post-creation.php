<div class="popup__header" id="new-post">
    <button class="back" data-popup="post-creation" aria-label="<?php _e('Back', 'weadapt'); ?>"><?php echo get_img('icon-arrow-left'); ?></button>
    <button class="close" data-popup="post-creation" aria-label="<?php _e('Close', 'weadapt'); ?>"><?php echo get_img('icon-close'); ?></button>
    <h3 class="popup__header__title" id="post-creation"><?php _e('Start a conversation', 'weadapt'); ?></h3>
</div>
<div class="popup__content sidebar-popup" aria-labelledby="post-creation" aria-hidden="true" id="post-creation-popup">
    <?php if ( is_user_logged_in() ) : ?>
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
            <input type="hidden" name="forum" id="forum-field" value="">
            <input type="hidden" name="post_type" id="post-type-field" value="">
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
    const form = document.getElementById('post-creation-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(form);
        fetch('<?php echo esc_url(admin_url('admin-post.php')); ?>', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('success-message').style.display = 'block';
                form.reset(); 
            } else {
                alert('There was an error submitting your post: ' + data.data);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

<style>
.popup__bg {
    max-width: 600px; /* Adjust as needed */
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
</style>