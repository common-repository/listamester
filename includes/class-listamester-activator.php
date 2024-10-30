<?php
/**
 * Plugin Activator
 *
 * @package listamester
 */
class Listamester_Activator {
	public static function activate() {
		add_option( 'listamester_lmid', '', '', 'yes' );
		add_option( 'listamester_lmpwd', '', '', 'yes' );
	}
}
