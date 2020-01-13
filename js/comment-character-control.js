jQuery( document ).ready( function( $ ) {
	var textarea = $( '#respond textarea' );
	var submit_button = $('#respond input[type=submit]').prop( 'disabled', 'disabled' );
	var html = '<div class="sce-ccc-meter">';
	html += '<span></span>';
	html += '</div>';
	$(textarea).after(html);
	sce_progress_bar(textarea);
	$('textarea').keyup(function () {
		sce_progress_bar( this );
	});
	function sce_progress_bar( textarea ) {
		var max = sce_ccc.max_length;
		var min = sce_ccc.min_length;
		var len = $(textarea).val().length;
		var width = len / max * 100;
		$('.sce-ccc-meter span').css('width', width + '%' );
		if ( len > min ) {
			$('.sce-ccc-meter').removeClass('sce-ccc-invalid').addClass( 'sce-ccc-valid' );
			$( submit_button ).removeAttr( 'disabled' );
		}
		if ( len > max || len < min ) {
			$('.sce-ccc-meter').removeClass('sce-ccc-valid').addClass( 'sce-ccc-invalid' );
			$( submit_button ).prop('disabled', 'disabled');
		}
	}
} );