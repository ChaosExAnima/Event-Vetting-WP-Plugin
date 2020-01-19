<?php
/**
 * Core plugin class.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Core {
	/**
	 * Components of plugin.
	 *
	 * @var array
	 */
	protected $components = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$settings    = new Settings( EVENT_VETTING_PREFIX . 'group' );
		$notes       = new Notes();
		$admin       = new Admin( $settings );
		$roles       = new Roles();
		$application = new Application( $admin );
		$rest_api    = new RestAPI( $settings, $admin, $application );

		$this->components = compact( 'settings', 'notes', 'admin', 'roles', 'application', 'rest_api' );

		register_activation_hook( EVENT_VETTING_PLUGIN_FILE, [ __CLASS__, 'install' ] );
	}

	/**
	 * Sets up hooks.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'init', [ $this, 'run_actions' ] );

		$this->run_actions( 'setup' );
	}

	/**
	 * Initializes all child classes.
	 *
	 * @param string $current_action The action to run.
	 * @return void
	 */
	public function run_actions( string $current_action = null ) {
		if ( ! $current_action ) {
			$current_action = current_action();
		}
		foreach ( $this->components as $key => $instance ) {
			if ( is_callable( [ $instance, $current_action ] ) ) {
				call_user_func( [ $instance, $current_action ] );
			}
		}
	}

	/**
	 * Runs installation functions.
	 *
	 * @return void
	 */
	public static function install() {
		Roles::install();
	}
}
