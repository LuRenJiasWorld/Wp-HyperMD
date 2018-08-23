<?php

namespace HyperMDApp;

class Mermaid {

	private $plugin_slug;

	public function __construct( $plugin_slug ) {

		$this->plugin_slug = $plugin_slug;

		add_action( 'wp_enqueue_scripts', array( $this, 'mermaid_enqueue_scripts' ) );
		if( !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
			add_action( 'wp_print_footer_scripts', array( $this, 'mermaid_wp_footer_script' ) );
		}
	}

	public function mermaid_enqueue_scripts() {
		wp_deregister_script('jquery');
		wp_enqueue_script( 'jQuery-CDN', '//cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js', array(), '1.12.4', true );

		wp_enqueue_script( 'Mermaid',  '//cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js', array(), '8.0.0-rc.8', true );
	}

	public function mermaid_wp_footer_script() {
		?>
		<script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $(".mermaid script").remove();
                    mermaid.initialize(
                        <?php
                            $mermaidConfig = $this->get_opt('mermaid_config');
                            if ($mermaidConfig == '') {
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
                            }
                            echo $mermaidConfig;
                        ?>
                        ,'.mermaid');
                })
            })(jQuery)
		</script>
		<?php
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

}