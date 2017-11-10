<?php
/**
 * Plugin Name: Meow Editor
 * Plugin URI:  https://github.com/JaxsonWang/Meow-Editor
 * Description: 轻量级的WordPress编辑器
 * Version:     0.1 Beta
 * Author:      淮城一只猫
 * Author URI:  https://iiong.com
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	 die;
}

// Define constants.
define( 'PLUGIN_VERSION', '0.1.3' );
define( 'MINIMUM_WP_VERSION', '4.8' );
define( 'PLUGIN_NAME', plugin_basename( __FILE__ ) );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check if Jetpack module is enabled.
if ( ! class_exists( 'WPCom_Markdown' ) ) {
	include_once PLUGIN_DIR . 'includes/class-easy-markdown.php';
}

// Load Markdown class.
include_once PLUGIN_DIR . 'includes/class-markdown-editor.php';

// Get class instance.
Markdown_Editor::get_instance();

remove_theme_support( 'page', 'wpcom-markdown' );