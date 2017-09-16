<?php

namespace WP_MESSAGING_HUB\Utils;

use WP_MESSAGING_HUB\Admin\Options;

class Slack_Importer {

  private $_auth_token = '';
  private $_channel_id = '';

  const HTTP_REQ_TIMEOUT = 360;
  const MAX_RESULTS = 100;
  const POST_TAXONOMY = 'channel';
  const POST_TAX_LABEL = 'Hipchat';

  private $users = array();

  // constructor
  public function __construct() {
    $this->_channel_id = get_option( Options::OPTION_LABEL_SLACK_CHANNEL_ID, '');
    $this->_auth_token = get_option( Options::OPTION_LABEL_SLACK_AUTH_TOKEN, '');
  }

  // import data from messaging app
  public function import() {
    // initiate HTTP request to Slack API
    $res = $this->_request_messages_api();
    if ( $res === false || empty( $res ) ) {
      return false;
    }
    $messages = $this->_get_messages( $res );
    if ( $messages === false || empty( $messages ) ) {
      return false;
    }

    date_default_timezone_set('America/New_York');
    $imported_posts = array();
    foreach ( $messages as $message ) {
      $ts = $message['ts'];
      $date = new DateTime("@$ts");
      $imported_posts[] = $this->_insert_post(
        sprintf('Slack Notification from %s', $date->format('Y-m-d H:i:s' ) ),
        $message['text']
      );
    }

    return count( $messages );
  }

  // send API request to pull users
  private function _request_users_api() {
    $srv_args = array(
      'timeout' => HTTP_REQ_TIMEOUT,
      'method'  => 'GET',
      'headers' => array(
        'Accept'        => 'application/json;ver=1.0',
        'Content-Type'  => 'application/json; charset=UTF-8',
      )
    );
    $srv_url = sprintf(
      'https://slack.com/api/users.identity?token=%s&pretty=1',self::AUTH_TOKEN );
    $resp = wp_remote_request( $srv_url, $srv_args );

    if ( is_wp_error( $resp ) ) {
      error_log( print_r( $resp, true ) );
      return false;
    }
    if ( $resp['response']['code'] !== 200 ) {
      error_log( sprintf('Response code for %s: %d', $srv_url, $resp['response']['code'] ) );
      return false;
    }

    $resp_body = wp_remote_retrieve_body( $resp );
    if ( empty( $resp_body ) || is_wp_error( $resp_body ) ) {
      error_log( print_r( $resp_body, true ) );
      return false;
    }
    return $resp_body;
  }

  // send API request to pull messages
  private function _request_messages_api() {
    $srv_args = array(
      'timeout' => HTTP_REQ_TIMEOUT,
      'method'  => 'GET',
      'headers' => array(
        'Accept'        => 'application/json;ver=1.0',
        'Content-Type'  => 'application/json; charset=UTF-8',
      )
    );
    $srv_url = sprintf(
      'https://slack.com/api/channels.history?token=%s&channel=%s&count=%d&pretty=1',
      $this->_auth_token,
      $this->_channel_id,
      self::MAX_RESULTS
    );
    $resp = wp_remote_request( $srv_url, $srv_args );

    if ( is_wp_error( $resp ) ) {
      error_log( print_r( $resp, true ) );
      return false;
    }
    if ( $resp['response']['code'] !== 200 ) {
      error_log( sprintf('Response code for %s: %d', $srv_url, $resp['response']['code'] ) );
      return false;
    }

    $resp_body = wp_remote_retrieve_body( $resp );
    if ( empty( $resp_body ) || is_wp_error( $resp_body ) ) {
      error_log( print_r( $resp_body, true ) );
      return false;
    }
    return $resp_body;
  }

  // format API response
  private function _get_messages( $res ) {
    $json_object = json_decode( $res, TRUE );
    if ( $json_object['ok'] !== true ) {
      return false;
    }
    return $json_object['messages'];
  }

  // insert a new post based on the given parameters
  private function _insert_post( $title, $content ) {
    // define the post parameters
    $args = array(
      'post_title'   => $title,
      'post_status'  => 'publish',
      'post_content' => $content
    );

    $post_id = wp_insert_post( $args, true );
    if ( is_wp_error( $post_id ) ) {
      error_log( sprintf( 'Error on inserting new post: %s', $post_id->get_error_message() ) );
      return false;
    }

    // indicate the post as online chat message by tagging
    $is_tagged = $this->_tag_post( $post_id, 'post_tag', 'online chat message' );
    if ( $is_tagged === false ) {
      return false;
    }
    // tag the source of messaging channel
    $is_tagged = $this->_tag_post( $post_id, self::POST_TAXONOMY, self::POST_TAX_LABEL );
    if ( $is_tagged === false ) {
      return false;
    }

    return $post_id;
  }

  // attach tags to the given post
  private function _tag_post( $post_id, $taxonomy, $term_label ) {
    $term = term_exists( $term_label, $taxonomy );
    if ( $term == 0 || $term == null ) {
      return $post_id;
    }

    $term_ids[] = intval( $term['term_id'] );
    $term_taxonomy_ids = wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
    if ( is_wp_error( $term_taxonomy_ids ) ) {
      error_log( sprintf( 'Error on attaching a term to the post: %s', $term_taxonomy_ids->get_error_message() ) );
      return false;
    }
    return true;
  }

}