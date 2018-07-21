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
 * Description: Minify css and js files, make wordpress load one css and one js file.
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

define( 'MAGIC_MINIFY_SLUG', 'magic_minify');

add_action( 'wp_print_styles', function () {
	global $wp_styles;

	// arrange the queue based dependencies
	$wp_styles->all_deps( $wp_styles->queue );

	$handles = $wp_styles->to_do;


  $now = strtotime( 'now' );
  $upload_dir = wp_upload_dir();
  $file_name = 'style.css';

  // write last compilation to database for quicker lookup
  $option_name = MAGIC_MINIFY_SLUG . '_last_compilation';
  $last_compilation = magic_get_option( $option_name, 0 );

  $should_compile = false;

  foreach ( $handles as $handle ) {
    /*
      Clean up the url
      style.min.css?v=4.6
      to
      style.min.css
    */
    $src = strtok( $wp_styles->registered[$handle]->src, '?' );

    if ( strpos( $src, 'http' ) !== false ) {
      $site_url = site_url();

      // If the css file is local, change full url into relative path
      if ( strpos( $src, $site_url ) !== false ) {
        $src = str_replace( $site_url, '', $src );
      }
    }

    // remove preceding slash for file_get_contents
    $css_file_path = ltrim( $src, '/' );

    $last_changed = filemtime( $css_file_path );
    if ( $last_changed > $last_compilation ) {
      $should_compile = true;
    }

    $files[] = $css_file_path;

    wp_deregister_style( $handle );
  }


  if ( $should_compile ) {
    $css_code = '';

  	foreach ( $files as $handle ) {
      if ( file_exists( $handle ) ) {
  			$css_code .=  file_get_contents( $handle );
  		}
  	}

    magic_set_option( $option_name, $now );

  	// write the merged styles to uploads/style.css
    $merged_file_location = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $file_name;
  	file_put_contents ( $merged_file_location , $css_code );
  }

	wp_enqueue_style(
    'magic_style',
    $upload_dir['baseurl'] . '/' . $file_name,
    null,
    $last_compilation
  );
} );
