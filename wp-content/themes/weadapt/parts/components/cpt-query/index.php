<?php

$temp_args = !empty($args['query_args']) ? $args['query_args'] : [];
$show_filters = isset($args['show_filters']) ? wp_validate_boolean($args['show_filters']) : true;
$show_search = isset($args['show_search']) ? wp_validate_boolean($args['show_search']) : false;
$show_post_types = isset($args['show_post_types']) ? wp_validate_boolean($args['show_post_types']) : true;
$show_categories = isset($args['show_categories']) ? wp_validate_boolean($args['show_categories']) : false;
$show_loadmore = isset($args['show_loadmore']) ? wp_validate_boolean($args['show_loadmore']) : true;
$hide_no_found = isset($args['hide_no_found']) ? wp_validate_boolean($args['hide_no_found']) : false;
$initial_empty_sort_by = isset($args['initial_empty_sort_by']) ? wp_validate_boolean($args['initial_empty_sort_by']) : false;
$base_url = get_current_clean_url();

$query_type = isset($args['query_type']) ? $args['query_type'] : 'wp_query';

$temp_args['orderby'] = isset($temp_args['orderby']) ? $temp_args['orderby'] : 'date';
$temp_args['order'] = isset($temp_args['order']) ? $temp_args['order'] : 'DESC';

$args_for_query = apply_filters('args_for_query', $temp_args, $_GET, $query_type);
$query_args = $args_for_query['args'];

if ($query_type === 'user_query') {
	$query_args['orderby'] = isset($query_args['orderby']) ? $query_args['orderby'] : 'registered';
	$query_args['order'] = isset($query_args['order']) ? $query_args['order'] : 'DESC';

	$user_query = new WP_User_Query($query_args);

	$posts_per_page = $temp_args['number'] ? intval($temp_args['number']) : get_option('posts_per_page');
	$max_num_pages = ceil($user_query->get_total() / $posts_per_page);

	if ($show_search && empty($user_query->results)) {
		$show_search = true;
	}
} else {
	$query = new WP_Query($query_args);

	$max_num_pages = $query->max_num_pages;
}

load_blocks_script('cpt-query', 'weadapt/cpt-query');
?>
<div class="cpt-query">
	<div class="cpt-filters<?php echo !$show_filters ? ' hidden' : ''; ?>">
		<form class="cpt-filters__form">
			<?php
			if ($show_filters) {
				get_part('components/cpt-filters/index', [
					'args_for_query'  		=> $args_for_query,
					'query_type'      		=> $query_type,
					'show_post_types' 		=> $show_post_types,
					'show_categories' 		=> $show_categories,
					'$initial_empty_sort_by' => $initial_empty_sort_by
				]);
			}
			?>

			<div class="cpt-filters__terms<?php echo !empty($post_types = $args_for_query['post_types']) || (!$show_categories && !empty($categories = $args_for_query['categories'])) ? '' : ' hidden'; ?>">
				<ul class="cpt-filters__list">
					<?php if (!empty($post_types)) :
						do_action('selected_post_types_filter', $base_url, $args_for_query);
					endif; ?>

					<?php if (!$show_categories && !empty($categories)) :
						do_action('selected_categories_filter', $base_url, $args_for_query);
					endif; ?>
				</ul>
			</div>

			<?php if ($show_search) : ?>
				<div class="cpt-filters__form__search">
					<input type="search" name="search" placeholder="<?php _e('Search...', 'weadapt'); ?>" class="cpt-filters__form__input">

					<button type="submit" class="wp-block-button__link cpt-filters__form__button">
						<?php _e('Search', 'weadapt'); ?>
						<?php echo get_img('icon-search-small'); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php
			if ($show_categories) {
				$selected_categories = !empty($_GET['categories']) ? wp_parse_id_list($_GET['categories']) : [];
				$categories = wp_get_post_terms(get_the_ID(), 'category', [
					'hide_empty' => false
				]);
				$unique_ID = get_unique_ID('form');

				if (!empty($categories)) :
			?><ul class="cpt-filters__categories"><?php
													foreach ($categories as $term) {
													?>
							<li class="cpt-filters__category">
								<input id="term-<?php echo esc_attr("$unique_ID-$term->term_id"); ?>" type="checkbox" name="categories[]" value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id, $selected_categories) ? 'checked' : null; ?>>
								<label tabindex="0" for="term-<?php echo esc_attr("$unique_ID-$term->term_id"); ?>" class="dropdown-wrapper__btn"><?php echo $term->name; ?></label>
							</li>
						<?php
													}
						?>
					</ul>
					<input type="hidden" value="1" name="has_categories">
			<?php
				endif;
			}
			?>

			<input type="hidden" value="<?php echo esc_attr($query_type); ?>" name="query_type">
			<input type="hidden" value="<?php echo esc_attr(json_encode($temp_args)); ?>" name="args" />
			<input type="hidden" value="<?php echo esc_attr($args_for_query['search_query']); ?>" name="s" />
			<input type="hidden" value="<?php echo esc_url($base_url); ?>" name="base_url" />
			<?php /* Reload page only if filters are shown */ ?>
			<input type="hidden" value="<?php echo esc_attr($show_filters); ?>" name="reload_page" />

			<input type="hidden" value="<?php echo esc_attr($args_for_query['post_type']); ?>" name="post_type" />
		</form>

		<?php
		if ($query_type === 'user_query') {
			load_inline_styles('/parts/components/member-item', 'member-item');
		}
		?>
	</div>

	<div class="cpt-latest row--ajax" data-page="<?php echo get_query_var('paged') ? get_query_var('paged') : 1; ?>" data-pages="<?php echo $max_num_pages; ?>">
		<?php
		// WP_User_Query
		if ($query_type === 'user_query') {
			if (!empty($user_query->results)) {
				foreach ($user_query->results as $user_ID) :
					echo get_part('components/member-item/index', [
						'member_ID' => $user_ID
					]);
				endforeach;
			} else {
				if (!$hide_no_found) {
					echo sprintf('<span class="empty-result">%s</span>', __('Nothing found.', 'weadapt'));
				}
			}
		}

		// WP_Query
		else {
			if (
				(isset($query_args['post__in']) && empty($query_args['post__in'])) ||
				!$query->have_posts()
			) {
				$max_num_pages = 0;
		
				if (!$hide_no_found) {
					echo sprintf('<span class="empty-result">%s</span>', __('Nothing found.', 'weadapt'));
				}
				
			} else {
				if ($query->have_posts()) {
					while ($query->have_posts()) {
						$query->the_post();
		
						foreach ([
							'theme_show_buttons',
							'theme_is_author_page',
							'theme_short_excerpt'
						] as $query_var) {
							if (!empty($query_args[$query_var])) {
								set_query_var($query_var, wp_validate_boolean($query_args[$query_var]));
							}
						}
		
						the_archive_template_grid();
					}
				}
			}
		}

			wp_reset_postdata();
		?>
	</div>

	<?php if ($show_loadmore) : ?>
		<div class="wp-block-button wp-block-button--template cpt-more<?php echo $max_num_pages > 1 ? '' : ' hidden'; ?>">
			<button type="button" class="wp-block-button__link cpt-more__btn">
				<?php _e('Load more', 'weadapt'); ?>
			</button>
		</div>
	<?php endif; ?>
</div>