<?php
/**
 * Core plugin class.
 *
 * @package event-vetting
 */

namespace EventVetting;

class Core {
	/**
	 * Singleton property.
	 *
	 * @var Core
	 */
	private static $instance = null;

	/**
	 * Singleton accessor method.
	 *
	 * @return Core
	 */
	public static function init() : Core {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
	}
}
