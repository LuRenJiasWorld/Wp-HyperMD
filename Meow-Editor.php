<?php
/**
 * Plugin Name: Meow Editor
 * Plugin URI: https://github.com/JaxsonWang/Meow-Editor
 * Description: 或许这是WordPress中很好用的Markdown插件。
 * Version: Alpha 0.1
 * Author: 淮城一只猫
 * Author URI: https://iiong.com/
 * License: GPLv3 or later
 */

require_once 'Parsedown/Parsedown.php';


class MeowEditor {
	function __construct() {
		remove_filter( 'the_content', 'wpautop' );//取消文章格式文本
		remove_filter( 'the_content', 'wptexturize' );//取消内容转义
		remove_filter( 'the_excerpt', 'wptexturize' );//取消摘要转义
		remove_filter( 'comment_text', 'wptexturize' );//取消评论转义

		add_filter( 'user_can_richedit', '__return_false' );//禁用可视化编辑器
		add_filter( 'the_content', array( $this, 'mdToHTML' ) );//文章内容
		add_filter( 'comment_text', array( $this, 'mdToHTML' ) );//评论内容
	}

	/*Markdown转换HTML*/
	function mdToHTML( $content ) {
		$parsedown = new Parsedown();
		$content   = $parsedown->text( $content );
		return $content;
	}
}

new MeowEditor;//实例化
?>