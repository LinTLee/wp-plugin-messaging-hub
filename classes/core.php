<?php

namespace WP_MESSAGING_HUB;

require_once 'admin/options.php';
require_once 'taxonomies/channel.php';
require_once 'utils/hipchat-importer.php';
require_once 'utils/slack-importer.php';

use WP_MESSAGING_HUB\Admin\Options;
use WP_MESSAGING_HUB\Taxonomies\Channel;
use WP_MESSAGING_HUB\Utils\Hipchat_Importer;
use WP_MESSAGING_HUB\Utils\Slack_Importer;

class Core {

  // instance of this class for use as singleton
  private static $_instance;

  // original file location of plugin
  private static $_plugin_file;

  // taxonomies
  private $_channel;

  // utils
  private $_options_dashboard;
  private $_hipchat_importer;
  private $_slack_importer;

  // initiates the collection of WP hooks
  public function __construct( $plugin_file ) {

    self::$_plugin_file = $plugin_file;

    $this->_channel = new Channel();
    $this->_options_dashboard = new Options();
    $this->_hipchat_importer = new Hipchat_Importer();
    $this->_slack_importer = new Slack_Importer();

    register_activation_hook( self::$_plugin_file, array( $this, 'schedule_events' ) );
    register_activation_hook( self::$_plugin_file, array( $this, 'register_uninstall_hook' ) );
    add_action( 'mh_hour_event',  array( $this, 'pull_messages_hourly' ) );
  }

  // register the uninstall hook on activation
  public function register_uninstall_hook() {
    register_uninstall_hook( self::$_plugin_file, array( get_class( $this ), 'clear_scheduled_events' ) );
  }

  // schedule events
  public function schedule_events() {
    wp_schedule_event( time(), 'hourly', 'my_hourly_event' );
  }

  // remove all scheduled events
  public function clear_scheduled_events() {
    wp_clear_scheduled_hook('my_hourly_event');
  }

  // import messages to WP posts
  public function pull_messages_hourly() {
    $this->_hipchat_importer->import();
    $this->_slack_importer->import();
  }

  // create the instance of the class
  public static function init( $plugin_file ) {
    // get (and instantiate, if necessary) the instance of the class
    if ( ! is_a( self::$_instance, __CLASS__ ) ) {
      self::$_instance = new self( $plugin_file );
    }
    return self::$_instance;
  }

}