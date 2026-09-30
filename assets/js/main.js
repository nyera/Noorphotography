/**
 * Noor theme frontend behaviour: header scroll state, mobile menu, search
 * overlay, reveal-on-scroll, animated counters, accordions, dark mode toggle,
 * portfolio filter/layout switching, and the cookie notice.
 * Vanilla JS — no jQuery, no framework runtime.
 */
( function () {
	'use strict';

	/* ---------- Header scroll state ---------- */
	var header = document.querySelector( '.site-header' );
	if ( header ) {
		var onScroll = function () {
			var scrolled = window.scrollY > 40;
			if ( header.dataset.transparent === '1' ) {
				header.classList.toggle( 'is-transparent', ! scrolled );
				header.classList.toggle( 'is-solid', scrolled );
			}
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	/* ---------- Mobile menu ---------- */
	var menuToggle = document.querySelector( '.menu-toggle' );
	var mobileMenu = document.querySelector( '.mobile-menu' );
	var menuClose = document.querySelector( '.mobile-menu__close' );
	function setMenu( open ) {
		if ( ! mobileMenu ) return;
		mobileMenu.classList.toggle( 'is-open', open );
		menuToggle && menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		document.body.style.overflow = open ? 'hidden' : '';
	}
	menuToggle && menuToggle.addEventListener( 'click', function () { setMenu( true ); } );
	menuClose && menuClose.addEventListener( 'click', function () { setMenu( false ); } );

	/* ---------- Search overlay ---------- */
	var searchTrigger = document.querySelector( '[data-search-open]' );
	var searchOverlay = document.querySelector( '.search-overlay' );
	var searchClose = document.querySelector( '.search-overlay__close' );
	var searchInput = document.querySelector( '#site-search' );
	var searchResults = document.querySelector( '.search-overlay__results' );
	var searchSuggestions = document.querySelector( '.search-overlay__suggestions' );

	function setSearch( open ) {
		if ( ! searchOverlay ) return;
		searchOverlay.classList.toggle( 'is-open', open );
		document.body.style.overflow = open ? 'hidden' : '';
		if ( open && searchInput ) {
			setMenu( false );
			searchInput.focus();
		}
	}
	searchTrigger && searchTrigger.addEventListener( 'click', function () { setSearch( true ); } );
	searchClose && searchClose.addEventListener( 'click', function () { setSearch( false ); } );

	function renderResults( query ) {
		if ( ! searchResults || typeof noorData === 'undefined' ) return;
		var q = query.trim();
		if ( ! q ) {
			searchResults.innerHTML = '';
			searchSuggestions && ( searchSuggestions.style.display = '' );
			return;
		}
		searchSuggestions && ( searchSuggestions.style.display = 'none' );
		var matches = ( noorData.search || [] ).filter( function ( item ) {
			return item.title.indexOf( q ) !== -1;
		} );
		if ( ! matches.length ) {
			searchResults.innerHTML = '<div class="empty-state"><h3 class="t-h3">' + noorData.i18n.noResults + '</h3></div>';
			return;
		}
		var html = '<ul>';
		matches.forEach( function ( item ) {
			html += '<li><a href="' + item.url + '"><span class="title">' + item.title + '</span><span class="t-caption">' + item.type + '</span></a></li>';
		} );
		html += '</ul>';
		searchResults.innerHTML = html;
	}
	searchInput && searchInput.addEventListener( 'input', function ( e ) { renderResults( e.target.value ); } );
	document.querySelectorAll( '[data-search-suggestion]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			searchInput.value = btn.dataset.searchSuggestion;
			renderResults( searchInput.value );
			searchInput.focus();
		} );
	} );

	/* ---------- Escape key closes overlays ---------- */
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			setMenu( false );
			setSearch( false );
		}
	} );

	/* ---------- Reveal on scroll ---------- */
	var revealEls = document.querySelectorAll( '.reveal' );
	if ( 'IntersectionObserver' in window && revealEls.length ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'reveal-in' );
					io.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.15 } );
		revealEls.forEach( function ( el ) { io.observe( el ); } );
	} else {
		revealEls.forEach( function ( el ) { el.classList.add( 'reveal-in' ); } );
	}

	/* ---------- Animated counters ---------- */
	var counters = document.querySelectorAll( '[data-counter-to]' );
	if ( counters.length ) {
		var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var animateCounter = function ( el ) {
			var to = parseInt( el.dataset.counterTo, 10 ) || 0;
			var suffix = el.dataset.counterSuffix || '';
			if ( reduceMotion ) {
				el.textContent = to.toLocaleString( 'ar-EG' ) + suffix;
				return;
			}
			var start = performance.now();
			var dur = 1600;
			function tick( now ) {
				var p = Math.min( 1, ( now - start ) / dur );
				var eased = 1 - Math.pow( 1 - p, 3 );
				el.textContent = Math.round( to * eased ).toLocaleString( 'ar-EG' ) + suffix;
				if ( p < 1 ) requestAnimationFrame( tick );
			}
			requestAnimationFrame( tick );
		};
		if ( 'IntersectionObserver' in window ) {
			var cio = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						animateCounter( entry.target );
						cio.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.4 } );
			counters.forEach( function ( el ) { cio.observe( el ); } );
		} else {
			counters.forEach( animateCounter );
		}
	}

	/* ---------- Hero "scroll down" button ---------- */
	var scrollDownBtn = document.querySelector( '.js-scroll-down' );
	if ( scrollDownBtn ) {
		scrollDownBtn.addEventListener( 'click', function () {
			var heroSection = scrollDownBtn.closest( 'section' );
			var target = heroSection ? heroSection.nextElementSibling : null;
			if ( target ) {
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			} else {
				window.scrollTo( { top: window.innerHeight, behavior: 'smooth' } );
			}
		} );
	}

	/* ---------- Accordions ---------- */
	document.querySelectorAll( '.noor-accordion__trigger' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var panel = btn.closest( '.noor-accordion__item' ).querySelector( '.noor-accordion__panel' );
			var expanded = btn.getAttribute( 'aria-expanded' ) === 'true';
			btn.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
			if ( panel ) panel.hidden = expanded;
		} );
	} );

	/* ---------- Dark mode toggle ---------- */
	var themeToggle = document.querySelector( '[data-theme-toggle]' );
	if ( themeToggle ) {
		var stored = null;
		try { stored = window.localStorage.getItem( 'noor-theme' ); } catch ( err ) {}
		if ( stored === 'dark' ) {
			document.documentElement.classList.add( 'dark' );
			themeToggle.textContent = themeToggle.dataset.labelLight || themeToggle.textContent;
		}
		themeToggle.addEventListener( 'click', function () {
			var dark = document.documentElement.classList.toggle( 'dark' );
			themeToggle.textContent = dark ? ( themeToggle.dataset.labelLight || '' ) : ( themeToggle.dataset.labelDark || '' );
			try { window.localStorage.setItem( 'noor-theme', dark ? 'dark' : 'light' ); } catch ( err ) {}
		} );
	}

	/* ---------- Portfolio filter (client-side over rendered cards) ---------- */
	var filterBar = document.querySelector( '[data-filter-bar]' );
	if ( filterBar ) {
		var cards = document.querySelectorAll( '[data-project-category]' );
		filterBar.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button[data-filter]' );
			if ( ! btn ) return;
			filterBar.querySelectorAll( 'button' ).forEach( function ( b ) { b.classList.remove( 'is-active' ); b.setAttribute( 'aria-pressed', 'false' ); } );
			btn.classList.add( 'is-active' );
			btn.setAttribute( 'aria-pressed', 'true' );
			var value = btn.dataset.filter;
			cards.forEach( function ( card ) {
				var show = value === 'all' || card.dataset.projectCategory === value;
				card.style.display = show ? '' : 'none';
			} );
		} );
	}

	/* ---------- Portfolio layout switch ---------- */
	var layoutSwitch = document.querySelector( '[data-layout-switch]' );
	if ( layoutSwitch ) {
		var grid = document.querySelector( '[data-project-grid]' );
		layoutSwitch.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button[data-layout]' );
			if ( ! btn || ! grid ) return;
			layoutSwitch.querySelectorAll( 'button' ).forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
			btn.classList.add( 'is-active' );
			grid.classList.remove( 'masonry', 'grid-cards', 'grid-full' );
			grid.classList.add( btn.dataset.layout );
		} );
	}

	/* ---------- Cookie notice ---------- */
	var cookieNotice = document.querySelector( '.cookie-notice' );
	if ( cookieNotice ) {
		var dismissed = false;
		try { dismissed = window.localStorage.getItem( 'noor-cookie-dismissed' ) === '1'; } catch ( err ) {}
		if ( ! dismissed ) {
			window.setTimeout( function () { cookieNotice.classList.add( 'is-visible' ); }, 1200 );
		}
		cookieNotice.querySelectorAll( '[data-cookie-dismiss]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				cookieNotice.classList.remove( 'is-visible' );
				try { window.localStorage.setItem( 'noor-cookie-dismissed', '1' ); } catch ( err ) {}
			} );
		} );
	}
} )();
