<?php
/**
 * @param string      $format Optional. PHP date format. Defaults to the 'date_format' option.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default current post.
 */
 function insertBeforeLastTwo($str) {
 	if(empty($str)) return '';
   	$formattedTimezone = '';
	try {
		$lastFive = substr($str, -5);
		$length = strlen($lastFive);
	  	$format = substr($lastFive, 0, $length - 2) . ":" . substr($lastFive, $length - 2);
	  	$formattedTimezone = '(UTC ' . $format . ')';
	} catch (Exception $e) {
	  	$formattedTimezone = $str;
	}
   return $formattedTimezone;
 }

function get_event_formatted_date( $post_ID ) {
	$date_html  = '';
	$start_date = get_field( 'start_date', $post_ID );
	$end_date   = get_field( 'end_date', $post_ID );
	$timezone   = get_field( 'timezone', $post_ID );
	$timezoneFormatted = insertBeforeLastTwo($timezone);

	if ( ! empty( $start_date ) ) {
		$start_date_obj = new DateTime($start_date);
		$date_html= 'Event: ';
		$date_html .= $start_date_obj->format('d/m/Y') . ' - ' . $start_date_obj->format('H:i');

		if ( ! empty( $end_date )  ) {
			$end_date_obj = new DateTime($end_date);
			$date_html .= ' ' . $timezoneFormatted . ' - ';
			if( $end_date_obj->format('d/m/Y') !== $start_date_obj->format('d/m/Y') ) {
				$date_html .=  $end_date_obj->format('d/m/Y') . ' - ';
			}
			 $date_html .=  $end_date_obj->format('H:i');
			 $date_html .=  ' ' . $timezoneFormatted;
		}
	}
	return $date_html;
}
