<?php
/**
 * This file contains the autoloader for the plugin.
 *
 * @package Tax_Collector
 */

namespace Tax_Collector;

/**
 * Autoload classes.
 *
 * @param  string $cls Class name.
 */
function autoload( $cls ) {
	$cls = ltrim( $cls, '\\' );
	if ( strpos( $cls, __NAMESPACE__ . '\\' ) !== 0 ) {
		return;
	}

	$cls = strtolower( str_replace( [ __NAMESPACE__ . '\\', '_' ], [ '', '-' ], $cls ) );
	$dirs = explode( '\\', $cls );
	$cls = array_pop( $dirs );

	require_once( PATH . rtrim( '/lib/' . implode( '/', $dirs ), '/' ) . '/class-' . $cls . '.php' );
}
spl_autoload_register( __NAMESPACE__ . '\autoload' );
