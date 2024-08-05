<?php
/**
 * Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = ! empty( $args['breadcrumbs'] ) ? $args['breadcrumbs'] : [];
?>
<nav class="breadcrumbs breadcrumbs--list" aria-label="Breadcrumb">
	<?php load_inline_styles( __DIR__, 'breadcrumbs' ); ?>
	<ol class="breadcrumbs__list" role="list">
	<?php foreach ($breadcrumbs as $crumb) : ?>
		
		<li class="breadcrumbs__item<?php if (isset($crumb['current']) && $crumb['current']) echo ' breadcrumbs__item--current'; ?>" role="listitem"<?php if (isset($crumb['current']) && $crumb['current']) echo ' aria-current="page"'; ?>>
			<?php if (!empty($crumb['url'])) : ?>
				<a href="<?php echo esc_url($crumb['url']); ?>" class="breadcrumbs__link"><?php echo esc_html($crumb['label']); ?></a>
			<?php else : ?>
				<span class="breadcrumbs__label"><?php echo esc_html($crumb['label']); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ol>
</nav>