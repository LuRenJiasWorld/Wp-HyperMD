<?php

namespace HyperMDUtils;

class Settings {
	private $plugin_name;
	private $plugin_slug;
	private $verison;
	private $textdomain;
	private $options;
	private $settings;

	public function __construct( $plugin_name, $plugin_slug, $version, $textdomain ) {
		$this->plugin_slug = $plugin_slug;
		$this->plugin_name = $plugin_name;
		$this->verison     = $version;
		$this->textdomain  = str_replace( '_', '-', $plugin_slug );

		// 初始化设置
		add_action( 'admin_init', array( $this, 'init' ) );

		// 将设置页面添加到菜单
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
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
		add_plugins_page( $this->plugin_name . __( ' Options', $this->textdomain ), $this->plugin_name, 'manage_options', $this->plugin_slug, array( $this, 'settings_page' ) );
	}

	/**
	 * 构建设置字段
	 *
	 * @return array 在设置页面上显示的字段
	 */
	private function settings_fields() {

		$settings['basic'] = array(
			'title'       => __( 'Basic Settings', $this->textdomain ),
			'description' => __( '', $this->textdomain ),
			'fields'      => array(
				array(
					'id'          => 'support_comment',
					'label'       => __( 'Use Markdown For Posts And Pages', $this->textdomain ),
					'description' => '<a href="' . admin_url( "options-writing.php" ) . '" target="_blank">' . __( 'Go', $this->textdomain ) . '</a>',
					'type'        => 'html'
				),
				array(
					'id'          => 'support_post_page',
					'label'       => __( 'Use Markdown For Comments', $this->textdomain ),
					'description' => '<a href="' . admin_url( "options-discussion.php#wpcom_publish_comments_with_markdown" ) . '" target="_blank">' . __( 'Go', $this->textdomain ) . '</a>',
					'type'        => 'html'
				)
			)
		);

		$settings['hypermd_syntax_highlighting'] = array(
			'title'       => __( 'Syntax Highlighting', $this->textdomain ),
			'description' => __( '', $this->textdomain ),
			'fields'      => array(
				array(
					'id'          => 'enable_highlight',
					'label'       => __( 'Enable Syntax Highlighting', $this->textdomain ),
					'description' => __( '', $this->textdomain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'line_numbers',
					'label'       => __( 'Line Numbers', $this->textdomain ),
					'description' => __( '', $this->textdomain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'show_language',
					'label'       => __( 'Show Code Language', $this->textdomain ),
					'description' => __( '', $this->textdomain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'copy_clipboard',
					'label'       => __( 'Copy To Clipboard', $this->textdomain ),
					'description' => __( '', $this->textdomain ),
					'type'        => 'checkbox',
					'default'     => 'off'
				),
				array(
					'id'          => 'highlight_library_style',
					'label'       => __( 'PrismJS Syntax Highlight Style', $this->textdomain ),
					'description' => __( '', $this->textdomain ),
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
						'customize'      => __( 'Customize Style Library', $this->textdomain )
					),
					'default'     => 'default'
				),
				array(
					'id'          => 'customize_my_style',
					'label'       => __( 'Customize Style Library', $this->textdomain ),
					'description' => __( 'Get More <a href="https://github.com/JaxsonWang/Prism.js-Style" target="_blank" rel="nofollow">Theme Style</a>', $this->textdomain ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'nothing', $this->textdomain )
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
	 * @param $data
	 *
	 * @return mixed
	 */
	public function get_opt($data) {
	    $options = get_option( $this->plugin_slug );
	    return $options[$data];
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
		// 	add_settings_error( $this->plugin_slug, 'no-password', __('A password is required.', $this->textdomain), 'error' );
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
            <h2><?php _e( 'WP HyperMD Settings', $this->textdomain ); ?></h2>
            <p><?php _e( 'Hi! Welcome!', $this->textdomain ); ?></p>

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
                })

                triggers.eq(0).click();
            });
        </script>
		<?php
	}
}