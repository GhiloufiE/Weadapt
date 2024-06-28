jQuery(document).ready(function($){
	$('#fep_oa_fields').sortable({
		cursor: 'move',
	});

	var count = fep_oa_script.count;
	$(document).on('click', '.fep_oa_remove', function(){
		$(this).parent().parent().remove();
	});
	
	$('.fep_oa_add').on('click',function(){
		$('#fep_oa_fields').append('<div><span class="dashicons dashicons-move"></span><span><input type="text"  placeholder="'+fep_oa_script.name+'" required name="oa_admins[oa_'+count+'][name]" value=""/></span><span><input type="text"  placeholder="'+fep_oa_script.username+'" required name="oa_admins[oa_'+count+'][username]" value=""/></span><span><input type="button" class="button button-small fep_oa_remove" value="'+fep_oa_script.remove+'" /></span></div>' );
		count++;
		return false;
	});        
});