<?php

add_action( 'admin_menu', function () {
  $title = 'Magic Minify Settings';

  $settings = array(
    array (
      'name' => 'admin_off',
      'type' => 'checkbox',
      'default' => false,
      'label' => 'disable minification for administrator accounts',
    ),
    array (
      'name' => 'css',
      'type' => 'checkbox',
      'default' => false,
      'label' => 'minify css',
    ),
  );

  magic_dashboard_add_submenu_page( array (
    'link' => 'Minify',
    'slug' => MAGIC_MINIFY_SLUG,
    'title' => $title,
    'settings' => $settings,
   ) );
}, 2 );
