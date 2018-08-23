<?php
/**
 * MathJax support.
 *
 * Backward compatibility requires support for both "$$mathjax$$" or "$mathjax$" shortcodes.
 *
 */

namespace HyperMDApp;

class MathJax {

	public function __construct() {

		//前端加载资源
		add_action( 'wp_enqueue_scripts', array( $this, 'mathjax_enqueue_scripts' ) );

		if( !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
			//执行公式渲染操作
			add_action( 'wp_print_footer_scripts', array( $this, 'mathjax_wp_footer_scripts' ) );
		}

	}

	public function mathjax_enqueue_scripts() {
		wp_enqueue_script( 'mathjax', '//cdn.jsdelivr.net/npm/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML', array(), '2.7.5', true );
	}

	public function mathjax_wp_footer_scripts() {
		?>
        <script type="text/x-mathjax-config">
			MathJax.Hub.Config({
			    tex2jax: {
                    processEscapes: true,
                    inlineMath: [['$', '$'], ["\\(", "\\)"]],
                    skipTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
                },
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