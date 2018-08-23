<?php

namespace HyperMD;

use HyperMDAdmin\Controller as ControllerAdmin;
use HyperMDApp\ImagePaste;
use HyperMDApp\KaTeX;
use HyperMDApp\MathJax;
use HyperMDApp\Mermaid;
use HyperMDApp\Twemoji;
use HyperMDFront\Controller as ControllerFront;
use HyperMDApp\WPComMarkdown;
use HyperMDApp\PrismJSAuto;
use HyperMDApp\TaskList;
use HyperMDUtils\Guide;
use HyperMDUtils\Internationalization;
use HyperMDUtils\Loader;
use HyperMDUtils\PluginMeta;
use HyperMDUtils\Settings;

/**
 * 核心插件类
 * Class Main
 *
 * @package HyperMD
 */
class Main {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * textdomain
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $text_domain
     */
    protected $text_domain;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'WP HyperMD';
        $this->plugin_slug = 'wp-hypermd-settings';
        $this->text_domain = 'hypermd';
        $this->version = CAT_HYPERMD_VER;
        $this->loader = new Loader();

        $this->run_core();

        $this->set_locale();
        $this->define_admin_hooks();
        //$this->define_public_hooks();
    }

	/**
	 * 国际化
	 *
	 * 使用 Internationalization 类来设置域并使用WordPress注册钩子
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization();
		$plugin_i18n->set_domain($this->get_text_domain());

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new ControllerAdmin($this->get_plugin_name(), $this->get_version(), $this->get_text_domain(), $this->get_plugin_slug());

        //$this->loader->add_action('edit_page_form', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('edit_page_form', $plugin_admin, 'enqueue_scripts');
        //$this->loader->add_action('edit_form_advanced', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('edit_form_advanced', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new ControllerFront($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * 实现方法
     *
     * @return void
     */
    public function run_core() {
        // 实现Markdown类
        new WPComMarkdown($this->get_text_domain(), $this->get_plugin_slug());
        // 实现设置类
        new Settings($this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version(), $this->get_text_domain());
        // 实现插件meta信息
        new PluginMeta($this->get_text_domain());
        // 实现欢迎页面提醒
        new Guide($this->get_text_domain());
        // 任务列表
        new TaskList();

	    $this->get_opt( 'enable_image_paste' ) == 'local' ? new ImagePaste() : null;

	    $this->get_opt( 'enable_highlight' ) == 'on' ? new PrismJSAuto( $this->get_plugin_slug() ) : null;

	    $this->get_opt( 'math_type' ) == 'mathjax' ? new MathJax() : new KaTeX();

	    $this->get_opt( 'enable_emoji' ) == 'on' && $this->get_opt( 'enhance_emoji' ) == 'on' ? new Twemoji( $this->get_plugin_slug() ) : null;

	    $this->get_opt( 'enable_mermaid' ) == 'on' ? new Mermaid( $this->get_plugin_slug() ) : null;

        return;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

	/**
	 * Get slug
	 * @return string
	 */
    public function get_plugin_slug() {
    	return $this->plugin_slug;
    }


    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_text_domain() {
        return $this->text_domain;
    }

	/**
	 * 获取选项值
	 * @param string $option_data 选项值
	 *
	 * @return mixed
	 */
	public function get_opt($option_data) {
		$options = get_option( $this->get_plugin_slug() );
		$val = !empty($options[$option_data]) ? $options[$option_data] : 'off';
		return $val;
	}

}
