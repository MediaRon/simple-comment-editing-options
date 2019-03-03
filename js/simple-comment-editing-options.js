jQuery( document ).ready( function( $ ) {
	if( 'compact' === sce_options.timer_appearance ) {
		sce_hooks.addFilter( 'sce.comment.timer.text', 'simple-comment-editing-options', function( timer_text, days_text, hours_text, minutes_text, seconds_text, days, hours, minutes, seconds ) {
			timer_text = '';
			if( days > 0 ) {
				if( days < 10 ) {
					timer_text += '' + '0' + days;
				} else {
					timer_text += days;
				}
				timer_text += ':';
			}
			if( hours > 0 ) {
				if( hours < 10 ) {
					timer_text += '' + '0' + hours;
				} else {
					timer_text += hours;
				}
				timer_text += ':';
			} else if( hours === 0 && days > 0 ) {
				timer_text += '00';
				timer_text += ':';
			}
			if( minutes > 0 ) {
				if( minutes < 10 ) {
					timer_text += '' + '0' + minutes;
				} else {
					timer_text += minutes;
				}
				timer_text += ':';
			} else if( minutes === 0 && hours > 0 ) {
				timer_text += '00';
				timer_text += ':';
			}
			if (seconds > 0) {
				if( seconds < 10 ) {
					timer_text += '' + '0' + seconds;
				} else {
					timer_text += seconds;
				}
			} else if( seconds === 0 && minutes > 0 ) {
				timer_text += '00';
			}
			return timer_text;
		} );
	}
	var simplecommenteditingoptions = $.simplecommenteditingoptions = $.fn.simplecommenteditingoptions = function() {
		var $this = this;
		return this.each( function() {
			var ajax_url = $( this ).find( 'a:first' ).attr( 'href' );
			var ajax_params = wpAjax.unserialize( ajax_url );
			var element = this;
			jQuery(element).on( 'sce.timer.loaded', function(e) {
				if ( sce_options.show_stop_timer ) {
					$( element ).find( '.sce-timer' ).after( '<div class="sce-timer-cancel-wrapper"><button class="sce-timer-cancel">' + sce_options.stop_timer_svg + sce_options.stop_timer_text + '</button></div>');
					$( element ).siblings( '.sce-textarea' ).find( ' .sce-timer' ).after( '<div class="sce-timer-cancel-wrapper"><button class="sce-timer-cancel">' + sce_options.stop_timer_svg + sce_options.stop_timer_text + '</button></div>' );
				}
			} );
			jQuery( element ).on( 'click', '.sce-timer-cancel', function( e ) {
				e.preventDefault();
				cancel_timer( element );
			} );
			jQuery( element ).siblings( '.sce-textarea' ).find( '.sce-timer' ).on( 'click', '.sce-timer-cancel', function( e ) {
				e.preventDefault();
				cancel_timer( element );
			} );
			function cancel_timer( element ) {
				$( element ).siblings( '.sce-textarea' ).off();
				$( element ).off();

				//Remove elements
				$( element ).parent().remove();

				$.post( ajax_url, { action: 'sce_stop_timer', comment_id: ajax_params.cid, post_id: ajax_params.pid, nonce: ajax_params._wpnonce }, function( response ) {
					// do nothing for now
				}, 'json' );
			}
		} );

	};
	$( '.sce-edit-button' ).simplecommenteditingoptions();
} );
function sce_get_comment(e) {
	e.preventDefault();
	var $ = jQuery;
	$element = $(e.target);
	var url = wpAjax.unserialize( $element.attr( 'href' ) );
	$.post( $element.attr( 'href' ), { action: 'sce_get_moderation_comment', comment_id: url.comment_id, nonce: url.nonce}, function( response ) {
		var $wrapper_clone = $( '.sce-inline-interface-hidden:last' ).clone();
		$wrapper_clone.removeClass( 'sce-inline-interface-hidden' ).addClass( 'sce-inline-interface' );

		// Add name to wrapper
		$wrapper_clone.find('.sce-inline-name').val( response.comment_author );

		// Add email to wrapper
		$wrapper_clone.find( '.sce-inline-email' ).val( response.comment_author_email );

		// Add URL to wrapper
		$wrapper_clone.find( '.sce-inline-url' ).val( response.comment_author_url );

		// Add Comment to wrapper
		$wrapper_clone.find( '.sce-inline-comment' ).val( response.comment_content );

		// Add comment status to wrapper
		var comment_status = response.comment_approved;
		if( 1 == comment_status ) {
			$wrapper_clone.find('.sce-inline-status-wrapper .approved' ).attr( 'checked', 'checked' );
		} else if ( 2 == comment_status ) {
			$wrapper_clone.find('.sce-inline-status-wrapper .pending' ).attr( 'checked', 'checked' );
		} else {
			$wrapper_clone.find('.sce-inline-status-wrapper .spam' ).attr( 'checked', 'checked' );
		}

		$wrapper_clone.find('button').attr('data-comment_id', url.comment_id );
		$wrapper_clone.find('button').attr('data-nonce', url.nonce );
		$wrapper_clone.appendTo( '#sce-comment' + url.comment_id );
	}, 'json' );
}