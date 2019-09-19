<?php
/**
 * Abstract class to provide singleton functionality.
 *
 * @package Event-Vetting
 */

namespace EventVetting\Bases;

abstract class Singleton {
	/**
	 * Singleton property.
	 *
	 * @var Core
	 */
	protected static $instance = null;

	/**
	 * Singleton accessor method.
	 *
	 * @return Core
	 */
	public static function instance() : Core {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}
