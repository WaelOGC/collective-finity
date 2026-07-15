/**
 * Collective Finity — Customizer live preview for Theme Options.
 *
 * Updates CSS variables, body design attributes, and a few visibility toggles
 * via postMessage without a full refresh.
 */
( function ( $, api, config ) {
	'use strict';

	if ( ! config || ! config.optionKey ) {
		return;
	}

	var optionKey = config.optionKey;
	var root = document.documentElement;
	var loadedFonts = {};

	function settingId( key ) {
		return optionKey + '[' + key + ']';
	}

	function setVar( name, value ) {
		root.style.setProperty( name, value );
	}

	function hexToRgba( hex, alpha ) {
		hex = String( hex || '' ).replace( '#', '' );
		if ( hex.length === 3 ) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}
		if ( hex.length !== 6 ) {
			return 'rgba(255,183,0,' + alpha + ')';
		}
		var r = parseInt( hex.slice( 0, 2 ), 16 );
		var g = parseInt( hex.slice( 2, 4 ), 16 );
		var b = parseInt( hex.slice( 4, 6 ), 16 );
		return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
	}

	function adjustHexBrightness( hex, steps ) {
		hex = String( hex || '' ).replace( '#', '' );
		if ( hex.length === 3 ) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}
		if ( hex.length !== 6 ) {
			return '#' + hex;
		}
		var r = Math.max( 0, Math.min( 255, parseInt( hex.slice( 0, 2 ), 16 ) + steps ) );
		var g = Math.max( 0, Math.min( 255, parseInt( hex.slice( 2, 4 ), 16 ) + steps ) );
		var b = Math.max( 0, Math.min( 255, parseInt( hex.slice( 4, 6 ), 16 ) + steps ) );
		function toHex( n ) {
			var h = n.toString( 16 );
			return h.length === 1 ? '0' + h : h;
		}
		return '#' + toHex( r ) + toHex( g ) + toHex( b );
	}

	function applyPrimaryColor( hex ) {
		if ( ! hex ) {
			return;
		}
		setVar( '--primary-color', hex );
		setVar( '--cf-accent', hex );
		setVar( '--cf-accent-hover', adjustHexBrightness( hex, 40 ) );
		setVar( '--cf-accent-dim', hexToRgba( hex, 0.14 ) );
	}

	function applyAccentColor( hex ) {
		if ( ! hex ) {
			return;
		}
		setVar( '--secondary-color', hex );
		setVar( '--cf-bg-darkest', hex );
		setVar( '--cf-bg-dark', hex );
		setVar( '--cf-bg-panel', hex );
	}

	function ensureGoogleFont( key ) {
		var font = config.fonts && config.fonts[ key ];
		if ( ! font || ! font.google || loadedFonts[ key ] ) {
			return;
		}
		loadedFonts[ key ] = true;
		var link = document.createElement( 'link' );
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css2?family=' + font.google + '&display=swap';
		document.head.appendChild( link );
	}

	function applyFont( key, cssVar ) {
		var font = config.fonts && config.fonts[ key ];
		if ( ! font ) {
			return;
		}
		ensureGoogleFont( key );
		setVar( cssVar, font.stack );
	}

	function bind( key, handler ) {
		api( settingId( key ), function ( setting ) {
			setting.bind( handler );
		} );
	}

	// Colors
	bind( 'primary_color', applyPrimaryColor );
	bind( 'accent_color', applyAccentColor );
	bind( 'text_color', function ( v ) { setVar( '--cf-text', v ); } );
	bind( 'text_muted_color', function ( v ) { setVar( '--cf-text-2', v ); } );
	bind( 'card_bg_color', function ( v ) { setVar( '--cf-bg-card', v ); } );
	bind( 'border_color', function ( v ) { setVar( '--cf-border', v ); } );
	bind( 'link_color', function ( v ) { setVar( '--cf-link', v ); } );
	bind( 'link_hover_color', function ( v ) { setVar( '--cf-link-hover', v ); } );

	// Logo sizes
	bind( 'sidebar_logo_size', function ( v ) { setVar( '--cf-sidebar-logo-size', parseInt( v, 10 ) + 'px' ); } );
	bind( 'mobile_logo_size', function ( v ) { setVar( '--cf-mobile-logo-size', parseInt( v, 10 ) + 'px' ); } );

	// Typography
	bind( 'body_font', function ( v ) { applyFont( v, '--cf-body' ); } );
	bind( 'heading_font', function ( v ) { applyFont( v, '--cf-mono' ); } );
	bind( 'base_font_size', function ( v ) { setVar( '--cf-font-size-base', parseInt( v, 10 ) + 'px' ); } );
	bind( 'h1_font_size', function ( v ) { setVar( '--cf-h1-size', parseInt( v, 10 ) + 'px' ); } );
	bind( 'h2_font_size', function ( v ) { setVar( '--cf-h2-size', parseInt( v, 10 ) + 'px' ); } );
	bind( 'h3_font_size', function ( v ) { setVar( '--cf-h3-size', parseInt( v, 10 ) + 'px' ); } );
	bind( 'heading_font_weight', function ( v ) { setVar( '--cf-heading-weight', v ); } );
	bind( 'heading_letter_spacing', function ( v ) { setVar( '--cf-heading-tracking', parseFloat( v ) + 'em' ); } );
	bind( 'body_line_height', function ( v ) { setVar( '--cf-body-line-height', v ); } );

	// Buttons
	bind( 'button_radius', function ( v ) { setVar( '--cf-btn-radius', parseInt( v, 10 ) + 'px' ); } );
	bind( 'button_size', function ( v ) {
		var pad = ( config.buttonPadding && config.buttonPadding[ v ] ) || config.buttonPadding.regular;
		setVar( '--cf-btn-padding-y', pad.y + 'px' );
		setVar( '--cf-btn-padding-x', pad.x + 'px' );
	} );
	bind( 'button_hover_effect', function ( v ) {
		document.body.setAttribute( 'data-btn-hover', v || 'none' );
	} );

	// Cards
	bind( 'card_radius', function ( v ) { setVar( '--cf-card-radius', parseInt( v, 10 ) + 'px' ); } );
	bind( 'card_border_width', function ( v ) { setVar( '--cf-card-border-width', parseInt( v, 10 ) + 'px' ); } );
	bind( 'card_hover_effect', function ( v ) {
		document.body.setAttribute( 'data-card-hover', v || 'none' );
	} );
	bind( 'card_shadow', function ( v ) {
		var shadow = ( config.cardShadows && config.cardShadows[ v ] ) || config.cardShadows.soft;
		setVar( '--cf-card-shadow', shadow );
	} );

	// Effects & spacing
	bind( 'transition_speed', function ( v ) {
		var speed = ( config.transitions && config.transitions[ v ] ) || config.transitions.normal;
		setVar( '--cf-transition-speed', speed );
	} );
	bind( 'enable_glow_effects', function ( v ) {
		document.body.classList.toggle( 'cf-glow-disabled', ! v || v === '0' || v === false );
	} );
	bind( 'section_spacing', function ( v ) {
		var gap = ( config.sectionGaps && config.sectionGaps[ v ] ) || config.sectionGaps.default;
		setVar( '--cf-section-gap', gap );
	} );

	// Music player visibility
	bind( 'show_global_player', function ( v ) {
		var show = ! ( ! v || v === '0' || v === false );
		var player = document.getElementById( 'cf-global-audio-player' );
		if ( player ) {
			player.style.display = show ? '' : 'none';
		}
		document.body.style.paddingBottom = show ? '' : '0';
	} );
}( jQuery, wp.customize, window.cfCustomizerPreview || {} ) );
