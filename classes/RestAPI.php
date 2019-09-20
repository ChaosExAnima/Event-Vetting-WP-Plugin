<?php
/**
 * REST API class.
 *
 * This class handles interaction with the WP API,
 * and provides a webhook for responses from forms.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class RestAPI {
	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Admin controller instance.
	 *
	 * @var Admin
	 */
	protected $admin;

	public function __construct( Settings $settings, Admin $admin ) {
		$this->settings = $settings;
		$this->admin    = $admin;
	}

	public function setup() {
		$this->settings->register(
			'enable_api',
			'string',
			true
		)->add_field_data(
			__( 'Enable REST API', 'event-vetting' ),
			__( 'Whether to enable the API to get vetting entries.', 'event-vetting' )
		);
	}
}
