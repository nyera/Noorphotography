<?php
/**
 * Custom nav walker for the fullscreen mobile menu (adds the "01" index
 * markers seen in the original design).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Noor_Mobile_Menu_Walker extends Walker_Nav_Menu {
	private $index = 0;

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		++$this->index;
		$classes = $item->current ? ' class="current-menu-item"' : '';
		$output .= '<li' . $classes . '><a href="' . esc_url( $item->url ) . '"><span class="index">' . esc_html( str_pad( $this->index, 2, '0', STR_PAD_LEFT ) ) . '</span>' . esc_html( $item->title ) . '</a></li>';
	}
}
