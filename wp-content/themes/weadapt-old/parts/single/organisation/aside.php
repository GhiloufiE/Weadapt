<?php
/**
 * Single Organisation Aside
 *
 * @package WeAdapt
 */

get_part('components/info-widget-cpt/index', [
	'cpt_ID'      => get_the_ID(),
	'cpt_buttons' => ['website', 'share']
]);

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);