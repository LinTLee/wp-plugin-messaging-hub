<?php

namespace WP_MESSAGING_HUB\Taxonomies;

class Channel {

	const NAME = 'channel';

	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 10 );
	}

	// register the buildings taxonomy
	public function register_taxonomy() {

		$labels = array(
			'name'              => _x( 'Channels', 'taxonomy general name' ),
			'singular_name'     => _x( 'Channel', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Channel' ),
			'all_items'         => __( 'All Channels' ),
			'parent_item'       => __( 'Parent Channel' ),
			'parent_item_colon' => __( 'Parent Channel' ),
			'edit_item'         => __( 'Edit Channel' ),
			'update_item'       => __( 'Update Channel' ),
			'add_new_item'      => __( 'Add New Channel' ),
			'new_item_name'     => __( 'New Channel' ),
			'menu_name'         => __( 'Channels' ),
		);

		$args = array(
			'public'            => true,
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
      'rewrite'           => array( 'slug' => self::NAME )
		);

		register_taxonomy( self::NAME, array( 'post' ), $args );
	}

}
