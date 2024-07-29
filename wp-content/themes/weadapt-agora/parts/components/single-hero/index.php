<?php

/**
 * Single Hero template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
$type = isset($args['type']) ? $args['type'] : '';
$post_ID = isset(get_queried_object()->term_id) ? get_queried_object()->term_id : get_the_ID();
$title = get_the_title();
$excerpt = has_excerpt() ? get_the_excerpt() : '';
$thumb_ID = 0;
$thumb_caption = '';
$post_author_html = '';
$document_list = get_field('document_list');
$document_item = !empty($document_list) ? $document_list[0] : null;
$file_ID = !empty($document_item) ? $document_item['file'] : null;
function parse_count($count_string)
{
	preg_match('/(\d+)/', $count_string, $matches);
	return isset($matches[1]) ? (int)$matches[1] : 0;
}
if (!empty($image_ID = get_field('image'))) {
	$thumb_ID = $image_ID;
	$thumb_caption = apply_filters('wp_get_attachment_caption', get_post_field('post_excerpt', $image_ID), $image_ID);
}

if (empty($thumb_ID) && has_post_thumbnail()) {
	$thumb_ID = get_post_thumbnail_id();
	$thumb_caption = get_the_post_thumbnail_caption();
}
echo get_part('components/popup/index', ['template' => 'post-creation']);
$fields = [];

$post_meta_items = [
	['icon-user', get_members_count($post_ID)],
	['icon-edit-pencil', get_post_meta_count($post_ID)],
	['icon-glob', get_post_meta_count($post_ID, ['case-study'], 'Case study', 'Case studies')],
];

switch ($type) {
	case 'theme':
		$fields = [
			'type_link' => ucfirst($type),
			'btn_join' => [
				'title' => __('Join Theme', 'weadapt'),
				'unjoin_title' => __('Unsubscribe Theme', 'weadapt'),
				'class' => 'button-join single-hero__action-btn',
				'icon' => ['icon-add', 'icon-delete']
			]
		];
		break;

	case 'network':
		$fields = [
			'type_link' => ucfirst($type),
			'btn_join' => [
				'title' => __('Join Network', 'weadapt'),
				'unjoin_title' => __('Unsubscribe Network', 'weadapt'),
				'class' => 'button-join single-hero__action-btn',
				'icon' => ['icon-add', 'icon-delete']
			]
		];

		break;

	case 'organisation':
		$fields = [
			'type_link' => ucfirst($type),
		];

		break;

	case 'forums':
		$fields = [
			'type_link' => __('Forum', 'weadapt'),
			'btn_join' => [
				'title' => __('Follow Forum', 'weadapt'),
				'unjoin_title' => __('Unfollow Forum', 'weadapt'),
				'class' => 'button-join single-hero__action-btn',
				'icon' => ['icon-add', 'icon-delete'],
				'join_ID' => $post_ID,
				'join_type' => 'forums'
			]
		];
		$post_meta_items = [
			['icon-user', get_members_count($post_ID, 'forums')],
			['icon-edit-pencil', get_post_meta_count($post_ID, ['forum'], 'Discussion', 'Discussions')],
		];

		if (empty($thumb_ID)) {
			$theme_network_ID = get_field('relevant_main_theme_network', $post_ID);
			if (!empty($theme_network_ID) && has_post_thumbnail($theme_network_ID)) {
				$thumb_ID = get_post_thumbnail_id($theme_network_ID);
			}
		}

		break;

	case 'forum':
		if (post_type_supports(get_post_type(), 'comments')) {
			$comments_count = wp_count_comments($post_ID);
			$replies_text = $comments_count->approved > 0 ? sprintf(_n('%s reply', '%s replies', $comments_count->approved, 'weadapt'), $comments_count->approved) : '';

			$post_meta_items = [
				['icon-calendar', get_the_date()],
				['icon-clock', get_estimate_reading_time(get_the_content())],
			];

			if ($comments_count->approved > 0) {
				$post_meta_items[] = ['icon-chat-20', $replies_text, 'replies-count'];
			}

			$likes_count = get_like_count($post_ID);
			if (parse_count($likes_count) > 0) {
				$post_meta_items[] = ['icon-thumb-up', $likes_count, 'likes-count'];
			}

			if (!empty($document_list)) {
				$download_count = get_download_count($file_ID);
				if (parse_count($download_count) > 0) {
					$post_meta_items[] = ['icon-download', $download_count, 'download-count'];
				}
			}
		} else {
			$post_meta_items = [
				['icon-calendar', get_the_date()],
				['icon-clock', get_estimate_reading_time(get_the_content())],
			];

			$likes_count = get_like_count($post_ID);
			if (parse_count($likes_count) > 0) {
				$post_meta_items[] = ['icon-thumb-up', $likes_count, 'likes-count'];
			}

			if (!empty($document_list)) {
				$download_count = get_download_count($file_ID);
				if (parse_count($download_count) > 0) {
					$post_meta_items[] = ['icon-download', $download_count, 'download-count'];
				}
			}
		}
		break;

	default:
		$fields = [
			'btn_join' => [
				'title' => __('Bookmark', 'weadapt'),
				'unjoin_title' => __('Unbookmark', 'weadapt'),
				'class' => 'button-join single-hero__action-btn',
				'icon' => 'icon-bookmark'
			]
		];

		$relevant = get_field('relevant');
		$main_theme_networks = get_field('relevant_main_theme_network', $post_ID);
		error_log(print_r($main_theme_networks, true));
		if (!empty($main_theme_networks) && is_array($main_theme_networks)) {
			$fields['type_link'] = []; 
			foreach ($main_theme_networks as $type_ID) {
				if (!empty($type_ID)) {
					$fields['type_link'][] = [ 
						'url' => get_permalink($type_ID),
						'title' => get_the_title($type_ID),
						'target' => '_self',
					];
				}
			}
		}

		$people = get_field('people');
		$authors = !empty($people['contributors']) ? $people['contributors'] : array();

		function get_download_count_as_downloads($file_id)
		{
			$views_text = get_download_count($file_id);
			$downloads_text = str_replace([' view', 'Views'], [' download', 'Downloads'], $views_text);
			return $downloads_text;
		}

		if (post_type_supports(get_post_type(), 'comments')) {
			$comments_count = wp_count_comments($post_ID);
			$replies_text = $comments_count->approved > 0 ? sprintf(_n('%s reply', '%s replies', $comments_count->approved, 'weadapt'), $comments_count->approved) : '';

			$post_meta_items = [
				['icon-calendar', get_the_date()],
				['icon-clock', get_estimate_reading_time(get_the_content())],
			];

			if ($comments_count->approved > 0) {
				$post_meta_items[] = ['icon-chat-20', $replies_text, 'replies-count'];
			}

			$likes_count = get_like_count($post_ID);
			if (parse_count($likes_count) > 0) {
				$post_meta_items[] = ['icon-thumb-up', $likes_count, 'likes-count'];
			}

			if (!empty($document_list)) {
				$download_count = get_download_count_as_downloads($file_ID);
				if (parse_count($download_count) > 0) {
					$post_meta_items[] = ['icon-download', $download_count, 'download-count'];
				}
			}
		} else {
			$post_meta_items = [
				['icon-calendar', get_the_date()],
				['icon-clock', get_estimate_reading_time(get_the_content())],
			];

			$likes_count = get_like_count($post_ID);
			if (parse_count($likes_count) > 0) {
				$post_meta_items[] = ['icon-thumb-up', $likes_count, 'likes-count'];
			}

			if (!empty($document_list)) {
				$download_count = get_download_count($file_ID);
				if (parse_count($download_count) > 0) {
					$post_meta_items[] = ['icon-download', $download_count, 'download-count'];
				}
			}
		}
		break;
}
?>
<style>
	.post-meta__item svg {
		width: 17px;
		height: 16px
	}

	@media (min-width: 768px) {
		.post-meta {
			gap: 1.5rem !important;
		}
	}
</style>
<section class="single-hero">
	<?php load_inline_styles(__DIR__, 'single-hero'); ?>
	<?php load_blocks_script('single-hero', 'weadapt/single-hero'); ?>

	<div class="single-hero__container container">
		<div class="single-hero__row row <?php echo empty($thumb_ID) ? 'single-hero__row_top' : ''; ?>">
			<div class="single-hero__left">
				<div class="single-hero__left-inner">
				<?php if (array_key_exists('type_link', $fields) && !empty($fields['type_link']) && is_array($fields['type_link'])) : ?>
					    <div class="single-hero__types">
						<style>
						.single-hero__types {
						    display: flex;
						    gap: 1rem;
						    flex-wrap: wrap; /* Ensure items wrap if necessary */
						}
						
						@media (max-width: 768px) {
						    .single-hero__types {
						        flex-direction: column;
						        gap: 0.5rem; /* Adjust gap for smaller screens */
						    }
						}
						</style>
					        <?php foreach ($fields['type_link'] as $type_item) : 
					            echo get_button(
					                $type_item,
					                'outline-small',
					                'single-hero__type'
					            );
					        endforeach; ?>
					    </div>
					<?php endif; ?>

					<h1 class="single-hero__title" id="main-heading"><?php echo $title; ?></h1>

					<?php if ($excerpt) : ?>
						<div class="single-hero__excerpt"><?php echo $excerpt; ?></div>
					<?php endif; ?>

					<?php
					if (!empty($authors)) {
						$author_class = 'single-hero__author cpt-list-item__author';

						if (count($authors) > 1) {
							$author_class .= ' cpt-list-item__author--multiple';
						}
						the_post_author_html($authors, $author_class, '', $post_author_html);
					}
					?>

					<?php if (!empty($post_meta_items)) : ?>
						<ul class="post-meta single-hero__meta">
							<?php
							$post_ID = get_the_ID();
							$views = get_post_views($post_ID);
							echo " $views";

							set_post_views($post_ID); ?>
							<?php foreach ($post_meta_items as $item) :
								$id = isset($item[2]) ? 'id="' . $item[2] . '"' : '';
							?>
								<li class="post-meta__item">

									<span class="icon" aria-label="<?php echo esc_attr($item[0]); ?>"><?php echo get_img($item[0]); ?></span>

									<span class="text" <?php echo $id; ?>><?php echo $item[1]; ?></span>
								</li>
							<?php endforeach; ?>

							<?php
							if ($type === 'theme') : ?>
								<div class="wp-block-button">
									<button class="wp-block-button__link has-background" data-post-id="<?php echo esc_attr($post_ID); ?>" data-post-type="forum" data-popup="post-creation" onclick="setPostDetails(this)">
										<?php echo sprintf("<span>%s</span>", esc_html__("Start a conversation", "weadapt")); ?>
									</button>
								</div>
								<script>
									function setPostDetails(button) {
										const postId = button.getAttribute('data-post-id');
										const postType = button.getAttribute('data-post-type');
										const forumField = document.getElementById('forum-field');
										const postTypeField = document.getElementById('post-type-field');

										if (forumField) {
											forumField.value = postId;
										}
										if (postTypeField) {
											postTypeField.value = postType;
										}
									}
								</script>
							<?php endif; ?>
							<?php if ($type === 'event') :
								$date_html = get_event_formatted_date($post_ID);
								if (!empty($date_html)) : ?>
									<li class="post-meta__item">
										<span class="text"><b><?php echo $date_html; ?></b></span>
									</li>
								<?php endif ?>
							<?php endif ?>
						</ul>
					<?php endif; ?>

				</div>
			</div>

			<div class="single-hero__right">
				<?php if (!empty($thumb_ID)) : ?>
					<figure class="single-hero__image img-caption">
						<?php echo get_img($thumb_ID); ?>

						<?php if (!empty($thumb_caption)) : ?>
							<figcaption class="img-caption__caption"><?php echo wp_kses_post($thumb_caption); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<?php
				if (!empty($fields['btn_link'])) {
					$btn_icon = !empty($fields['btn_icon']) ? $fields['btn_icon'] : '';

					echo get_button(
						$fields['btn_link'],
						'',
						'single-hero__action-btn',
						$btn_icon
					);
				}

				if (!empty($fields['btn_join'])) {
					get_part('components/button-join/index', $fields['btn_join']);
				}
				?>
			</div>
		</div>
	</div>
</section>