<?php

/**
 * Apply `bold` style to categories filtered by selected Theme/Network
 */
add_action('acf/input/admin_footer', function () {
	$filtered_categories = [];

	$theme_network_query = new WP_Query( [
		'post_type'           => ['theme', 'network'],
		'fields'              => 'ids',
		'ignore_sticky_posts' => true,
		'posts_per_page'      => -1,
	] );

	if ( ! empty( $theme_network_query->posts ) ) {
		foreach ( $theme_network_query->posts as $post_ID ) {
			$filtered_categories[$post_ID] = wp_get_post_categories( $post_ID );
		}
	}
?>
<script type="text/javascript">
(function($) {
	const filteredCategories = <?php echo json_encode( $filtered_categories ); ?>

	const themeNetworkField = document.querySelector('#acf-field_651f98cd28ce7');
	const categoriesField   = document.querySelector('.acf-field-651f993a28ce8');

	if (filteredCategories && themeNetworkField && categoriesField) {
		const categoriesItems   = categoriesField.querySelectorAll('.acf-checkbox-list li');

		const filterCategoriesByTheme = function (id) {
			categoriesItems.forEach(categoriesItem => {
				categoriesItem.classList.remove('bold');
			});

			const filteredCategoriesItems = Array.from(categoriesItems).filter(function(liElement) {
				const dataId = parseInt(liElement.getAttribute('data-id'));

				return filteredCategories[id].includes(dataId);
			});

			if (filteredCategoriesItems) {
				filteredCategoriesItems.forEach(categoriesItem => {
					categoriesItem.classList.add('bold');
				});
			}
		};

		if (themeNetworkField.value) {
			filterCategoriesByTheme(themeNetworkField.value);
		}

		jQuery(document).on('change','#acf-field_651f98cd28ce7', function () {
			filterCategoriesByTheme(this.value);
		});
	}
})(jQuery);
</script>
<?php
});