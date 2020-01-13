jQuery( document ).ready( function( $ ) {
	var textarea = $( '#respond textarea' );
	var submit_button = $('#respond input[type=submit]').prop( 'disabled', 'disabled' );
	html = '<progress max="2000" value="0" aria-hidden="true" class="sce-ccc-status" style="display: block; width: 100%;"></progress>';
	$(textarea).after(html);
	sce_progress_bar(textarea);
	$('textarea').keyup(function () {
		sce_progress_bar( this );
	});
	function sce_progress_bar( textarea ) {
		var max = 2000;
		var min = 100;
		var len = $(textarea).val().length;
		$('.sce-ccc-status').val( len );
		if ( len > min ) {
			$('.sce-ccc-status').removeClass('sce-ccc-invalid').addClass( 'sce-ccc-valid' );
			$( submit_button ).removeAttr( 'disabled' );
		}
		if ( len > max || len < min ) {
			$('.sce-ccc-status').removeClass('sce-ccc-valid').addClass( 'sce-ccc-invalid' );
			$( submit_button ).prop('disabled', 'disabled');
		}
	}
} );