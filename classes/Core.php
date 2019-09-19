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
		new Admin();
	}
}
