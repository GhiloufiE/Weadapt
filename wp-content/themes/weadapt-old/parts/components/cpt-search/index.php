<?php
$type = isset($args['type']) ? $args['type'] : '';
$id = 'search-organisations';
$title = 'Search organisations';

if ($type === 'members') {
	$title = 'Search members';
	$id = 'search-members';
}

$action = "/$id";
?>
<form class="cpt-search" action="<?php echo $action; ?>" method="get" title="<?php echo $title; ?> Form">
	<?php load_inline_styles( __DIR__, 'cpt-search' ); ?>

	<label for="<?php echo $id; ?>" id="<?php echo $id; ?>-label" class="screen-reader-text"><?php echo $title; ?></label>
	<input class="cpt-search__input" type="text" id="<?php echo $id; ?>" name="q" aria-label="<?php echo $title; ?> Input" aria-labelledby="<?php echo $id; ?>-label" placeholder="<?php _e( 'Search...', 'weadapt' ); ?>">

	<div class="wp-block-button wp-block-button--template  cpt-search__button">
		<button type="button" class="wp-block-button__link">
			<?php _e('Search', 'weadapt'); ?>
			<?php echo get_img('icon-search-small'); ?>
		</button>
	</div>
</form>