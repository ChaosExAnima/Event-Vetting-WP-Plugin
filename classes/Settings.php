<?php
/**
 * Settings class that manages settings storage and retrieval.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

use EventVetting\Bases\Singleton;

class Settings extends Singleton {

	const OPTION_GROUP = 'event-vetting-settings';

	const OPTION_NAME_PREFIX = 'event_vetting_';

	/**
	 * Stores settings to be registered.
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Constructor.
	 */
	protected function __construct() {
		add_action( 'init', [ $this, 'setup' ] );
	}

	/**
	 * Registers a setting.
	 *
	 * @param string   $name              Name of the setting to register.
	 * @param string   $type              Type of the setting to register.
	 * @param string   $default           Default setting value.
	 * @param callable $sanitize_callback Callback for sanitization.
	 *
	 * @return boolean
	 */
	public static function register(
		string $name,
		string $type = 'string',
		string $default = '',
		callable $sanitize_callback = null
	) : bool {
		$self = self::instance();
		if ( isset( $self->settings[ $name ] ) ) {
			return false;
		}

		// Adds a sanitization callback based off type.
		if ( ! $sanitize_callback ) {
			switch ( $type ) {
				case 'string':
					$sanitize_callback = 'sanitize_text_field';
					break;
				case 'boolean':
					$sanitize_callback = 'wp_validate_boolean';
					break;
				case 'integer':
					$sanitize_callback = 'intval';
					break;
				case 'number':
					$sanitize_callback = 'floatval';
					break;
				case 'raw':
					break;
			}
		}

		// Ensure the default value is set.
		if ( is_callable( $sanitize_callback ) ) {
			$default = $sanitize_callback( $default );
		}
		$self->settings[ $name ] = compact( 'name', 'type', 'default', 'sanitize_callback' );

		return true;
	}

	/**
	 * Registers options.
	 *
	 * @return void
	 */
	public function setup() {
		foreach ( $this->settings as $name => $args ) {
			register_setting(
				self::OPTION_GROUP,
				self::OPTION_NAME_PREFIX . $name,
				$args
			);
		}
	}

	/**
	 * Gets a setting.
	 *
	 * Static wrapper around class method.
	 *
	 * @param string $name The name of the option.
	 * @return mixed
	 */
	public static function get( string $name ) {
		$self = self::instance();
		return $self->get_setting( $name );
	}

	/**
	 * Gets multiple settings.
	 *
	 * @param array $names Array of setting names.
	 * @return array       An array of values at originally requested index.
	 */
	public static function get_many( array $names ) : array {
		if ( 0 === count( $names ) ) {
			return [];
		}

		$self = self::instance();
		return array_map( [ $self, 'get_option' ], $names );
	}

	/**
	 * Updates a setting.
	 *
	 * Static wrapper around class method.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed  $value The value to set.
	 * @return boolean
	 */
	public static function set( string $name, $value ) : bool {
		$self = self::instance();
		return $self->update_setting( $name, $value );
	}

	/**
	 * Updates many settings.
	 *
	 * This accepts an associated array of setting key => values,
	 * and returns an associated array with setting keys => true if updated.
	 *
	 * @param array $names Array of setting names and new values.
	 * @return array
	 */
	public static function set_many( array $names ) : array {
		if ( 0 === count( $names ) ) {
			return [];
		}

		$output = [];
		$self   = self::instance();
		foreach ( $names as $name => $value ) {
			$output[ $name ] = $self->update_setting( $name, $value );
		}
		return $output;
	}

	/**
	 * Gets an option.
	 *
	 * @param string $name The name of the option.
	 * @return mixed
	 */
	private function get_setting( string $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return null;
		}
		$setting = $this->settings[ $name ];

		$value = get_option( self::OPTION_NAME_PREFIX . $name, $setting['default'] );
		if ( is_callable( $setting['sanitize_callback'] ) ) {
			$sanitize_callback = $setting['sanitize_callback'];
			return $sanitize_callback( $value );
		}
		return $value;
	}

	/**
	 * Updates a setting.
	 *
	 * Static wrapper around class method.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed  $value The value to set.
	 * @return boolean
	 */
	private function update_setting( string $name, $value ) : bool {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return false;
		}

		$setting = $this->settings[ $name ];
		if ( is_callable( $setting['sanitize_callback'] ) ) {
			$sanitize_callback = $setting['sanitize_callback'];
			$value             = $sanitize_callback( $value );
		}

		$updated = update_option( self::OPTION_NAME_PREFIX . $name, $value, false );
		if ( $updated ) {
			/**
			 * Fires for all updated setting events.
			 *
			 * @since 1.0.0
			 *
			 * @param mixed  $value The value of the setting.
			 * @param string $name  The name of the setting.
			 */
			do_action( 'event_vetting_updated_setting', $value, $name );

			/**
			 * Fires for specific update setting events.
			 *
			 * @since 1.0.0
			 *
			 * @param mixed  $value The value of the setting.
			 */
			do_action( "event_vetting_updated_setting_{$name}", $value );
		}
		return $updated;
	}
}
