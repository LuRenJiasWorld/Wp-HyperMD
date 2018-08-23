<?php

namespace HyperMDUtils;

class Settings {
	private $plugin_name;
	private $plugin_slug;
	private $verison;
	private $text_domain;
	private $options;
	private $settings;

	public function __construct( $plugin_name, $plugin_slug, $version, $text_domain ) {
		$this->plugin_slug = $plugin_slug;
		$this->plugin_name = $plugin_name;
		$this->verison     = $version;
		$this->text_domain = $text_domain;

		// 初始化设置
		add_action( 'admin_init', array( $this, 'init' ) );

		// 将设置页面添加到菜单
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		add_action( 'admin_enqueue_scripts',array($this,'hyperMDCodeMirror') );
	}

	/**
	 * 初始化设置
	 *
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
		$this->options  = $this->get_options();
		$this->register_settings();
	}

	/**
	 * 将设置页面添加到菜单
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_plugins_page( $this->plugin_name . __( ' Options', $this->text_domain ), $this->plugin_name, 'manage_options', $this->plugin_slug, array( $this, 'settings_page' ) );
	}

	/**
	 * 构建设置字段
	 *
	 * @return array 在设置页面上显示的字段
	 */
	private function settings_fields() {
	    $mermaidConfig = '{
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
}';


		$settings['basic'] = array(
			'title'       => __( 'Basic', $this->text_domain ),
			'description' => __( '', $this->text_domain ),
			'fields'      => array(
				array(
					'id'          => 'support_post_page',
					'label'       => __( 'Use Markdown For Comments', $this->text_domain ),
					'description' => '<a href="' . admin_url( "options-discussion.php#wpcom_publish_comments_with_markdown" ) . '" target="_blank">' . __( 'Go', $this->text_domain ) . '</a>',
					'type'        => 'html'
				),
				array(
					'id'          => 'enable_image_paste',
					'label'       => __( 'ImagePaste/Drag-Drop', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'select',
					'options'     => array(
						'disable' => 'Disable',
						'local'   => 'Local Media',
						'smms'    => 'https://sm.ms'
					),
					'default'     => 'disable'
				),
				array(
					'id'          => 'hide_token',
					'label'       => __( 'Hide Token', $this->text_domain ),
					'description' => __( 'Auto show/hide markdown tokens like `##` or `*`', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'enabled_hover',
					'label'       => __( 'Enabled Hover', $this->text_domain ),
					'description' => __( 'When mouse hovers on a link or footnote ref, shows related footnote', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'enabled_click',
					'label'       => __( 'Enabled Click', $this->text_domain ),
					'description' => __( 'Click to open links / jump to footnotes / toggle TODOs, and more', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'enabled_paste',
					'label'       => __( 'Enabled Paste', $this->text_domain ),
					'description' => __( 'Convert content to Markdown before pasting', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'table_align',
					'label'       => __( 'Table Align', $this->text_domain ),
					'description' => __( 'Align Table Columns', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
			)
		);

		$settings['highlight'] = array(
			'title'       => __( 'Syntax Highlighting', $this->text_domain ),
			'description' => __( '', $this->text_domain ),
			'fields'      => array(
				array(
					'id'          => 'enable_highlight',
					'label'       => __( 'Enable Syntax Highlighting', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'line_numbers',
					'label'       => __( 'Line Numbers', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'show_language',
					'label'       => __( 'Show Code Language', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'copy_clipboard',
					'label'       => __( 'Copy To Clipboard', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'highlight_library_style',
					'label'       => __( 'PrismJS Syntax Highlight Style', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'select',
					'options'     => array(
						'default'        => 'Default',
						'dark'           => 'Dark',
						'funky'          => 'Funky',
						'okaidia'        => 'Okaidia',
						'twilight'       => 'Twilight',
						'coy'            => 'Coy',
						'solarizedlight' => 'Solarized Light',
						'tomorrow'       => 'Tomorrow Night',
						'customize'      => __( 'Customize Style Library', $this->text_domain )
					),
					'default'     => 'default'
				),
				array(
					'id'          => 'customize_my_style',
					'label'       => __( 'Customize Style Library', $this->text_domain ),
					'description' => __( 'Get More <a href="https://github.com/JaxsonWang/Prism.js-Style" target="_blank" rel="nofollow">Theme Style</a>', $this->text_domain ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'nothing', $this->text_domain )
				)
			)
		);

		$settings['emoji'] = array(
			'title'       => __( 'Emoji', $this->text_domain ),
			'description' => __( '', $this->text_domain ),
			'fields'      => array(
				array(
					'id'          => 'enable_emoji',
					'label'       => __( 'Enable Emoji', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'enhance_emoji',
					'label'       => __( 'Enhance mode', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				)
			)
		);

		$settings['math'] = array(
			'title'       => __( 'Math', $this->text_domain ),
			'description' => __( '', $this->text_domain ),
			'fields'      => array(
				array(
					'id'          => 'math_type',
					'label'       => __( 'Math Type', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'select',
					'options'     => array(
						'mathjax' => 'MathJax',
						'katex'   => 'KaTeX',
						'disable' => 'Disable',
					),
					'default'     => 'disable'
				),
			)
		);

		$settings['mermaid'] = array(
			'title'       => __( 'Mermaid', $this->text_domain ),
			'description' => __( '', $this->text_domain ),
			'fields'      => array(
				array(
					'id'          => 'enable_mermaid',
					'label'       => __( 'Enable Mermaid', $this->text_domain ),
					'description' => __( '', $this->text_domain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'    => 'mermaid_config',
					'label'   => __( 'Mermaid Config', $this->text_domain ),
					'description'    => __( 'More info: <a rel="nofollow" target="_blank" href="https://mermaidjs.github.io/mermaidAPI.html">MermaidAPI Doc</a> and <a href="https://github.com/knsv/mermaid/blob/master/src/mermaidAPI.js" target="_blank" rel="nofollow">MermaidAPI.js</a>', $this->text_domain ),
					'type'    => 'textarea',
					'default' => $mermaidConfig,
					'placeholder' => __( '', $this->text_domain )
				)
			)
		);

		$settings = apply_filters( 'plugin_settings_fields', $settings );

		return $settings;
	}


	/**
	 * 选项默认值保存
	 *
	 * @return array 选项的保存或默认选项。
	 */
	public function get_options() {
		$options = get_option( $this->plugin_slug );

		if ( ! $options && is_array( $this->settings ) ) {
			$options = Array();
			foreach ( $this->settings as $section => $data ) {
				foreach ( $data['fields'] as $field ) {
					$options[ $field['id'] ] = $field['default'];
				}
			}

			add_option( $this->plugin_slug, $options );
		}

		return $options;
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
		$val     = ! empty( $options[ $option_data ] ) ? $options[ $option_data ] : null;

		return $val;
	}

	/**
	 * 注册插件设置
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			register_setting( $this->plugin_slug, $this->plugin_slug, array( $this, 'validate_fields' ) );

			foreach ( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->plugin_slug );

				foreach ( $data['fields'] as $field ) {

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), $this->plugin_slug, $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * 生成用于显示字段的HTML
	 *
	 * @param  array $args Field data
	 *
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->plugin_slug . "[" . $field['id'] . "]";

		$data = ( isset( $this->options[ $field['id'] ] ) ) ? $this->options[ $field['id'] ] : '';

		switch ( $field['type'] ) {
			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( is_array( $data ) && in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;

			case 'html':
				$html .= '<span class="description">' . $data . '</span>';
				break;
		}

		switch ( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
				break;
		}

		echo $html;
	}

	/**
	 * 验证个别设置字段
	 *
	 * @param  array $data Inputted value
	 *
	 * @return array       Validated value
	 */
	public function validate_fields( $data ) {
		// $data数组包含要保存的值：
		// 要么清理/修改$data，要么返回false
		// 防止保存新选项

		// Sanitize fields, eg. cast number field to integer
		// $data['number_field'] = (int) $data['number_field'];

		// Validate fields, eg. don't save options if the password field is empty
		// if ( $data['password_field'] == '' ) {
		// 	add_settings_error( $this->plugin_slug, 'no-password', __('A password is required.', $this->text_domain), 'error' );
		// 	return false;
		// }

		return $data;
	}

	/**
	 * 加载设置页面内容
	 *
	 * @return void
	 */
	public function settings_page() {
		// 构建页面HTML输出
		// 如果您不需要选项卡式导航，只需删除<!-- Tab navigation -->标记之间的所有内容。
		?>
        <div class="wrap" id="<?php echo $this->plugin_slug; ?>">
            <h2><?php _e( 'WP HyperMD Settings', $this->text_domain ); ?></h2>
            <p><?php _e( 'Hi! Welcome!', $this->text_domain ); ?></p>

            <!-- Tab navigation starts -->
            <h2 class="nav-tab-wrapper settings-tabs hide-if-no-js">
				<?php
				foreach ( $this->settings as $section => $data ) {
					echo '<a href="#' . $section . '" class="nav-tab">' . $data['title'] . '</a>';
				}
				?>
            </h2>
			<?php $this->do_script_for_tabbed_nav(); ?>
            <!-- Tab navigation ends -->

            <form action="options.php" method="POST">
				<?php settings_fields( $this->plugin_slug ); ?>
                <div class="settings-container">
					<?php do_settings_sections( $this->plugin_slug ); ?>
                </div>
				<?php submit_button(); ?>
            </form>
        </div>
		<?php
	}

	public function hyperMDCodeMirror() {
		wp_enqueue_script( 'code-editor' );
		wp_enqueue_style( 'code-editor' );

		$settings = wp_enqueue_code_editor( array(
			'type' => 'json',
		) );

		// 系统禁用CodeMirror
		if ( false === $settings ) {
			return;
		}

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { jQuery("#mermaid_config").length !== 0 ? wp.codeEditor.initialize( "mermaid_config", %s ) : ""; } );',
				wp_json_encode( $settings )
			)
		);

		wp_add_inline_script(
			'wp-codemirror',
			'window.CodeMirror = wp.CodeMirror;'
		);
    }

	/**
	 * 打印选项卡式导航的jQuery脚本
	 *
	 * @return void
	 */
	private function do_script_for_tabbed_nav() {
		// 用于选项卡式导航的非常简单的jQuery逻辑。
		// 如果您不需要，请删除此功能。
		// 如果你有其他JS文件，你可以在那里合并。
		?>
        <script>
            jQuery(document).ready(function ($) {
                var headings = jQuery('.settings-container > h2, .settings-container > h3');
                var paragraphs = jQuery('.settings-container > p');
                var tables = jQuery('.settings-container > table');
                var triggers = jQuery('.settings-tabs a');

                triggers.each(function (i) {
                    triggers.eq(i).on('click', function (e) {
                        e.preventDefault();
                        triggers.removeClass('nav-tab-active');
                        headings.hide();
                        paragraphs.hide();
                        tables.hide();

                        triggers.eq(i).addClass('nav-tab-active');
                        headings.eq(i).show();
                        paragraphs.eq(i).show();
                        tables.eq(i).show();
                    });
                });

                triggers.eq(0).click();
            });
        </script>
		<?php
	}
}