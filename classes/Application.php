<?php
/**
 * Application post type class.
 *
 * @package event-vetting
 */

namespace EventVetting;

class Application {

	const POST_TYPE = 'vetting-application';

	/**
	 * Sets up the post type.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	/**
	 * Registers the post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = [
			'name'               => __( 'Voyagers', 'event-vetting' ),
			'singular_name'      => __( 'Voyager', 'event-vetting' ),
			'add_new_item'       => __( 'Add New Voyager', 'event-vetting' ),
			'edit_item'          => __( 'Edit Voyager', 'event-vetting' ),
			'new_item'           => __( 'New Voyager', 'event-vetting' ),
			'view_item'          => __( 'View Voyager', 'event-vetting' ),
			'view_items'         => __( 'View Voyagers', 'event-vetting' ),
			'search_items'       => __( 'Search Voyagers', 'event-vetting' ),
			'not_found'          => __( 'No Voyagers found', 'event-vetting' ),
			'not_found_in_trash' => __( 'No Voyagers found in trash', 'event-vetting' ),
			'all_items'          => __( 'All Voyagers', 'event-vetting' ),
		];
		register_post_type( self::POST_TYPE, [
			'labels'               => $labels,
			'description'          => __( 'Voyagers to Eutopia events', 'event-vetting' ),
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_rest'         => false,
			'supports'             => [ 'thumbnail', 'revisions' ],
			'register_meta_box_cb' => [ $this, 'register_meta_boxes' ],
			'menu_icon'            => 'dashicons-star-filled',
			'capabilities'         => [
				'create_posts' => 'do_not_allow',
			],
			'rewrite'              => [
				'slug'  => 'voyager',
				'pages' => false,
			],
		] );
	}

	/**
	 * Registers custom meta boxes.
	 *
	 * @return void
	 */
	public function register_meta_boxes() {

	}
}
