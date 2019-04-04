<?php
/**
 * Plugin Name: Eutopia Rising Vetting
 * Plugin URI: http://eutopia-rising.com
 * Description: Vetting plugin for Eutopia Rising.
 * Version: 1.0.0
 * Author: Echo <me@echonyc.name>
 * Author URI: echonyc.name
 * License: MIT
 * Text Domain: eutopia-vetting
 *
 * @package eutopia-vetting
 */

namespace EutopiaVetting;

require_once __DIR__ . '/vendor/autoload.php';

new Voyagers\Config();
new Events\Config();
