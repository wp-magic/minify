<?php
/**
 * Add Admin Menu to WordPress
 *
 * @package MagicMinify
 * @since 0.0.1
 */

add_action(
	'admin_menu',
	function () {
		$title = 'Magic Minify Settings';

		$settings = array(
			array(
				'name'    => 'admin_off',
				'type'    => 'checkbox',
				'default' => false,
				'label'   => 'disable minification for administrator accounts',
			),
			array(
				'name'    => 'css',
				'type'    => 'checkbox',
				'default' => false,
				'label'   => 'minify css',
			),
		);

		magic_dashboard_add_submenu_page(
			array(
				'link'     => 'Minify',
				'slug'     => MAGIC_MINIFY_SLUG,
				'title'    => $title,
				'settings' => $settings,
				'action'   => MAGIC_MINIFY_POST_SETTINGS_ACTION,
			)
		);
	},
	2
);
