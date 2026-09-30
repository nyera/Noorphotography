/**
 * Live-updates the #noor-customizer-vars <style> tag (and the Google Fonts
 * <link>) inside the Customizer preview iframe as controls change — no
 * page reload. Mirrors the PHP defaults/logic in inc/customizer.php so the
 * preview and the saved result never disagree.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.customize || typeof noorCustomizerData === 'undefined' ) {
		return;
	}

	var state = {
		colors: {},
		heading: 'amiri',
		body: 'ibm_plex',
		size: 'medium',
	};

	function currentStyleTag() {
		var tag = document.getElementById( 'noor-customizer-vars' );
		if ( ! tag ) {
			tag = document.createElement( 'style' );
			tag.id = 'noor-customizer-vars';
			document.head.appendChild( tag );
		}
		return tag;
	}

	function render() {
		// Colors: light-mode only (see inc/customizer.php for why :not(.dark)
		// matters — otherwise this fights the theme's own dark palette and
		// leaves dark-mode text unreadable against a light background).
		var lines = [ ':root:not(.dark) {' ];
		Object.keys( state.colors ).forEach( function ( key ) {
			var cssVar = noorCustomizerData.colorVars[ key ];
			if ( cssVar && state.colors[ key ] ) {
				lines.push( '\t' + cssVar + ': ' + state.colors[ key ] + ';' );
			}
		} );
		lines.push( '}' );

		// Typography: applies in both modes.
		lines.push( ':root {' );
		if ( noorCustomizerData.headingCss[ state.heading ] ) {
			lines.push( '\t--font-display: ' + noorCustomizerData.headingCss[ state.heading ] + ';' );
		}
		if ( noorCustomizerData.bodyCss[ state.body ] ) {
			lines.push( '\t--font-body: ' + noorCustomizerData.bodyCss[ state.body ] + ';' );
		}
		lines.push( '}' );
		if ( noorCustomizerData.fontSizes[ state.size ] ) {
			lines.push( 'body { font-size: ' + noorCustomizerData.fontSizes[ state.size ] + '; }' );
		}
		currentStyleTag().textContent = lines.join( '\n' );
	}

	function ensureFontsLoaded() {
		var families = [];
		if ( noorCustomizerData.headingUrl[ state.heading ] ) {
			families.push( noorCustomizerData.headingUrl[ state.heading ] );
		}
		if ( noorCustomizerData.bodyUrl[ state.body ] && state.body !== state.heading ) {
			families.push( noorCustomizerData.bodyUrl[ state.body ] );
		}
		if ( ! families.length ) {
			return;
		}
		// Only the space in a multi-word family name needs encoding (as "+")
		// — ":", "@", ";" must stay literal, they're part of Google's own
		// family:axis@values syntax. encodeURIComponent() would escape those
		// too and break the request (see inc/customizer.php for the same fix).
		var safeFamilies = families.map( function ( f ) { return f.replace( / /g, '+' ); } );
		var url = 'https://fonts.googleapis.com/css2?family=' + safeFamilies.join( '&family=' ) + '&display=swap';
		var existing = document.getElementById( 'noor-preview-fonts' );
		if ( existing && existing.href === url ) {
			return;
		}
		if ( existing ) {
			existing.remove();
		}
		var link = document.createElement( 'link' );
		link.id = 'noor-preview-fonts';
		link.rel = 'stylesheet';
		link.href = url;
		document.head.appendChild( link );
	}

	Object.keys( noorCustomizerData.colorVars ).forEach( function ( key ) {
		wp.customize( key, function ( value ) {
			value.bind( function ( newVal ) {
				state.colors[ key ] = newVal;
				render();
			} );
		} );
	} );

	wp.customize( 'noor_font_heading', function ( value ) {
		value.bind( function ( newVal ) {
			state.heading = newVal;
			ensureFontsLoaded();
			render();
		} );
	} );

	wp.customize( 'noor_font_body', function ( value ) {
		value.bind( function ( newVal ) {
			state.body = newVal;
			ensureFontsLoaded();
			render();
		} );
	} );

	wp.customize( 'noor_font_size', function ( value ) {
		value.bind( function ( newVal ) {
			state.size = newVal;
			render();
		} );
	} );
} )( window.wp );
