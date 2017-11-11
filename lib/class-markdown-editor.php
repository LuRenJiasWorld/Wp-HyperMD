<?php
// 如果这个文件被直接调用，则中止
if ( ! defined( 'WPINC' ) ) {
	 die;
}

/**
 * Main plugin class.
 */
class Meow_Editor {

	/**
	 * Default instance.
	 *
	 * @since 0.1.0
	 * @var string $instance.
	 */
	private static $instance;

	/**
	 * Sets up the Markdown editor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {

		// Add default post type support.
		add_post_type_support( 'post', 'wpcom-markdown' );

		// Load markdown editor.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'admin_footer', array( $this, 'init_editor' ) );

		// Remove quicktags buttons.
		add_filter( 'quicktags_settings', array( $this, 'quicktags_settings' ), 'content' );

		// Remove rich editing.
		add_filter( 'user_can_richedit', array( $this, 'disable_rich_editing' ) );

		// Load Jetpack Markdown module.
		$this->load_jetpack_markdown_module();

	}

	/**
	 * Get instance.
	 *
	 * @since 0.1.0
	 * @return object $instance Plugin instance.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __clone() {
		trigger_error( 'Clone is not allowed.', E_USER_ERROR );
	}

	/**
	 * Filter markdown post types.
	 *
	 * @since  0.1.0
	 * @return bool
	 */
	function get_post_type() {
		return get_current_screen()->post_type;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	function enqueue_scripts_styles() {

		// Only enqueue on specified post types.
		if ( ! post_type_supports( $this->get_post_type(), 'wpcom-markdown' ) ) {
			return;
		}
		wp_enqueue_style( 'codemirror-css', MEOW_EDITOR_URL . '/assets/codemirror/lib/codemirror.css', array(), MEOW_EDITOR_VERSION, 'all' );
		wp_enqueue_style( 'hypermd-css', MEOW_EDITOR_URL . '/assets/hypermd/mode/hypermd.css', array(), MEOW_EDITOR_VERSION, 'all' );
		wp_enqueue_style( 'hypermd-light-css', MEOW_EDITOR_URL . '/assets/hypermd/theme/hypermd-light.css', array(), MEOW_EDITOR_VERSION, 'all' );

		wp_enqueue_script( 'assets1', MEOW_EDITOR_URL . '/assets/codemirror/lib/codemirror.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets2', MEOW_EDITOR_URL . '/assets/codemirror/addon/mode/overlay.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets3', MEOW_EDITOR_URL . '/assets/codemirror/addon/edit/continuelist.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets4', MEOW_EDITOR_URL . '/assets/codemirror/mode/meta.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets5', MEOW_EDITOR_URL . '/assets/codemirror/mode/xml/xml.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets6', MEOW_EDITOR_URL . '/assets/codemirror/mode/markdown/markdown.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets7', MEOW_EDITOR_URL . '/assets/codemirror/mode/gfm/gfm.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets8', MEOW_EDITOR_URL . '/assets/hypermd/mode/hypermd.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets9', MEOW_EDITOR_URL . '/assets/hypermd/addon/click.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets10', MEOW_EDITOR_URL . '/assets/hypermd/addon/cursor-debounce.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets11', MEOW_EDITOR_URL . '/assets/hypermd/addon/fold.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets12', MEOW_EDITOR_URL . '/assets/hypermd/addon/hide-token.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets13', MEOW_EDITOR_URL . '/assets/hypermd/addon/hover.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets14', MEOW_EDITOR_URL . '/assets/hypermd/addon/paste.js', array(), MEOW_EDITOR_VERSION, false  );
		wp_enqueue_script( 'assets15', MEOW_EDITOR_URL . '/assets/hypermd/addon/readlink.js', array(), MEOW_EDITOR_VERSION, false  );
	}

	/**
	 * Load Jetpack Markdown Module.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	function load_jetpack_markdown_module() {

		// If the module is active, let's make this active for posting. Comments will still be optional.
		if ( class_exists( 'Easy_Markdown' ) ) {
			add_filter( 'pre_option_' . Easy_Markdown::POST_OPTION, '__return_true' );
		}
		add_action( 'admin_init', array( $this, 'jetpack_markdown_posting_always_on' ), 11 );
		add_filter( 'plugin_action_links_' . MEOW_EDITOR_NAME, array( $this, 'jetpack_markdown_settings_link' ) );

	}

	/**
	 * Set Jetpack posting to always on.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	function jetpack_markdown_posting_always_on() {
		if ( ! class_exists( 'Easy_Markdown' ) ) {
			return;
		}
		global $wp_settings_fields;
		if ( isset( $wp_settings_fields['writing']['default'][ Easy_Markdown::POST_OPTION ] ) ) {
			unset( $wp_settings_fields['writing']['default'][ Easy_Markdown::POST_OPTION ] );
		}
	}

	/**
     * 插件设置
	 * @param $actions
	 *
	 * @return array
	 */
	function jetpack_markdown_settings_link( $actions ) {
		return array_merge(
			array(
				'<a href="#" target="_blank" rel="nofollow">' . __( '文档', 'editormd' ) . '</a>',
				'<a href="#" target="_blank" rel="nofollow">' . __( '源码', 'editormd' ) . '</a>',
				'<a href="' . admin_url( 'options-discussion.php#' . Easy_Markdown::COMMENT_OPTION ) . '">' . __( '设置' ) . '</a>',
			),
			$actions
		);
	}

	/**
	 * Initialize editor.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	function init_editor() {

		// Only initialize on specified post types.
		if ( ! post_type_supports( $this->get_post_type(), 'wpcom-markdown' ) ) {
			return;
		}
		?>
		<script type="text/javascript">
            var myTextarea = document.getElementById('content')
            var editor = CodeMirror.fromTextArea(myTextarea, {
                lineNumbers: true,
                lineWrapping: true,
                theme: "hypermd-light",
                mode: "text/x-hypermd",

                gutters: [
                    "CodeMirror-linenumbers",
                    "HyperMD-goback"
                ],
                extraKeys: {
                    "Enter": "newlineAndIndentContinueMarkdownList"
                },

                hmdHideToken: "(profile-1)",
                hmdCursorDebounce: true,
                hmdAutoFold: 200,
                hmdPaste: true,
                hmdFoldMath: { interval: 200, preview: true }
            })

            editor.hmdHoverInit()       // tooltips on footnotes
            editor.hmdClickInit()       // click to follow links and footnotes
		</script>
		<?php
	}

	/**
	 * Quick tag settings.
	 *
	 * @since 0.1.0
	 * @param  array $qt_init Quick tag args.
	 * @return array
	 */
	function quicktags_settings( $qt_init ) {

		// Only remove buttons on specified post types.
		if ( ! post_type_supports( $this->get_post_type(), 'wpcom-markdown' ) ) {
			return $qt_init;
		}

		$qt_init['buttons'] = ' ';
		return $qt_init;
	}

	/**
	 * Disable rich editing.
	 *
	 * @since  0.1.1
	 * @param  array $default Default post types.
	 * @return array
	 */
	function disable_rich_editing( $default ) {

		if ( post_type_supports( $this->get_post_type(), 'wpcom-markdown' ) ) {
			return false;
		}

		return $default;
	}
}
