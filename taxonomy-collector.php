<?php
/**
 * Plugin Name:     Tax Collector
 * Plugin URI:      https://github.com/alleyinteractive/tax-collector
 * Description:     Collect and organize Taxonomies
 * Author:          Matthew Boynes
 * Author URI:      https://www.alleyinteractive.com/
 * Text Domain:     tax-collector
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tax Collector
 */

namespace Tax_Collector;

define( __NAMESPACE__ . '\PATH', __DIR__ );
define( __NAMESPACE__ . '\URL', trailingslashit( plugins_url( '', __FILE__ ) ) );

// Custom autoloader
require_once( PATH . '/lib/autoload.php' );

// Singleton trait
require_once( PATH . '/lib/trait-singleton.php' );

// Load the main plugin file.
add_action( 'after_setup_theme', [ __NAMESPACE__ . '\Main', 'instance' ] );

// Load the main plugin file.
add_action( 'fm_post', [ __NAMESPACE__ . '\Fields', 'meta_box' ] );
