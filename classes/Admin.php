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
use EventVetting\Admin\SettingsPage;

class Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
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
		new SettingsPage( $page );
	}

	/**
	 * Initializes the admin pages.
	 *
	 * @return void
	 */
	public function init() {

	}
}
