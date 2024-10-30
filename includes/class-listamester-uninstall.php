<?php
/**
 * Plugin uninstall
 *
 * @package listamester
 */
class Listamester_Uninstall {
	public static function uninstall() {
		$lm_defined_options = array( 'listamester_lmid', 'listamester_lmpwd' );
		// Clear up our settings
		foreach ( $lm_defined_options as $option_name ) {
			delete_option( $option_name );
		}
	}
}
