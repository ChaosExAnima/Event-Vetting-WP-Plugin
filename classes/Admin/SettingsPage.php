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
use EventVetting\Setting;

class SettingsPage extends AdminPage {
	/**
	 * Reference to settings instance.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Parent page factory instance.
	 *
	 * @var PageFactory
	 */
	protected $parent_page;

	/**
	 * Constructor.
	 *
	 * @param PageFactory $page     The parent page factory.
	 * @param Settings    $settings Settings instance.
	 */
	public function __construct( PageFactory $page, Settings $settings ) {
		$this->settings    = $settings;
		$this->parent_page = $page;
	}

	/**
	 * Adds the submenu page.
	 *
	 * @return void
	 */
	public function menu_init() {
		$this->parent_page->add_child(
			__( 'Settings', 'event-vetting' ),
			[ $this, 'render_page' ],
			'',
			'',
			'manage_options'
		);
	}

	/**
	 * Initializes settings fields.
	 *
	 * @return void
	 */
	public function admin_init() {
		add_settings_section(
			EVENT_VETTING_PREFIX . 'section',
			'',
			'__return_empty_string',
			$this->settings->option_group
		);
		foreach ( $this->settings->get_display_settings() as $field ) {
			$render_callback = [ $this, 'render_text_field' ];
			if ( is_callable( $field->render_callback ) ) {
				$render_callback = $field->render_callback;
			} elseif ( is_callable( [ $this, "render_{$field->type}_field" ] ) ) {
				$render_callback = [ $this, "render_{$field->type}_field" ];
			}
			add_settings_field(
				$field->key,
				$field->title,
				$render_callback,
				$this->settings->option_group,
				EVENT_VETTING_PREFIX . 'section',
				[
					'label_for' => $field->key,
					'setting'   => $field,
				]
			);
		}
	}

	/**
	 * Callback to render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		$group_name = $this->settings->option_group;
		settings_errors( $group_name );
		echo '<div class="wrap">';
		printf(
			'<h1>%s</h1>',
			esc_html__( 'Event Vetting Settings', 'event-vetting' )
		);
		echo '<form action="options.php" method="post">';
		settings_fields( $group_name );
		do_settings_sections( $group_name );
		submit_button( __( 'Save Settings', 'event-vetting' ) );
		echo '</form></div>';
	}

	/**
	 * Renders text field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_text_field( array $args ) {
		$setting = $args['setting'];
		$type    = 'text';
		printf(
			'<input id="%1$s" name="%1$s" type="%2$s" value="%3$s" />',
			esc_attr( $setting->key ),
			esc_attr( $type ),
			esc_attr( $setting->value )
		);
		$this->render_description( $setting );
	}

	/**
	 * Renders checkbox field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_boolean_field( array $args ) {
		$setting = $args['setting'];
		printf(
			'<label for="%s">',
			esc_attr( $setting->key )
		);
		printf(
			'<input id="%1$s" name="%1$s" type="checkbox" value="1" %2$s />',
			esc_attr( $setting->key ),
			checked( true, $setting->value, false )
		);
		if ( $setting->description ) {
			echo ' ' . esc_html( $setting->description );
		}
		echo '</label>';
	}

	/**
	 * Renders the field description.
	 *
	 * @param Setting $setting Setting instance.
	 * @return void
	 */
	protected function render_description( Setting $setting ) {
		if ( $setting->description ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $setting->description )
			);
		}
	}
}
