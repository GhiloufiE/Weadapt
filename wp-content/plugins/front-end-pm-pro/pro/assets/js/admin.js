jQuery(document).ready(function( $ ){		
	function fep_show_hide_pop3_fields(){
		if( 'pop3' == $('#ep_enable').val() ){
			$( '.fep-pop3-hidden-field' ).show('slow');
		} else {
			$( '.fep-pop3-hidden-field' ).hide('slow');
		}
	}
	if( $('#ep_enable').length ){
		fep_show_hide_pop3_fields();
	}
	
	$('.form-table').on( "change", "#ep_enable", function(e) {
		fep_show_hide_pop3_fields();
	});
});