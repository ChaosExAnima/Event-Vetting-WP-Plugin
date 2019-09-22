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
		$admin       = new Admin( $settings );
		$roles       = new Roles();
		$application = new Application( $admin );

		$this->components = compact( 'settings', 'admin', 'roles', 'application' );
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
}
