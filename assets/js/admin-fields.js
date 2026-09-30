/**
 * Media picker UI for the theme's hand-rolled meta boxes (gallery + single image).
 */
( function ( $ ) {
	'use strict';

	function initGalleryFields() {
		$( '.noor-gallery-field' ).each( function () {
			var $wrap = $( this );
			var $input = $wrap.find( 'input[type="hidden"]' );
			var $preview = $wrap.find( '.noor-gallery-preview' );

			$wrap.find( '.noor-gallery-select' ).on( 'click', function ( e ) {
				e.preventDefault();
				var frame = wp.media( { title: 'اختر الصور', multiple: true, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var selection = frame.state().get( 'selection' );
					var ids = [];
					$preview.empty();
					selection.each( function ( attachment ) {
						var data = attachment.toJSON();
						ids.push( data.id );
						var thumb = ( data.sizes && data.sizes.thumbnail ) ? data.sizes.thumbnail.url : data.url;
						$preview.append( $( '<img>' ).attr( 'src', thumb ) );
					} );
					$input.val( ids.join( ',' ) );
				} );
				frame.open();
			} );

			$wrap.find( '.noor-gallery-clear' ).on( 'click', function ( e ) {
				e.preventDefault();
				$input.val( '' );
				$preview.empty();
			} );
		} );
	}

	function initSingleImageFields() {
		$( '.noor-single-image-field' ).each( function () {
			var $wrap = $( this );
			var $input = $wrap.find( 'input[type="hidden"]' );
			var $preview = $wrap.find( '.noor-gallery-preview' );

			$wrap.find( '.noor-single-image-select' ).on( 'click', function ( e ) {
				e.preventDefault();
				var frame = wp.media( { title: 'اختر صورة', multiple: false, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					$input.val( attachment.id );
					$preview.empty();
					var thumb = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.url;
					$preview.append( $( '<img>' ).attr( 'src', thumb ) );
				} );
				frame.open();
			} );
		} );
	}

	function initHomeSectionsSortable() {
		var $list = $( '#noor-home-sections-sortable' );
		if ( ! $list.length ) {
			return;
		}
		var $order = $( '#noor-home-section-order' );

		function syncOrder() {
			var keys = $list.find( 'li' ).map( function () {
				return $( this ).data( 'key' );
			} ).get();
			$order.val( keys.join( ',' ) );
		}

		$list.sortable( {
			axis: 'y',
			handle: '.noor-drag-handle',
			update: syncOrder,
		} );
		syncOrder();
	}

	$( function () {
		initGalleryFields();
		initSingleImageFields();
		initHomeSectionsSortable();
	} );
} )( jQuery );
