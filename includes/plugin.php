<?php
/**
 * Appointment Gdpr Compliance
 *
 * @package   Magic_Gdpr
 * @license   GPL-2.0+
 */

if ( is_admin() ) {
  require_once 'admin/dashboard.php';
}

add_action( 'wp_print_styles', function () {
  $admin_off = magic_get_option( MAGIC_MINIFY_SLUG . '_admin_off', false);
  $minify_css = magic_get_option( MAGIC_MINIFY_SLUG . '_css', false);

  if (is_admin() && $admin_off) {
    return;
  }

  if ($minify_css) {
    global $wp_styles;

    // arrange the queue based dependencies
    $wp_styles->all_deps( $wp_styles->queue );

    $handles = $wp_styles->to_do;

    $now = strtotime( 'now' );
    $upload_dir = wp_upload_dir();
    $file_name = MAGIC_MINIFY_CSS_FILE_NAME;

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

    if ( true || $should_compile ) {
      $css_code = '';

      foreach ( $files as $handle ) {
        if ( file_exists( $handle ) ) {
          $css_code .=  file_get_contents( $handle );
        }
      }

      magic_set_option( $option_name, $now );

      // "minify" the css output
      // replace tab with spaces
      $css_code = str_replace(  "\t",    "  ", $css_code );
      // replace newlines with a single newline
      $css_code = preg_replace( "#\R+#", "\n", $css_code );
      // merge classes of one css declaration into one line
      $css_code = str_replace(  ",\n",   ",", $css_code );
      // remove spaces around various special chars, but NOT }
      $css_code = preg_replace("/\s*([,;:+={])\s*/", "$1", $css_code);
      // remove newlines before } but not after it.
      // this keeps every declaration on one line
      $css_code = str_replace(  "\n}",   "}", $css_code );

      // write the merged styles to uploads/$file_name
      $merged_file_location = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $file_name;
      file_put_contents ( $merged_file_location , $css_code );
    }

    wp_enqueue_style(
      'magic_style',
      $upload_dir['baseurl'] . '/' . $file_name,
      null,
      $last_compilation
    );
  }
} );

add_action('init', function () {
  $domain = MAGIC_MINIFY_SLUG;
  load_plugin_textdomain( $domain, FALSE, plugin_dir_path( __FILE__ ) . 'languages' );
} );