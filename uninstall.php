<?php

if (
	! defined( 'WP_UNINSTALL_PLUGIN' )
	||
	! WP_UNINSTALL_PLUGIN
	||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) )
) {
	status_header( 404 );
	exit;
}

static $options_name = 'wp-hypermd-settings';

// 删除选项
delete_option($options_name);

//开启自带可视化编辑器
add_filter( 'user_can_richedit', '__return_true' );