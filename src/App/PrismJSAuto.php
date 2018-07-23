<?php

namespace HyperMDApp;

class PrismJSAuto {

	private $plugin_slug;

	public function __construct( $plugin_slug ) {

		$this->plugin_slug = $plugin_slug;

		add_action( 'wp', array( $this, 'prism_styles_scripts' ) );

		if( !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
			add_action( 'wp_print_footer_scripts', array( $this, 'prism_wp_footer_scripts') );
		}
	}

	public function prism_styles_scripts() {
		$prism_base_url = '//cdn.jsdelivr.net/npm/prismjs@1.14.0'; //资源载入地址
		$prism_theme    = $this->get_opt( 'highlight_library_style' ); //语法高亮风格
		$line_numbers   = $this->get_opt( 'line_numbers' ) == 'on' ? true : false; //行号显示
		$show_language  = $this->get_opt( 'show_language' ) == 'on' ? true : false; //显示语言
		$copy_clipboard = $this->get_opt( 'copy_clipboard' ) == 'on' ? true : false; //粘贴
		if($show_language == true) {
			$toolbar = true;
		}
		$prism_plugins  = array(
			'autoloader' => array(
				'js'  => true,
				'css' => false
			),
			'toolbar' => array(
				'js'  => $toolbar,
				'css' => $toolbar
			),
			'line-numbers' => array(
				'css' => $line_numbers,
				'js'  => $line_numbers
			),
			'show-language' => array(
				'js'  => $show_language,
				'css' => false
			),
			'copy-to-clipboard' => array(
				'js'  => $copy_clipboard,
				'css' => false
			),
		);
		$prism_styles   = array();
		$prism_scripts  = array();

		$prism_scripts['prism-core-js'] = $prism_base_url . '/components/prism-core.min.js';
		//$prism_scripts['prism-language-default-js'] = $prism_base_url . '/prism.min.js';

		if ( empty( $prism_theme ) || $prism_theme == 'default' ) {
			$prism_styles['prism-theme-default'] = $prism_base_url . '/themes/prism.min.css';
		} else if ( $prism_theme == 'customize' ) {
			$prism_styles['prism-theme-style'] = $this->get_opt( 'customize_my_style' ); //自定义风格
		} else {
			$prism_styles['prism-theme-style'] = $prism_base_url . "/themes/prism-{$prism_theme}.min.css";
		}
		foreach ( $prism_plugins as $prism_plugin => $prism_plugin_config ) {
			if ( $prism_plugin_config['css'] === true ) {
				$prism_styles["prism-plugin-{$prism_plugin}"] = $prism_base_url . "/plugins/{$prism_plugin}/prism-{$prism_plugin}.min.css";
			}
			if ( $prism_plugin_config['js'] === true ) {
				$prism_scripts["prism-plugin-{$prism_plugin}"] = $prism_base_url . "/plugins/{$prism_plugin}/prism-{$prism_plugin}.min.js";
			}
		}

		/**
		 * 代码粘贴代码增强
		 * 引入clipboard
		 */
		if ( $copy_clipboard ) {
			wp_enqueue_script('copy-clipboard', '//cdn.jsdelivr.net/npm/clipboard@2.0.1/dist/clipboard.min.js', array(), '2.0.1', true);
		}

		foreach ( $prism_styles as $name => $prism_style ) {
			wp_enqueue_style( $name, $prism_style, array(), '1.14.0', 'all' );
		}

		foreach ( $prism_scripts as $name => $prism_script ) {
			wp_enqueue_script( $name, $prism_script, array(), '1.14.0', true );
		}
	}

	public function prism_wp_footer_scripts() {
		?>
		<script type="text/javascript">
			Prism.plugins.autoloader.languages_path = "<?php print '//cdn.jsdelivr.net/npm/prismjs@1.14.0' ?>/components/"
		</script>
		<?php
	}

	/**
	 * 获取选项值
	 * @param $data
	 *
	 * @return mixed
	 */
	public function get_opt($data) {
		$options = get_option( $this->plugin_slug );
		return $options[$data];
	}
}