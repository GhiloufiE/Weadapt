<?php

/**
 * Get Template Part with vars
 *
 * Like get_template_part() put lets you pass args to the template file
 * Args are available in the tempalte as $args array
 * @param string filepart
 * @param mixed wp_args style argument list
 */
function get_part( $file, $args = [] ) {
	return get_template_part( "/parts/$file", null, $args );
}
