<?php

namespace Utils;

class Debugger {

    public static function hypermd_debug($text_domain) {
        $user = wp_get_current_user();
        $basics = '';
        $highlighting = '';
        $advanced = '';
        foreach ((array)get_option('hypermd_basics') as $key => $value) {
            $basics .= "{$key} => {$value} <br>";
        }
        foreach ((array)get_option('hypermd_syntax_highlighting') as $key => $value) {
            $highlighting .= "{$key} => {$value} <br>";
        }
        foreach ((array)get_option('hypermd_editor_advanced') as $key => $value) {
            $advanced .= "{$key} => {$value} <br>";
        }

        $debug_info = '<table class="debugger-wrap">';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Operating System', $text_domain) . '</th><th>' . PHP_OS . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Operating Environment', $text_domain) . '</th><th>' . $_SERVER["SERVER_SOFTWARE"] . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('PHP Version', $text_domain) . '</th><th>' . PHP_VERSION . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('PHP Operating Mode', $text_domain) . '</th><th>' . php_sapi_name() . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Browser Information', $text_domain) . '</th><th>' . $_SERVER['HTTP_USER_AGENT'] . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('WordPress Version', $text_domain) . '</th><th>' . $GLOBALS['wp_version'] . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('HyperMD Version', $text_domain) . '</th><th>' . WP_HYPERMD_VER . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('jQuery Version', $text_domain) . '</th><th id="jquery"></th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Current Roles', $text_domain) . '</th><th>' . $user->roles[0] . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Site URL', $text_domain) . '</th><th>' . site_url() . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Home URL', $text_domain) . '</th><th>' . home_url() . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Basic Settings', $text_domain) . '</th><th>' . $basics . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Syntax Highlighting Settings', $text_domain) . '</th><th>' . $highlighting . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '<tr>';
        $debug_info .= '<th>' . __('Advanced Settings', $text_domain) . '</th><th>' . $advanced . '</th>';
        $debug_info .= '</tr>';

        $debug_info .= '</div>';

        return $debug_info;
    }

}