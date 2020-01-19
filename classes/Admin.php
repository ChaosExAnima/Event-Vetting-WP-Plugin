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
use EventVetting\Admin\ApplicationListPage;

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
	 * The settings page.
	 *
	 * @var SettingsPage
	 */
	protected $settings_page;

	/**
	 * The application list page.
	 *
	 * @var ApplicationListPage
	 */
	protected $application_list_page;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings instance.
	 */
	public function __construct( Settings $settings ) {
		$this->settings              = $settings;
		$this->root_page             = new PageFactory( self::MENU_SLUG );
		$this->settings_page         = new SettingsPage( $this->root_page, $this->settings );
		$this->application_list_page = new ApplicationListPage( $this->root_page );
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
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	/**
	 * Initializes the admin page menus.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$this->root_page->menu_init(
			__( 'Event Vetting', 'event-vetting' ),
			__( 'Vetting', 'event-vetting' ),
			'__return_empty_string',
			Roles::VETTER_CAP,
			'dashicons-feedback'
		);
		$this->settings_page->menu_init();
	}

	/**
	 * Initializes child pages.
	 *
	 * @return void
	 */
	public function admin_init() {
		$this->settings_page->admin_init();
		$this->application_list_page->admin_init();
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
