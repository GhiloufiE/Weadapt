<?php

/**
 * Apply `bold` style to categories filtered by selected Theme/Network
 */


 add_action('acf/input/admin_footer', function () {
	 $filtered_categories = [];
 
	 $theme_network_query = new WP_Query([
		 'post_type'           => ['theme', 'network'],
		 'fields'              => 'ids',
		 'ignore_sticky_posts' => true,
		 'posts_per_page'      => -1,
		 'compare'             => 'LIKE'
	 ]);
 
	 if (!empty($theme_network_query->posts)) {
		 foreach ($theme_network_query->posts as $post_ID) {
			 $filtered_categories[$post_ID] = wp_get_post_categories($post_ID);
		 }
	 }
	 ?>
	 <script type="text/javascript">
		 const filteredCategories = <?php echo json_encode($filtered_categories); ?>;
	 </script>
	 <?php
 ?>
 <script type="text/javascript">
 (function($) {
 
	 const themeNetworkFieldKey = 'field_651f98cd28ce7'; 
	 const themeNetworkField = acf.getField(themeNetworkFieldKey);
 
	 const categoriesField = $('.acf-field-651f993a28ce8'); 
 
	 if (themeNetworkField && categoriesField.length) {
		 const categoriesItems = categoriesField.find('.acf-checkbox-list li');
 
		 const getSelectedIds = function() {
			 let selectedIds = [];
 
			 const selectedData = themeNetworkField.val();
 
			 if (selectedData) {
				 selectedIds = Array.isArray(selectedData) ? selectedData.map(id => parseInt(id)) : [parseInt(selectedData)];
			 }
 
			 return selectedIds;
		 };
 
		 const highlightCategories = function(ids) {
			 categoriesItems.removeClass('bold');
 
			 ids.forEach(id => {
				 const linkedCategories = filteredCategories[id];
 
				 if (linkedCategories) {
					 linkedCategories.forEach(categoryId => {
						 categoriesItems.each(function() {
							 const dataId = parseInt($(this).attr('data-id'));
							 if (categoryId === dataId) {
								 $(this).addClass('bold');
							 }
						 });
					 });
				 }
			 });
		 };
 
		 const initialSelectedIds = getSelectedIds();
		 highlightCategories(initialSelectedIds);
 
		 themeNetworkField.on('change', function() {
			 const selectedIds = getSelectedIds();
			 highlightCategories(selectedIds);
		 });
	 }
 })(jQuery);
 </script>
 <?php
 });