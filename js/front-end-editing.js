jQuery( document ).ready( function( $ ) {
	$('.sce-save-comment').on('click', function( e ) {
		e.preventDefault();
		var nonce = $( '#sce_nonce' ).val();
		var comment_id = $( '#sce-comment-id' ).val();
		var comment_content = $.trim( $( '#sce-content' ).val() );
		var comment_name = $.trim( $( '#sce-name' ).val() );
		var comment_email = $.trim( $( '#sce-email' ).val() );
		var comment_url = $.trim( $( '#sce-url' ).val() );
		var status = $( 'input[name="status"]:checked' ).val();
		$( this ).html( sce_frontend.saving ).prop( 'disabled', 'disabled' );
		$.post( sce_frontend.ajaxurl, { action: 'sce_frontend_save_comment', comment_id: comment_id, nonce: nonce, content: comment_content, name: comment_name, email: comment_email, url: comment_url, status: status }, function( response ) {
			if ( 'spam' === status ) {
				window.parent.jQuery( '#comment-' + comment_id ).fadeOut( 'slow' );
			} else {
				window.parent.jQuery( '#sce-front-end-comment-' + comment_id ).html( comment_content );
				window.parent.jQuery( '#comment-' + comment_id ).find( '.comment-author-link' ).html( comment_name );
			}
			window.parent.jQuery.fancybox.close();
		} );
	} );
	$('.sce-delete-comment').on('click', function( e ) {
		e.preventDefault();
		var nonce = $( '#sce_nonce' ).val();
		var comment_id = $( '#sce-comment-id' ).val();
		$( this ).html( sce_frontend.deleting ).prop( 'disabled', 'disabled' );
		$.post( sce_frontend.ajaxurl, { action: 'sce_frontend_delete_comment', comment_id: comment_id, nonce: nonce }, function( response ) {
			window.parent.jQuery( '#comment-' + comment_id ).fadeOut( 'slow' );
			window.parent.jQuery.fancybox.close();
		} );
	} );
	$('.sce-close').on('click', function( e ) {
		e.preventDefault();
		window.parent.jQuery.fancybox.close();
	} );
} );