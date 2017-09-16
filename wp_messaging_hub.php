<?php
/*
Plugin Name: Messaging Hub
Description: Messaging Hub plugin imports data from messaging apps (only Slack and Hipchat are supported) as posts.
Version: 0.1
Author: Lin Lee
*/

define( 'WP_MESSAGING_HUB_DIR', dirname( __FILE__ ) );
add_action( 'plugins_loaded', 'wp_messaging_hub_init' );

// load the core class
function wp_messaging_hub_init() {
  require_once( WP_MESSAGING_HUB_DIR . '/classes/core.php' );
  \WP_MESSAGING_HUB\Core::init( __FILE__ );
}