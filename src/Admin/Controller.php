<?php

namespace Admin;

use App\WPComMarkdown;

class Controller {
	/**
	 * @var string 插件名称
	 */
	private $plugin_name;

	/**
	 * @var string 插件版本号
	 */
	private $version;

	/**
	 * @var string 翻译文本域
	 */
	private $text_domain;

	/**
	 * 筛选markdown post 类型
	 * @return string
	 */
	public function get_post_type() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return null;
		}

		return get_current_screen()->post_type;
	}

	/**
	 * Controller constructor 初始化类并设置其属性
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $ioption
	 */
	public function __construct( $plugin_name, $version, $text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->text_domain = $text_domain;
		$this->version     = $version;

		add_filter( 'quicktags_settings', array( $this, 'quicktags_settings' ), 'content' );

		add_action( 'admin_init', array( $this, 'editormd_markdown_posting_always_on' ), 11 );

		// 如果模块是激活状态保持文章/页面正常激活，评论Markdown是可选
		add_filter( 'pre_option_' . WPComMarkdown::POST_OPTION, '__return_true' );

	}

	/**
	 * 注册样式文件
	 */
	public function enqueue_styles() {
		//Style - Editor.md
		wp_enqueue_style( 'Editormd', WP_HYPERMD_URL . '/assets/Editormd/editormd.min.css', array(), '2.0.1', 'all' );
		//Style - Config
		wp_enqueue_style( 'Config', WP_HYPERMD_URL . '/assets/Config/editormd.min.css', array(), $this->version, 'all' );
	}

	/**
	 * 注册脚本文件
	 */
	public function enqueue_scripts() {

		if ( ! post_type_supports( $this->get_post_type(), WPComMarkdown::POST_TYPE_SUPPORT ) ) {
			return;
		}

        //JavaScript - Require
        wp_enqueue_script( 'Require', WP_HYPERMD_URL . '/assets/Require/require.js', array(), '2.3.5', true );

        //JavaScript - Patch Require
        wp_enqueue_script( 'Patch', WP_HYPERMD_URL . '/assets/Config/Patch.js', array( 'Require' ), $this->version, true );

		//JavaScript - Config
		wp_enqueue_script( 'HyperMD', WP_HYPERMD_URL . '/assets/Config/HyperMD.js', array( 'Patch' ), $this->version, true );


        wp_localize_script( 'HyperMD', 'HyperMD', array(
            'hypermdURL'       => WP_HYPERMD_URL,
        ) );
	}


	/**
	 * 将 Jetpack Markdown写作模式始终设置为开
	 */
	public function editormd_markdown_posting_always_on() {
		if ( ! class_exists( 'WPComMarkdown' ) ) {
			return;
		}
		global $wp_settings_fields;
		if ( isset( $wp_settings_fields['writing']['default'][ WPComMarkdown::POST_OPTION ] ) ) {
			unset( $wp_settings_fields['writing']['default'][ WPComMarkdown::POST_OPTION ] );
		}
	}

	/**
	 * 快速标记按钮
	 *
	 * @param $qt_init
	 *
	 * @return mixed
	 */
	public function quicktags_settings( $qt_init ) {

		// 仅删除指定 post 类型上的按钮
		if ( ! post_type_supports( $this->get_post_type(), WPComMarkdown::POST_TYPE_SUPPORT ) ) {
			return $qt_init;
		}

		$qt_init['buttons'] = '';

		return $qt_init;
	}

	/**
	 * 获取字段值
	 *
	 * @param string $option 字段名称
	 * @param string $section 字段名称分组
	 * @param string $default 没搜索到返回空
	 *
	 * @return mixed
	 */
	public function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

}
