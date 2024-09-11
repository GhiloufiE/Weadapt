<?php

/**
 * Single references
 *
 * @package WeAdapt
 */
?>

<div class="single-references" >
	<?php load_inline_styles(__DIR__, 'single-references'); ?>
	<?php load_blocks_script('single-references', 'weadapt/single-references'); ?>

	<?php if (!empty($references_list = get_field('links_list'))) : ?>
		<ul class="single-references__list">
			<?php foreach ($references_list as $references_item) : ?>
				<?php if (!empty($url = $references_item['url']) && !empty($description = $references_item['description'])) : ?>
					<li class="single-references__item">
						<a href="<?php echo esc_url($url); ?>" class="single-references__item-text"><?php echo esc_html($description); ?></a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="single-references__actions">
		<?php
		$post_ID    = get_the_ID();
		$post_type  = get_post_type($post_ID);
		$class_name = 'wp-block-button__link ';

		if (isset($_COOKIE['like_' . $post_ID]) && $_COOKIE['like_' . $post_ID] === '1') {
			$class_name .= ' liked';
		}
		?>

		<div class="wp-block-button wp-block-button--template is-style-outline wp-block-button--like wp-block-button--like-1 has-icon-left" data-nonce="<?php echo wp_create_nonce('like'); ?>" data-id="<?php echo $post_ID; ?>" data-like>
			<button class="<?php echo $class_name; ?>">
				<span class='wp-block-button__text wp-block-button__text--like'><?php echo _e('Like', 'weadapt'); ?></span>
				<span class='wp-block-button wp-block-button__text--unlike'><?php echo _e('Unlike', 'weadapt'); ?></span>

				<?php echo get_img('icon-thumb-up'); ?>
			</button>
		</div>

		<?php


		if (post_type_supports($post_type, 'comments')) {
			get_part('components/button-comment/index', [
				'post_ID' => $post_ID,
			]);
		}

		get_part('components/button-share/index', [
			'url'   => get_permalink($post_ID),
			'type'  => $post_type,
			'class' => 'has-icon-left',
			'icon'  => 'icon-share',
		]);

		?>
	</div>
</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		if (window.location.hash === '#reply') {
			openReplyPopup();
		}
	});
	public / wp - content / themes / weadapt / parts / components / single - references / index.php

	function openReplyPopup() {
		var replyButton = document.querySelector('button[data-popup="comments"]');
		if (replyButton) {
			replyButton.click();
		}
	}
</script>