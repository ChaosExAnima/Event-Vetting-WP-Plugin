<?php
/**
 * Application post type class.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Application {

	const POST_TYPE = 'event_vetting_app'; // Needs to be shorter than 20 characters.

	/**
	 * Constructor.
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
			'name'               => __( 'Applications', 'event-vetting' ),
			'singular_name'      => __( 'Application', 'event-vetting' ),
			'add_new_item'       => __( 'Add New Application', 'event-vetting' ),
			'edit_item'          => __( 'Edit Application', 'event-vetting' ),
			'new_item'           => __( 'New Application', 'event-vetting' ),
			'view_item'          => __( 'View Application', 'event-vetting' ),
			'view_items'         => __( 'View Application', 'event-vetting' ),
			'search_items'       => __( 'Search Applications', 'event-vetting' ),
			'not_found'          => __( 'No Applications found', 'event-vetting' ),
			'not_found_in_trash' => __( 'No Applications found in trash', 'event-vetting' ),
			'all_items'          => __( 'All Applications', 'event-vetting' ),
		];
		register_post_type( self::POST_TYPE, [
			'labels'               => $labels,
			'description'          => __( 'Applications', 'event-vetting' ),
			'show_ui'              => true,
			'show_in_menu'         => false,
			'show_in_rest'         => false,
			'supports'             => [ 'thumbnail', 'revisions' ],
			'register_meta_box_cb' => [ $this, 'register_meta_boxes' ],
			'rewrite'              => false,
			'capabilities'         => [
				'create_posts' => 'do_not_allow',
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
