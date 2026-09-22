<?php
/**
 * Plugin Name:       PromptBridge for Divi
 * Plugin URI:        https://github.com/MrDemonWolf/promptbridge-for-divi
 * Description:       Staging-alpha diagnostics for a proposed Divi 5 bridge to an independently installed Codex runtime.
 * Version:           0.1.0-alpha.1
 * Requires at least: 6.7
 * Requires PHP:      8.3
 * Author:            MrDemonWolf
 * Author URI:        https://www.mrdemonwolf.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       promptbridge-for-divi
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MDW_PBD_VERSION', '0.1.0-alpha.1' );
define( 'MDW_PBD_FILE', __FILE__ );
define( 'MDW_PBD_DIR', plugin_dir_path( __FILE__ ) );
define( 'MDW_PBD_URL', plugin_dir_url( __FILE__ ) );

require_once MDW_PBD_DIR . 'includes/class-runtime-path.php';
require_once MDW_PBD_DIR . 'includes/class-runtime-home.php';
require_once MDW_PBD_DIR . 'includes/class-runtime-probe.php';
require_once MDW_PBD_DIR . 'includes/class-model-catalog.php';
require_once MDW_PBD_DIR . 'includes/class-diagnostics.php';
require_once MDW_PBD_DIR . 'includes/class-admin-page.php';
require_once MDW_PBD_DIR . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );

add_action( 'plugins_loaded', array( Plugin::instance(), 'register' ) );
