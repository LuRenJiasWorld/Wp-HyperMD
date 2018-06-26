<?php

namespace Utils;

use \SettingsApi\SettingsApi as SettingsGo;

class Settings {

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
    protected $text_domain;

    private $settings_api;

    function __construct($plugin_name, $version, $text_domain) {
        $this->plugin_name = $plugin_name;
        $this->text_domain = $text_domain;
        $this->version = $version;

        $this->settings_api = new SettingsGo;

        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_plugins_page($this->plugin_name . __(' Options', $this->text_domain), $this->plugin_name, 'manage_options', 'wp-hypermd-settings', array($this, 'plugin_page'));
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'hypermd_basics',
                'title' => __('Basic Settings', $this->text_domain)
            ),
            array(
                'id' => 'hypermd_syntax_highlighting',
                'title' => __('Syntax Highlighting Settings', $this->text_domain)
            ),
            array(
                'id' => 'hypermd_editor_advanced',
                'title' => __('Advanced Settings', $this->text_domain)
            ),
        );

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'hypermd_basics' => array(
                array(
                    'name' => 'support_comment',
                    'label' => __('Use Markdown For Posts And Pages', $this->text_domain),
                    'desc' => '<a href="' . admin_url("options-writing.php") . '" target="_blank">' . __('Go', $this->text_domain) . '</a>',
                    'type' => 'html'
                ),
                array(
                    'name' => 'support_post_page',
                    'label' => __('Use Markdown For Comments', $this->text_domain),
                    'desc' => '<a href="' . admin_url("options-discussion.php#wpcom_publish_comments_with_markdown") . '" target="_blank">' . __('Go', $this->text_domain) . '</a>',
                    'type' => 'html'
                )
            ),
            'hypermd_syntax_highlighting' => array(
                array(
                    'name' => 'highlight_mode_auto',
                    'label' => __('Auto load mode', $this->text_domain),
                    'desc' => __('', $this->text_domain),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name' => 'line_numbers',
                    'label' => __('Line Numbers', $this->text_domain),
                    'desc' => __('', $this->text_domain),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name' => 'show_language',
                    'label' => __('Show Language', $this->text_domain),
                    'desc' => __('', $this->text_domain),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name' => 'copy_clipboard',
                    'label' => __('Copy to Clipboard', $this->text_domain),
                    'desc' => __('', $this->text_domain),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'name' => 'highlight_library_style',
                    'label' => __('PrismJS Syntax Highlight Style', $this->text_domain),
                    'desc' => __('Syntax highlight theme style', $this->text_domain),
                    'type' => 'select',
                    'options' => array(
                        'default' => 'Default',
                        'dark' => 'Dark',
                        'funky' => 'Funky',
                        'okaidia' => 'Okaidia',
                        'twilight' => 'Twilight',
                        'coy' => 'Coy',
                        'solarizedlight' => 'Solarized Light',
                        'tomorrow' => 'Tomorrow Night',
                        'customize' => __('Customize Style', $this->text_domain),
                    ),
                    'default' => 'default'
                ),
                array(
                    'name' => 'customize_my_style',
                    'label' => __('Customize Style Library', $this->text_domain),
                    'desc' => __('Get More <a href="https://github.com/JaxsonWang/Prism.js-Style" target="_blank" rel="nofollow">Theme Style</a>', $this->text_domain),
                    'type' => 'text',
                    'default' => 'notiong'
                )
            ),
            'hypermd_editor_advanced' => array(
                array(
                    'name' => 'debugger',
                    'label' => __('Debugger', $this->text_domain),
                    'desc' => '<a id="debugger" href="#">' . __('Info', $this->text_domain) . '</a>',
                    'type' => 'html'
                ),
                array(
                    'name' => 'hide_ads',
                    'label' => __('Hide Ads', $this->text_domain),
                    'desc' => __('', $this->text_domain),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo Debugger::hypermd_debug($this->text_domain);

        if ($this->get_option('hide_ads', 'hypermd_editor_advanced') == 'off') {
            //判断地区，根据不同的地区进入不同的文档
            switch (get_bloginfo('language')) {
                case 'zh-CN':
                    $donateImgUrl = '//gitee.com/JaxsonWang/JaxsonWang/raw/master/mydonate';
                    break;
                default :
                    $donateImgUrl = '//github.com/JaxsonWang/WP-Editor.md/raw/docs/screenshots';
            }
            echo '<div id="donate">';
            echo '<h3>' . __('Donate', $this->text_domain) . '</h3>';
            echo '<p style="width: 50%">' . __('It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using WP-HyperMD and find it useful, please consider making a donation. Your donation will help encourage and support the plugin’s continued development and better user support.Thank You!', $this->text_domain) . '</p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">Alipay(支付宝)：</strong><a rel="nofollow" target="_blank" href="' . $donateImgUrl . '/alipay.jpg"><img width="100" src="' . $donateImgUrl . '/alipay.jpg"/></a></p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">WeChat(微信)：</strong><a rel="nofollow" target="_blank" href="' . $donateImgUrl . '/wechart.jpg"><img width="100" src="' . $donateImgUrl . '/wechart.jpg"/></a></p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">PayPal(贝宝)：</strong><a rel="nofollow" target="_blank" href="https://www.paypal.me/JaxsonWang">https://www.paypal.me/JaxsonWang</a></p>';
            echo '</div>';
            echo '</div>';
        }

        $this->script_style();
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

    /**
     * 获取字段值
     *
     * @param string $option  字段名称
     * @param string $section 字段名称分组
     * @param string $default 没搜索到返回空
     *
     * @return mixed
     */
    private function get_option($option, $section, $default = '') {

        $options = get_option($section);

        if (isset($options[$option])) {
            return $options[$option];
        }

        return $default;
    }

    private function script_style() {
        ?>
        <style type="text/css" rel="stylesheet">
            /*设置选项样式*/
            .debugger-wrap {
                margin-top: 10px;
                display: none;
            }

            .debugger-wrap tbody tr {
                width: 100%;
                text-align: left;
            }

            .debugger-wrap tbody tr th {
                padding: 5px 10px 5px 0;
            }

            .debugger-wrap tbody tr th:nth-child(2) {
                color: #006686;
                width: 75%;
            }
        </style>
        <script type="text/javascript">
            (function ($) {
                //插入信息
                $('#jquery').text(jQuery.fn.jquery);
                //切换显示信息
                $('#debugger').click(function () {
                    $('.debugger-wrap').fadeToggle();
                    $('#donate').fadeToggle();
                });
                //判断非调试界面则隐藏
                $('a[href!="#hypermd_editor_advanced"].nav-tab').click(function () {
                    $('.debugger-wrap').fadeOut();
                    $('#donate').fadeIn();
                });
            })(jQuery);
        </script>
        <?php
    }

}