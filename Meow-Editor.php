<?php
/**
 * Plugin Name: Meow Editor
 * Plugin URI:  https://github.com/JaxsonWang/Meow-Editor
 * Description: 轻量级的WordPress编辑器
 * Version:     0.1 Beta
 * Author:      淮城一只猫
 * Author URI:  https://iiong.com
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// 如果这个文件被直接调用，则中止
if ( ! defined( 'WPINC' ) ) {
	 die;
}

define( 'MEOW_EDITOR_VERSION', '0.1 Bete' );
define( 'MINIMUM_WP_VERSION', '4.5' );
define( 'MEOW_EDITOR_NAME', plugin_basename( __FILE__ ) );
define( 'MEOW_EDITOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEOW_EDITOR_URL', plugin_dir_url( __FILE__ ) );
define( 'MEOW_EDITOR_PATH', dirname( __FILE__ ) );

// 检查Jetpack模块是否启用
if ( ! class_exists( 'WPCom_Markdown' ) ) {
	// 加载Jetpack模块函数
	require_once MEOW_EDITOR_PATH . '/lib/class-easy-markdown.php';
}

// 检查Markdown编辑器模块是否启用
if ( ! function_exists( 'Meow_Editor' ) ) {
	// 加载Markdown编辑器函数
	require_once MEOW_EDITOR_PATH . '/lib/class-markdown-editor.php';
}

// 获取实体类
Meow_Editor::get_instance();