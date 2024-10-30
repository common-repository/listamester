<?php
/**
 * Plugin Name: Listamester
 * Description: Hírlevélküldő, email marketing szolgáltatás WordPress-hez
 * Version: 2.3.4
 * Author: Listamester
 * Author URI: https://listamester.hu
 * Text Domain: listamester
 * Domain Path: /languages
 *
 * @package listamester
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-listamester.php';

/**
 * Initialize the main class.
 */
new Listamester();

function listamester_admin() {
	if ( is_admin() ) {
		require_once __DIR__ . '/includes/class-listamester-admin.php';
		new Listamester_Admin();
	}
}

add_action( 'init', 'listamester_admin' );

/**
 * Runs during plugin activation.
 */
function activate_listamester() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-listamester-activator.php';
	Listamester_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_listamester' );

/**
 * Runs during plugin uninstall/delete.
 */
function listamester_uninstall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-listamester-uninstall.php';
	Listamester_Uninstall::uninstall();
}
register_uninstall_hook( __FILE__, 'listamester_uninstall' );
