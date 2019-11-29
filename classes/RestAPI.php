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

use WP_REST_Request;

class RestAPI {

	const NAMESPACE = 'event-vetting/v1';

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

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings instance.
	 * @param Admin    $admin    Admin instance.
	 */
	public function __construct( Settings $settings, Admin $admin ) {
		$this->settings = $settings;
		$this->admin    = $admin;
	}

	/**
	 * Runs initial setup functions.
	 *
	 * @return void
	 */
	public function setup() {
		$this->settings->register(
			'enable_api',
			'bool',
			true
		)->add_field_data(
			__( 'Enable REST API', 'event-vetting' ),
			__( 'Whether to enable the API to get vetting entries.', 'event-vetting' )
		);

		if ( $this->settings->enable_api ) {
			$this->settings
				->register( 'rest_key', 'string', '' )
				->add_field_data(
					__( 'REST API key', 'event-vetting' ),
					__( 'Key API requests must use.', 'event-vetting' )
				);
		}

		add_filter( 'event_vetting_pre_update_setting_rest_key', [ $this, 'filter_update_key' ] );
		add_action( 'rest_api_init', [ $this, 'action_rest_init' ] );
	}

	/**
	 * Initializes REST endpoints.
	 *
	 * @return void
	 */
	public function action_rest_init() {
		register_rest_route(
			self::NAMESPACE,
			'/apply',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'handle_apply' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Handles application.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public function handle_apply( WP_REST_Request $request ) {
		return rest_ensure_response( [ 'success' => true ] );
	}

	/**
	 * Generates a key on save if empty
	 *
	 * @param mixed $key The raw input.
	 * @return string
	 */
	public function filter_update_key( $key ) : string {
		if ( ! $key ) {
			return wp_generate_password( 12, false );
		}
		return sanitize_text_field( $key );
	}
}
