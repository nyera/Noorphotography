/**
 * Fullscreen gallery lightbox for single-portfolio pages.
 */
( function () {
	'use strict';

	var lightbox = document.querySelector( '.noor-lightbox' );
	if ( ! lightbox ) return;

	var stageImg = lightbox.querySelector( '.noor-lightbox__stage img' );
	var counter = lightbox.querySelector( '.noor-lightbox__counter' );
	var caption = lightbox.querySelector( '.noor-lightbox__caption' );
	var closeBtn = lightbox.querySelector( '.noor-lightbox__close' );
	var prevBtn = lightbox.querySelector( '[data-lightbox-prev]' );
	var nextBtn = lightbox.querySelector( '[data-lightbox-next]' );
	var triggers = document.querySelectorAll( '[data-lightbox-open]' );

	var images = [];
	triggers.forEach( function ( t ) { images.push( { src: t.dataset.lightboxOpen, alt: t.dataset.lightboxAlt || '' } ); } );

	var index = 0;

	function render() {
		if ( ! images[ index ] ) return;
		stageImg.src = images[ index ].src;
		stageImg.alt = images[ index ].alt;
		if ( counter ) counter.textContent = ( index + 1 ) + ' / ' + images.length;
	}

	function open( i ) {
		index = i;
		render();
		lightbox.classList.add( 'is-open' );
		document.body.style.overflow = 'hidden';
	}
	function close() {
		lightbox.classList.remove( 'is-open' );
		document.body.style.overflow = '';
	}
	function next() { index = ( index + 1 ) % images.length; render(); }
	function prev() { index = ( index - 1 + images.length ) % images.length; render(); }

	triggers.forEach( function ( t, i ) {
		t.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			open( i );
		} );
	} );
	closeBtn && closeBtn.addEventListener( 'click', close );
	nextBtn && nextBtn.addEventListener( 'click', next );
	prevBtn && prevBtn.addEventListener( 'click', prev );

	document.addEventListener( 'keydown', function ( e ) {
		if ( ! lightbox.classList.contains( 'is-open' ) ) return;
		if ( e.key === 'Escape' ) close();
		if ( e.key === 'ArrowLeft' ) next();
		if ( e.key === 'ArrowRight' ) prev();
	} );
} )();
