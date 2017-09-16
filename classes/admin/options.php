<?php

namespace WP_MESSAGING_HUB\Admin;

class Options {

  const OPTION_LABEL_HIPCHAT_AUTH_TOKEN = 'mhub_option_hipchat_auth_token';
  const OPTION_LABEL_HIPCHAT_ROOM_ID = 'mhub_option_hipchat_room_id';
  const OPTION_LABEL_SLACK_AUTH_TOKEN = 'mhub_option_slack_auth_token';
  const OPTION_LABEL_SLACK_CHANNEL_ID = 'mhub_option_slack_channel_id';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_options_dashboard' ), 10 );
	}

	// add options dashboard to admin menu
	public function add_options_dashboard() {
    $page_title = 'Messaging Hub Options';
    $menu_title = 'MHub Options';
    $capability = 'manage_options';
    $menu_slug = 'messaging_hub_options';
    $function = array( $this, 'display_options_dashboard' );

    add_options_page(
      $page_title,
      $menu_title,
      $capability,
      $menu_slug,
      $function
    );
	}

  // display the content of dashboard
  public function display_options_dashboard() {

    if ( isset( $_POST ) && ! empty( $_POST ) &&
         ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'mhub-options-dashboard' ) ) ) {
      return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
      wp_die( 'Unauthorized user' );
    }

    if ( isset( $_POST['hipchat_room_id'] ) ) {
      update_option( self::OPTION_LABEL_HIPCHAT_ROOM_ID, $_POST['hipchat_room_id'] );
    }
    if ( isset( $_POST['hipchat_auth_token'] ) ) {
      update_option( self::OPTION_LABEL_HIPCHAT_AUTH_TOKEN, $_POST['hipchat_auth_token'] );
    }
    if ( isset( $_POST['slack_channel_id'] ) ) {
      update_option( self::OPTION_LABEL_SLACK_CHANNEL_ID, $_POST['slack_channel_id'] );
    }
    if ( isset( $_POST['slack_auth_token'] ) ) {
      update_option( self::OPTION_LABEL_SLACK_AUTH_TOKEN, $_POST['slack_auth_token'] );
    }

    $hipchat_room_id = get_option( self::OPTION_LABEL_HIPCHAT_ROOM_ID, '');
    $hipchat_auth_token = get_option( self::OPTION_LABEL_HIPCHAT_AUTH_TOKEN, '');
    $slack_channel_id = get_option( self::OPTION_LABEL_SLACK_CHANNEL_ID, '');
    $slack_auth_token = get_option( self::OPTION_LABEL_SLACK_AUTH_TOKEN, '');

    $views_folder = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) ) . 'views';
    include trailingslashit( $views_folder ) . 'options-dashboard.php';
  }
}
