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
	 * Top level slug for admin menu.
	 */
	const MENU_SLUG = 'event_vetting';

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * The root page.
	 *
	 * @var PageFactory
	 */
	protected $root_page;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings instance.
	 */
	public function __construct( Settings $settings ) {
		$this->settings  = $settings;
		$this->root_page = new PageFactory( self::MENU_SLUG );
	}

	/**
	 * Sets up hooks.
	 *
	 * @return void
	 */
	public function setup() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_menu', [ $this, 'menu' ] );
	}

	/**
	 * Initializes the admin page menus.
	 *
	 * @return void
	 */
	public function menu() {
		$this->root_page->menu_init(
			__( 'Event Vetting', 'event-vetting' ),
			__( 'Vetting', 'event-vetting' ),
			'__return_empty_string',
			'manage_options',
			'dashicons-feedback'
		);
		new SettingsPage( $this->root_page, $this->settings );
	}

	/**
	 * Gets the menu slug.
	 *
	 * @return string
	 */
	public function get_menu_slug() : string {
		return self::MENU_SLUG;
	}
}
