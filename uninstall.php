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

static $options_name = array(
	'hypermd_basics',
	'hypermd_syntax_highlighting',
	'hypermd_editor_advanced'
);


// 删除选项
foreach($options_name as $optionName) {
	delete_option($optionName);
}

//开启自带可视化编辑器
add_filter( 'user_can_richedit', '__return_true' );