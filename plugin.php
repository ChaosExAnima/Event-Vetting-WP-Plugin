<?php
/**
 * Plugin Name: Event Vetting
 * Plugin URI: https://echonyc.name
 * Description: Vetting plugin events. Integrates with Mailchimp and WooCommerce.
 * Version: 1.0.0
 * Author: Echo <ChaosExAnima@users.noreply.github.com >
 * Author URI: https://echonyc.name
 * License: MIT
 * Text Domain: event-vetting
 *
 * @package event-vetting
 */

namespace EventVetting;

define( 'EVENT_VETTING_VERSION', '1.0.0' );
define( 'EVENT_VETTING_PATH', plugin_dir_path( __FILE__ ) );
define( 'EVENT_VETTING_URL', plugin_dir_url( __FILE__ ) );
define( 'EVENT_VETTING_PREFIX', 'event_vetting_' );

require_once __DIR__ . '/vendor/autoload.php';

global $event_vetting_core;
$event_vetting_core = new Core();
$event_vetting_core->setup();
