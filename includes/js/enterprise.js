$(document).ready(function() {
	$('.tdDate').attr('title','Date (dd/mm/yyy)');
	$('.tdAutocomplete').attr('title','Auto Complete');
	//$('.timeMask').mask("99:99").css('width', '40px');
	$('.datepick').datepicker({dateFormat : 'dd-mm-yy',showAnim : 'fadeIn',changeMonth : true,changeYear : true}).attr("readonly", "readonly").css('width', '100px').css('font-size', '11px');
	$('.autoFillReadonly').attr('readonly','readonly');
});