<?php
/**
 * Edit Profile aside
 *
 * @package WeAdapt
 */

get_part('components/info-widget-user/index', [
	'user_ID'           => get_current_user_id(),
	'show_share_button' => true
]);