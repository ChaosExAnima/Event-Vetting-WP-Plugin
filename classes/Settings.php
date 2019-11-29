<?php
/**
 * Settings class that manages settings storage and retrieval.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Settings {

	/**
	 * Option group name.
	 *
	 * @var string
	 */
	private $option_group;

	/**
	 * Stores settings to be registered.
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Constructor.
	 *
	 * @param string $group_name The name of the option group.
	 */
	public function __construct( string $group_name ) {
		$this->option_group = $group_name;
	}

	/**
	 * Registers a setting.
	 *
	 * @param string   $name              Name of the setting to register.
	 * @param string   $type              Type of the setting to register.
	 * @param mixed    $default           Default setting value.
	 * @param callable $sanitize_callback Callback for sanitization.
	 *
	 * @return Setting
	 */
	public function register(
		string $name,
		string $type = 'string',
		$default = '',
		callable $sanitize_callback = null
	) : Setting {
		if ( isset( $this->settings[ $name ] ) ) {
			return $this->settings[ $name ];
		}

		// Adds a sanitization callback based off type.
		if ( ! $sanitize_callback ) {
			switch ( $type ) {
				case 'string':
					$sanitize_callback = 'sanitize_text_field';
					break;
				case 'boolean':
				case 'bool':
					$type              = 'boolean';
					$sanitize_callback = 'wp_validate_boolean';
					break;
				case 'integer':
				case 'int':
					$type              = 'integer';
					$sanitize_callback = 'intval';
					break;
				case 'absint':
				case 'abs':
					$type              = 'integer';
					$sanitize_callback = 'absint';
					break;
				case 'num':
				case 'number':
					$type              = 'number';
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
		$this->settings[ $name ] = new Setting(
			$this,
			$name,
			$type,
			$default,
			$sanitize_callback,
		);

		return $this->settings[ $name ];
	}

	/**
	 * Registers options.
	 *
	 * @return void
	 */
	public function init() {
		foreach ( $this->settings as $name => $setting ) {
			$args = [
				'type'              => $setting->type,
				'description'       => $setting->description,
				'sanitize_callback' => $setting->sanitize_callback,
				'default'           => $setting->default,
			];
			register_setting(
				$this->option_group,
				EVENT_VETTING_PREFIX . $name,
				$args
			);
		}
	}

	/**
	 * Gets an option.
	 *
	 * @param string $name The name of the option.
	 * @return mixed
	 */
	public function get( string $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return null;
		}
		$setting = $this->settings[ $name ];

		$value = get_option( EVENT_VETTING_PREFIX . $name, $setting->default );
		if ( is_callable( $setting->sanitize_callback ) ) {
			return call_user_func( $setting->sanitize_callback, $value );
		}
		return $value;
	}

	/**
	 * Gets multiple settings.
	 *
	 * @param array $names Array of setting names.
	 * @return array       An array of values at originally requested index.
	 */
	public function get_many( array $names ) : array {
		if ( 0 === count( $names ) ) {
			return [];
		}

		return array_map( [ $this, 'get' ], $names );
	}

	/**
	 * Gets info about a particular setting.
	 *
	 * @param string $name  Key for setting to get.
	 * @return Setting|null Setting instance or null.
	 */
	public function get_setting( string $name ) {
		if ( isset( $this->settings[ $name ] ) ) {
			return $this->settings[ $name ];
		}
		return null;
	}

	/**
	 * Gets all settings that are to be displayed in the admin.
	 *
	 * @return array
	 */
	public function get_display_settings() : array {
		return array_filter( $this->settings, function( $setting ) {
			return $setting->show_in_settings;
		} );
	}

	/**
	 * Updates a setting.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed  $value The value to set.
	 * @return boolean
	 */
	public function set( string $name, $value ) : bool {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return false;
		}

		$setting = $this->settings[ $name ];
		if ( is_callable( $setting->sanitize_callback ) ) {
			$value = call_user_func( $setting->sanitize_callback, $value );
		}

		/**
		 * Filters specific settings before update.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed   $value The value of the setting.
		 * @param Setting $name  The setting object.
		 */
		$value = apply_filters( "event_vetting_pre_update_setting_{$name}", $value, $setting );
		var_dump( $value );

		$updated = update_option( EVENT_VETTING_PREFIX . $name, $value, false );
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

	/**
	 * Updates many settings.
	 *
	 * This accepts an associated array of setting key => values,
	 * and returns an associated array with setting keys => true if updated.
	 *
	 * @param array $names Array of setting names and new values.
	 * @return array
	 */
	public function set_many( array $names ) : array {
		if ( 0 === count( $names ) ) {
			return [];
		}

		$output = [];
		foreach ( $names as $name => $value ) {
			$output[ $name ] = $this->set( $name, $value );
		}
		return $output;
	}

	/**
	 * Getter function.
	 *
	 * @param string $key The key to get.
	 * @return mixed
	 */
	public function __get( string $key ) {
		switch ( $key ) {
			case 'option_group':
			case 'group':
				return $this->option_group;
			default:
				return $this->get( $key );
		}
	}
}
