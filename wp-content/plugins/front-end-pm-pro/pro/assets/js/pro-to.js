jQuery(document).ready(function( $ ){
	$(document).on('change', '.fep_pro_to_checkbox', function(){
		if($(this).prop("checked")) {
			$('.fep_pro_to_checkbox').not($(this)).prop('checked',false);
			$('.fep_pro_to_div').not($(this).closest('.fep_pro_to_div')).hide('slow');
			$(this).closest('.fep_pro_to_div').find('.fep_pro_to_field_div').show('slow');
		} else {
			$('.fep_pro_to_div').show('slow');
			$('.fep_pro_to_field_div').hide('slow');
		}
	});
	$(".fep_pro_to_checkbox").each(function(){
		if($(this).prop("checked")) {
			$(this).trigger("change");
			return false;
		} else {
			$(this).trigger("change");
		}
	});
	$( document ).on( "fep_form_submit_done", function( event, response, thisForm ) {
		$("#fep_mr_to").tokenInput("clear");
		$(".fep_pro_to_checkbox").trigger("change");
	});
});
