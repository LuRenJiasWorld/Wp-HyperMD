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
		'support_post_page'       => 'nothing',
		'enable_image_paste'      => 'disable',
		'hide_token'              => 'off',
		'enabled_hover'           => 'off',
		'enabled_click'           => 'off',
		'enabled_paste'           => 'off',
		'table_align'             => 'off',
		'highlight_mode_auto'     => 'off',
		'line_numbers'            => 'off',
		'show_language'           => 'off',
		'copy_clipboard'          => 'off',
		'highlight_library_style' => 'default',
		'customize_my_style'      => 'nothing',
		'enable_emoji'            => 'off',
		'enhance_emoji'           => 'off',
		'math_type'               => 'disable',
		'enable_mermaid'          => 'off',
		'mermaid_config'          => '{
    "theme": "dark",
    "logLevel": 5,
    "arrowMarkerAbsolute": false,
    "startOnLoad": true,
    "flowchart": {
        "htmlLabels": true,
        "curve": "linear"
    },
    "sequence": {
        "diagramMarginX": 50,
        "diagramMarginY": 10,
        "actorMargin": 50,
        "width": 150,
        "height": 65,
        "boxMargin": 10,
        "boxTextMargin": 5,
        "noteMargin": 10,
        "messageMargin": 35,
        "mirrorActors": true,
        "bottomMarginAdj": 1,
        "useMaxWidth": true
    },
    "gantt": {
        "titleTopMargin": 25,
        "barHeight": 20,
        "barGap": 4,
        "topPadding": 50,
        "leftPadding": 75,
        "gridLineStartPadding": 35,
        "fontSize": 11,
        "fontFamily": "\"Open-Sans\", \"sans-serif\"",
        "numberSectionStyles": 4,
        "axisFormat": "%Y-%m-%d"
    },
    "class": {},
    "git": {}
}'
	);

}
