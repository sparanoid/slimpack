<?php

class Jetpack {
	/**
	 * Get $content_width, but with a <s>twist</s> filter.
	 */
	public static function get_content_width() {
		$content_width = isset( $GLOBALS['content_width'] ) ? $GLOBALS['content_width'] : false;
		return apply_filters( 'jetpack_content_width', $content_width );
	}

	/**
	 * Get a list of activated modules as an array of module slugs.
	 */
	// public static function get_active_modules() {
	// 	$active = Jetpack_Options::get_option( 'active_modules' );
	// 	if ( ! is_array( $active ) )
	// 		$active = array();
	// 	if ( is_admin() && ( class_exists( 'VaultPress' ) || function_exists( 'vaultpress_contact_service' ) ) ) {
	// 		$active[] = 'vaultpress';
	// 	} else {
	// 		$active = array_diff( $active, array( 'vaultpress' ) );
	// 	}
	// 	return array_unique( $active );
	// }

	/**
	 * Check whether or not a Jetpack module is active.
	 *
	 * @param string $module The slug of a Jetpack module.
	 * @return bool
	 *
	 * @static
	 */
	// public static function is_module_active( $module ) {
	// 	return in_array( $module, self::get_active_modules() );
	// }
}
