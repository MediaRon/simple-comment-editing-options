jQuery( document ).ready( function( $ ) {
	$('.sce-form-table').on('click', '.sce-restore-comment', function( e ) {
		e.preventDefault();
		var $elem = $(this);
		var ajax_url = $elem.attr('href');
		var comment_id = $elem.data('comment-id');
		var record_id = $elem.data('id');
		var nonce = $elem.data('nonce');
		$.post( ajax_url, { action: 'sce_restore_comment', comment_id: comment_id, nonce: nonce, id: record_id }, function( response ) {
			$('.wp-editor-area').html(response);
		});
	} );
} );