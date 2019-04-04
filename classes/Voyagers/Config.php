<?php
namespace EutopiaVetting\Voyagers;

/**
 * Voyager configuration and setup class.
 */
class Config {

	const POST_TYPE = 'eutopia-voyager';

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
			'name'               => __( 'Voyagers', 'eutopia-vetting' ),
			'singular_name'      => __( 'Voyager', 'eutopia-vetting' ),
			'add_new_item'       => __( 'Add New Voyager', 'eutopia-vetting' ),
			'edit_item'          => __( 'Edit Voyager', 'eutopia-vetting' ),
			'new_item'           => __( 'New Voyager', 'eutopia-vetting' ),
			'view_item'          => __( 'View Voyager', 'eutopia-vetting' ),
			'view_items'         => __( 'View Voyagers', 'eutopia-vetting' ),
			'search_items'       => __( 'Search Voyagers', 'eutopia-vetting' ),
			'not_found'          => __( 'No Voyagers found', 'eutopia-vetting' ),
			'not_found_in_trash' => __( 'No Voyagers found in trash', 'eutopia-vetting' ),
			'all_items'          => __( 'All Voyagers', 'eutopia-vetting' ),
		];
		register_post_type( self::POST_TYPE, [
			'labels'               => $labels,
			'description'          => __( 'Voyagers to Eutopia events', 'eutopia-vetting' ),
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
