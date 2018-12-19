<?php

namespace HyperMDAdmin;

use HyperMDApp\WPComMarkdown;

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
	 * @var string 插件标签
	 */
	private $plugin_slug;

	/**
	 * Controller constructor 初始化类并设置其属性
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $ioption
	 */
	public function __construct( $plugin_name, $version, $text_domain, $plugin_slug ) {

		$this->plugin_name = $plugin_name;
		$this->text_domain = $text_domain;
		$this->version     = $version;
		$this->plugin_slug = $plugin_slug;

		add_filter( 'quicktags_settings', array( $this, 'quicktags_settings' ) );

		add_action( 'admin_init', array( $this, 'hypermd_markdown_posting_always_on' ), 11 );

		// 如果模块是激活状态保持文章/页面正常激活，评论Markdown是可选
		add_filter( 'pre_option_' . WPComMarkdown::POST_OPTION, '__return_true' );

		if ( $this->get_opt( 'math_type' ) == 'mathjax' ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'mathjax_admin_print_footer_scripts' ) );
		}

        // WordPress 5.0 Gutenberg Editor
        if ( $GLOBALS['wp_version'] >= '5.0' ) {
            add_filter( 'gutenberg_can_edit_post_type', array($this, 'disable_gutenberg'), 10, 2 );
            add_filter( 'use_block_editor_for_post_type', array($this,'disable_gutenberg'), 10, 2 );
        }
	}

    /**
     * 禁用Gutenberg编辑器
     * @param $can_edit
     * @param $post_type
     *
     * @return bool
     */
    public function disable_gutenberg( $can_edit, $post_type ) {
        $can_edit = false;
        return $can_edit;
    }

	/**
	 * 注册脚本文件
	 */
	public function enqueue_scripts() {

		if ( $this->get_opt( 'enable_mermaid' ) == 'on' ) {
			//JavaScript - Mermaid
			wp_enqueue_script('Mermaid', '//cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js', array(), $this->version, true);
			//wp_enqueue_script( 'Mermaid', CAT_HYPERMD_URL . '/assets/Mermaid/dist/mermaid.min.js', array(), $this->version, true );
		}

		//JavaScript - Require
		wp_enqueue_script('Require', '//cdn.jsdelivr.net/npm/requirejs/require.min.js', array(), $this->version, true);
		//wp_enqueue_script( 'Require', CAT_HYPERMD_URL . '/assets/Requirejs/require.min.js', array(), $this->version, true );

		//JavaScript - Patch Require
		wp_enqueue_script( 'Patch', CAT_HYPERMD_URL . '/assets/Config/Patch.min.js', array( 'Require' ), $this->version, true );

		//JavaScript - Config
		wp_enqueue_script( 'HyperMD', CAT_HYPERMD_URL . '/assets/Config/HyperMD.min.js', array( 'Patch' ), $this->version, true );


		wp_localize_script( 'HyperMD', 'WPHyperMD', array(
			'hypermdURL'   => CAT_HYPERMD_URL,
			'imagePaste'   => $this->get_opt( 'enable_image_paste' ), //图片粘贴
			'isHideToken'  => $this->get_opt( 'hide_token' ), //自动隐藏标签
			'isHover'      => $this->get_opt( 'enabled_hover' ), //信息
			'isClick'      => $this->get_opt( 'enabled_click' ), //跳转
			'isPaste'      => $this->get_opt( 'enabled_paste' ), //跳转
			'isTableAlign' => $this->get_opt( 'table_align' ), //跳转
			'isEmoji'      => $this->get_opt( 'enable_emoji' ), //Emoji
			'mathType'     => $this->get_opt( 'math_type' ), //数学类型
			'isMermaid'    => $this->get_opt( 'enable_mermaid' ), //Mermaid
		) );
	}


	/**
	 * 将 Jetpack Markdown写作模式始终设置为开
	 */
	public function hypermd_markdown_posting_always_on() {
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
	public function quicktags_settings() {

		$qt_init['buttons'] = 'strong,em,link,block,del,img,ul,ol,li,code,more,spell,close,fullscreen';

		return $qt_init;
	}

	/**
	 * 获取选项值
	 *
	 * @param string $option_data 选项值
	 *
	 * @return mixed
	 */
	public function get_opt( $option_data ) {
		$options = get_option( $this->plugin_slug );
		$val     = ! empty( $options[ $option_data ] ) ? $options[ $option_data ] : 'off';

		return $val;
	}

	/**
	 * MathJax配置
	 */
	public function mathjax_admin_print_footer_scripts() {
		?>
        <script type="text/x-mathjax-config">
			MathJax.Hub.Config({
			    jax: ["input/TeX", "output/HTML-CSS","output/NativeMML","output/SVG"],
			    extensions: ["MathMenu.js","MathZoom.js", "AssistiveMML.js", "a11y/accessibility-menu.js"],
			    TeX: {
			        extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"]
			    }
			});


        </script>
		<?php
	}

}
