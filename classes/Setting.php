<?php
/**
 * Individual setting instance.
 *
 * This is controlled by the setting controller,
 * and normally not instantiated.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Setting {
	/**
	 * Settings parent instance.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Name of setting.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Title of setting. Used for display.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Description of setting. Used for display and REST.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Type of setting. Used for sanitization and display.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Default value.
	 *
	 * @var mixed
	 */
	protected $default;

	/**
	 * Whether to display this setting in the admin.
	 *
	 * @var boolean
	 */
	protected $show_in_settings = false;

	/**
	 * Callback to sanitize value on save.
	 *
	 * @var callable
	 */
	protected $sanitize_callback;

	/**
	 * Callback to display field.
	 *
	 * @var callable
	 */
	protected $display_callback;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings          The parent settings object.
	 * @param string   $name              Name of the setting to register.
	 * @param string   $type              Type of the setting to register.
	 * @param mixed    $default           Default setting value.
	 * @param callable $sanitize_callback Callback for sanitization.
	 */
	public function __construct(
		Settings $settings,
		string $name,
		string $type = 'string',
		$default = '',
		callable $sanitize_callback = null
	) {
		$this->settings          = $settings;
		$this->name              = $name;
		$this->type              = $type;
		$this->default           = $default;
		$this->sanitize_callback = $sanitize_callback;
	}

	/**
	 * Adds field data for display.
	 *
	 * @param string   $title            The title to display.
	 * @param string   $description      Optional. Description of field to display.
	 * @param callable $display_callback Optional. Custom callback for display.
	 * @return void
	 */
	public function add_field_data(
		string $title,
		string $description = '',
		callable $display_callback = null
	) {
		$this->show_in_settings = true;
		$this->title            = $title;
		$this->description      = $description;
		$this->display_callback = $display_callback;
	}

	/**
	 * Gets the value of the setting.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->settings->get( $this->name );
	}

	/**
	 * Getter for setting.
	 *
	 * @param string $key Key to get from current instance.
	 * @return mixed
	 */
	public function __get( string $key ) {
		if ( 'settings' === $key ) {
			return null;
		}
		if ( 'value' === $key ) {
			return $this->get_value();
		}
		if ( 'key' === $key ) {
			return EVENT_VETTING_PREFIX . $this->name;
		}
		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}
		return null;
	}
}

