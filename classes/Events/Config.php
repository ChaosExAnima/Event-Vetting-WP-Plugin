<?php
namespace EutopiaVetting\Events;

use EutopiaVetting\Voyagers\Config as Voyagers;

/**
 * Events configuration and setup class.
 */
class Config {

	const POST_TYPE = 'eutopia-events';

	const TAXONOMY_NAME = '_eutopia-events';

	/**
	 * Sets up the post type.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	/**
	 * Sets up the post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = [
			'name'          => __( 'Events', 'eutopia-vetting' ),
			'singular_name' => __( 'Event', 'eutopia-vetting' ),
			'search_items'  => __( 'Search Events', 'eutopia-vetting' ),
			'popular_items' => __( 'Popular Events', 'eutopia-vetting' ),
			'all_items'     => __( 'All Events', 'eutopia-vetting' ),
			'edit_item'     => __( 'Edit Event', 'eutopia-vetting' ),
			'view_item'     => __( 'View Event', 'eutopia-vetting' ),
			'update_item'   => __( 'Update Event', 'eutopia-vetting' ),
			'add_new_item'  => __( 'Add New Event', 'eutopia-vetting' ),
			'new_item_name' => __( 'New Event Name', 'eutopia-vetting' ),
			'not_found'     => __( 'No events found', 'eutopia-vetting' ),
			'no_terms'      => __( 'No events', 'eutopia-vetting' ),
		];
		register_post_type( self::POST_TYPE, [
			'labels'               => $labels,
			'description'          => __( 'Eutopia Rising Events', 'eutopia-vetting' ),
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_rest'         => false,
			'supports'             => [ 'thumbnail', 'revisions' ],
			'register_meta_box_cb' => [ $this, 'register_meta_boxes' ],
			'taxonomies'           => [ self::TAXONOMY_NAME ],
			'menu_icon'            => 'dashicons-tickets-alt',
			'rewrite'              => false,
		] );
	}

	/**
	 * Registers the taxonomy.
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		$labels = [
			'name'          => __( 'Events', 'eutopia-vetting' ),
			'singular_name' => __( 'Event', 'eutopia-vetting' ),
			'search_items'  => __( 'Search Events', 'eutopia-vetting' ),
			'popular_items' => __( 'Popular Events', 'eutopia-vetting' ),
			'all_items'     => __( 'All Events', 'eutopia-vetting' ),
			'edit_item'     => __( 'Edit Event', 'eutopia-vetting' ),
			'view_item'     => __( 'View Event', 'eutopia-vetting' ),
			'update_item'   => __( 'Update Event', 'eutopia-vetting' ),
			'add_new_item'  => __( 'Add New Event', 'eutopia-vetting' ),
			'new_item_name' => __( 'New Event Name', 'eutopia-vetting' ),
			'not_found'     => __( 'No events found', 'eutopia-vetting' ),
			'no_terms'      => __( 'No events', 'eutopia-vetting' ),
		];
		register_taxonomy( self::TAXONOMY_NAME, Voyagers::POST_TYPE, [
			'labels'       => $labels,
			'description'  => __( 'Eutopia Rising Events', 'eutopia-vetting' ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => false,
			'show_in_rest' => false,
			'meta_box_cb'  => 'post_categories_meta_box',
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
