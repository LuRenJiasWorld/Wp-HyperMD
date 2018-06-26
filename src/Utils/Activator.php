<?php

namespace Utils;

class Activator {

	public static function activate() {

		// 开启自带可视化编辑器
		update_user_option( get_current_user_id(), 'rich_editing', 'false', true );

		// 初次载入插件写入默认数据 => 判断本地是否存在数据 不存在写入数据即可
		if ( get_option( 'hypermd_basics' ) == false ) {
			add_option( 'hypermd_basics', Activator::$defaultOptionsBasics, '', 'yes' );
		}

		if ( get_option( 'hypermd_syntax_highlighting' ) == false ) {
			add_option( 'hypermd_syntax_highlighting', Activator::$defaultOptionsSyntax, '', 'yes' );
		}

		if ( get_option( 'hypermd_editor_advanced' ) == false ) {
			add_option( 'hypermd_editor_advanced', Activator::$defaultOptionsAdvanced, '', 'yes' );
		}

	}

	public static $defaultOptionsBasics = array(
		'task_list'      => 'off',
		'imagepaste'     => 'off',
		'live_preview'   => 'off',
		'sync_scrolling' => 'off',
		'html_decode'    => 'off'
	);

	public static $defaultOptionsSyntax = array(
		'highlight_mode_auto'            => 'off',
		'line_numbers'                   => 'off',
		'show_language'                  => 'off',
		'copy_clipboard'                 => 'off',
		'highlight_library_style'        => 'default',
		'customize_my_style'             => 'nothing'
	);

	public static $defaultOptionsAdvanced = array(
		'hide_ads' => 'off'
	);
}
