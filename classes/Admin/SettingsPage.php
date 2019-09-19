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

use EventVetting\Settings;

class SettingsPage {
	/**
	 * Constructor.
	 *
	 * @param PageFactory $page The parent page factory.
	 */
	public function __construct( PageFactory $page ) {
		$page->add_child(
			__( 'Settings', 'event-vetting' ),
			[ $this, 'render_page' ],
			'',
			'manage_options'
		);
	}

	/**
	 * Callback to render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		settings_errors( Settings::OPTION_GROUP );
		settings_fields( Settings::OPTION_GROUP );
		do_settings_sections( Settings::OPTION_GROUP );
		submit_button( __( 'Save Settings', 'event-vetting' ) );
	}
}
