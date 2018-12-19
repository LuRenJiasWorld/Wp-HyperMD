<?php
/**
 * Plugin Name:       WP HyperMD
 * Plugin URI:        https://github.com/JaxsonWang/Wp-HyperMD
 * Description:       Perhaps this is the best and most perfect Markdown editor in WordPress
 * Version:           1.0.2
 * Author:            淮城一只猫
 * Author URI:        https://iiong.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       hypermd
 * Domain Path:       /languages
 */

namespace HyperMDRoot;

use HyperMD\Main;
use HyperMDUtils\Activator;
use HyperMDUtils\Deactivator;

define( 'CAT_HYPERMD_VER', '1.0.2' ); //版本说明
define( 'CAT_HYPERMD_URL', plugins_url( '', __FILE__ ) ); //插件资源路径
define( 'CAT_HYPERMD_PATH', dirname( __FILE__ ) ); //插件路径文件夹
define( 'CAT_HYPERMD_NAME', plugin_basename( __FILE__ ) ); //插件名称

// 自动载入文件
require_once CAT_HYPERMD_PATH . '/vendor/autoload.php';

/**
 * 插件激活期间运行的代码
 * includes/class-plugin-name-activator.php
 */
function activate_hypermd() {
	Activator::activate();
}

/**
 * 在插件停用期间运行的代码
 * includes/class-plugin-name-deactivator.php
 */
function deactivate_hypermd() {
    Deactivator::deactivate();
}

register_activation_hook( __FILE__, '\HyperMDRoot\activate_hypermd' );
register_deactivation_hook( __FILE__, '\HyperMDRoot\deactivate_hypermd' );

/**
 * 执行插件函数
 */
function run_hypermd() {
    $php_version = phpversion();
    $ver = '5.3.0';
    if (version_compare($php_version, $ver) < 0) {
        $a = __("WP HyperMD requires at least version 5.3.0 of PHP. You are running an older version: $php_version. Please upgrade PHP version!",'hypermd');
        wp_die( $a, 'WP HyperMD' );
    } else {
        $plugin = new Main();
        $plugin->run();
    }
}

/**
 * 开始执行插件
 */
run_hypermd();