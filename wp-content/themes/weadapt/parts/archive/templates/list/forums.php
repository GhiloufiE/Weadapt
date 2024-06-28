<?php $post_ID = !empty($args["post_ID"]) ? $args["post_ID"] : 0;

if (!empty($post_ID)):
	$post_meta = [
		["icon-user", get_members_count($post_ID)],
		[
			"icon-edit-pencil",
			get_post_meta_count($post_ID, ["forum"], "Post", "Posts"),
		],
	]; ?>
	<article class="cpt-list-item theme-list-item">
		<?php
		$post_ID = !empty($args["post_ID"]) ? $args["post_ID"] : 0;

		$thumb_ID = 0;

		if (has_post_thumbnail($post_ID)) {
			$thumb_ID = get_post_thumbnail_id();
		}
		if (empty($thumb_ID)) {
			$theme_network_ID = get_field("relevant_main_theme_network", $post_ID);

			if (
				!empty($theme_network_ID) &&
				has_post_thumbnail($theme_network_ID)
			) {
				$thumb_ID = get_post_thumbnail_id($theme_network_ID);
			}
		}

		if (!empty($thumb_ID)): ?>
			<div class="cpt-list-item__image">
				<a href="<?php if ("draft" === get_post_status()) {
					echo get_edit_post_link($post_ID);
				} else {
					the_permalink($post_ID);
				} ?>" class="cpt-list-item__image-link">
					<?php echo get_img($thumb_ID, "cpt-list-item"); ?>
				</a>
			</div>
		<?php endif;
		?>

		<div class="cpt-list-item__content">
			<?php
			the_post_type_html($post_ID);
			the_post_title_html($post_ID);
			the_post_excerpt_html($post_ID);
			the_post_meta_html($post_meta);

			/* the_post_tag_html( $post_ID ); */
			?>

			<div class="cpt-list-item__actions">
				<?php echo get_button(
					[
						"url" => get_permalink($post_ID),
						"title" => __("View", "weadapt"),
						"target" => "",
					],
					"outline"
				); ?>

				<div class="wp-block-button">
					<button class="wp-block-button__link has-background" data-post-id="<?php echo esc_attr($post_ID); ?>"
						data-post-type="forum" data-popup="post-creation" onclick="setPostDetails(this)">
						<?php echo sprintf("<span>%s</span>", esc_html__("Start a conversation", "weadapt")); ?>
					</button>
				</div>
			</div>
		</div>

		<script>function setPostDetails(button) {
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


	</article><?php
endif; ?>