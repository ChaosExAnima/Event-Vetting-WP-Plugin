<?php
/**
 * Core plugin class.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Core {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$settings = new Settings( EVENT_VETTING_PREFIX . 'group' );
		new Admin( $settings );
	}
}
