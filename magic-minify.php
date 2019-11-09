<?php
/**
 * Magic Minify
 *
 * @package   Magic-Minify
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Magic Minify.
 * Plugin URI:
 * Description: Minify css and js files, make WordPress load one css and one js file.
 * Version:     0.0.1
 * Author:      Jascha Ehrenreich
 * Author URI:  http://github.com/wp-magic
 * Text Domain: magic-user-manage
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MAGIC_MINIFY_SLUG', 'magic_minify' );

define( 'MAGIC_MINIFY_CSS_FILE_NAME', 'magic.css' );

define( 'MAGIC_MINIFY_POST_SETTINGS_ACTION', 'magic-minify-post-settings' );

require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';

register_activation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules();
	}
);
