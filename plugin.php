<?php
/*
Plugin Name: WP Node
Description: Magical things that sound like fun, but might end badly.
Version: 0.1
Author: Eddie Moya
Author URI: http://eddiemoya.com
*/

define('WPNODE_PATH', 		plugin_dir_path(__FILE__));

include_once (WPNODE_PATH 		. 'wp_node.php');
//include (WPNODE_PATH 		. 'wp_node_factory.php');

add_action('init', 'create_nodes', 10);
function create_nodes(){

	$node = new WP_Node('skcategory');
}

