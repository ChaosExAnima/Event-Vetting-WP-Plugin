<?php
/**
 * Base admin class.
 *
 * This kicks off the rest of the admin pages.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

use EventVetting\Admin\PageFactory;

class Admin {

	/**
	 * Constructor.
	 */
	protected function __construct() {
		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Initializes the admin page menus.
	 *
	 * @return void
	 */
	public function menu() {
		$page = new PageFactory(
			__( 'Event Vetting', 'event-vetting' ),
			'__return_empty_string'
		);
	}

	/**
	 * Initializes the admin pages.
	 *
	 * @return void
	 */
	public function init() {

	}
}
