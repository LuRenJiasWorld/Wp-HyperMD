<?php

namespace HyperMDUtils;

class Activator {

	public static function activate() {

		// 开启自带可视化编辑器
		update_user_option( get_current_user_id(), 'rich_editing', 'false', true );

		// 初次载入插件写入默认数据 => 判断本地是否存在数据 不存在写入数据即可
		if ( get_option( 'wp-hypermd-settings' ) == false ) {
			add_option( 'wp-hypermd-settings', Activator::$defaultOptions, '', 'yes' );
		}

	}

	public static $defaultOptions = array(
		'highlight_mode_auto'     => 'off',
		'line_numbers'            => 'off',
		'show_language'           => 'off',
		'copy_clipboard'          => 'off',
		'highlight_library_style' => 'default',
		'customize_my_style'      => 'nothing',
		'hide_ads'                => 'off'
	);

}
