jQuery( document ).ready( function( $ ) {
	var textarea = $( '#respond textarea' );
	var comment_submit_button = $('#respond input[type=submit]').prop( 'disabled', 'disabled' );
	var html = '<div class="sce-ccc-meter">';
	html += '<span></span>';
	html += '</div>';
	$(textarea).after(html);
	sce_progress_bar(textarea, 'sce-ccc-meter', comment_submit_button);
	$(textarea).keyup(function () {
		sce_progress_bar( this, 'sce-ccc-meter', comment_submit_button);
	});
	$('body').on('sce.edit.show', function( event, sce_textarea, comment_id) {
		var sce_submit_button = $('#sce-edit-comment' + comment_id + ' button').prop( 'disabled', 'disabled' );
		var html = '<div class="sce-ccc-meter sce-ccc-meter-' + comment_id + '">';
		html += '<span></span>';
		html += '</div>';
		if ( $('#sce-edit-comment' + comment_id + ' .sce-ccc-meter' ).length <= 0 ) {
			$(sce_textarea).after(html);
		}
		sce_progress_bar( sce_textarea, 'sce-ccc-meter-' + comment_id, sce_submit_button );
		$(sce_textarea).keyup(function () {
			sce_progress_bar( this, 'sce-ccc-meter-' + comment_id, sce_submit_button );
		});
	} );
	function sce_progress_bar( sce_progress_bar_textarea, className, submit_button ) {
		var max = sce_ccc.max_length;
		var min = sce_ccc.min_length;
		var len = $(sce_progress_bar_textarea).val().length;
		var width = len / max * 100;
		$('.' + className + ' span').css('width', width + '%' );
		if ( len > min ) {
			$('.' + className).removeClass('sce-ccc-invalid').addClass( 'sce-ccc-valid' );
			$( submit_button ).removeAttr( 'disabled' );
		}
		if ( len > max || len < min ) {
			$('.' + className).removeClass('sce-ccc-valid').addClass( 'sce-ccc-invalid' );
			$( submit_button ).prop('disabled', 'disabled');
		}
	}
} );