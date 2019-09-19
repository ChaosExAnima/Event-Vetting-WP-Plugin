<?php
/**
 * Settings page.
 *
 * This controls the display of the settings,
 * but doesn't manage saving them.
 *
 * @package Event-Vetting
 */

namespace EventVetting\Admin;

use EventVetting\Bases\AdminPage;
use EventVetting\Settings;

class SettingsPage extends AdminPage {
	/**
	 * Reference to settings instance.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Constructor.
	 *
	 * @param PageFactory $page     The parent page factory.
	 * @param Settings    $settings Settings instance.
	 */
	public function __construct( PageFactory $page, Settings $settings ) {
		$page->add_child(
			__( 'Settings', 'event-vetting' ),
			[ $this, 'render_page' ],
			'',
			'manage_options'
		);
		$this->settings = $settings;
	}

	/**
	 * Callback to render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		$group_name = $this->settings->option_group;
		settings_errors( $group_name );
		settings_fields( $group_name );
		do_settings_sections( $group_name );
		submit_button( __( 'Save Settings', 'event-vetting' ) );
	}
}
