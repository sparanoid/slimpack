<?php

/**
 * Slimpack ver.
 */

class Jetpack {

	/**
	 * Holds the singleton instance of this class
	 * @since 2.3.3
	 * @var Jetpack
	 */
	static $instance = false;

	/**
	 * Singleton
	 * @static
	 */
	public static function init() {
		if ( ! self::$instance ) {
			if ( did_action( 'plugins_loaded' ) )
				self::plugin_textdomain();
			else
				add_action( 'plugins_loaded', array( __CLASS__, 'plugin_textdomain' ), 99 );

			self::$instance = new Jetpack;

		}

		return self::$instance;
	}

	/**
	 * Constructor.  Initializes WordPress hooks
	 */
	private function Jetpack() {
		/*
		 * Do things that should run even in the network admin
		 * here, before we potentially fail out.
		 */
		add_filter( 'jetpack_require_lib_dir', 		array( $this, 'require_lib_dir' ) );

		add_action( 'wp_loaded', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'devicepx' ) );
	}

	/**
	 * Load language files
	 */
	public static function plugin_textdomain() {
		// Note to self, the third argument must not be hardcoded, to account for relocated folders.
		load_plugin_textdomain( 'jetpack', false, dirname( plugin_basename( JETPACK__PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Register assets for use in various modules and the Jetpack admin page.
	 *
	 * @uses wp_script_is, wp_register_script, plugins_url
	 * @action wp_loaded
	 * @return null
	 */
	public function register_assets() {
		if ( ! wp_script_is( 'spin', 'registered' ) ) {
			wp_register_script( 'spin', plugins_url( '_inc/spin.js', __FILE__ ), false, '1.3' );
		}

		if ( ! wp_script_is( 'jquery.spin', 'registered' ) ) {
			wp_register_script( 'jquery.spin', plugins_url( '_inc/jquery.spin.js', __FILE__ ), array( 'jquery', 'spin' ), '1.3' );
		}
	}

	/**
	 * Device Pixels support
	 * This improves the resolution of gravatars and wordpress.com uploads on hi-res and zoomed browsers.
	 */
	function devicepx() {
		wp_enqueue_script( 'devicepx', plugins_url( '_inc/devicepx-jetpack.js', __FILE__ ), array(), gmdate( 'oW' ), true );
	}

	/*
	 * Returns the location of Jetpack's lib directory. This filter is applied
	 * in require_lib().
	 *
	 * @filter require_lib_dir
	 */
	function require_lib_dir( $lib_dir ) {
		return JETPACK__PLUGIN_DIR . '_inc/lib';
	}

	/**
	 * Extract a module's slug from its full path.
	 */
	public static function get_module_slug( $file ) {
		return str_replace( '.php', '', basename( $file ) );
	}

	/**
	 * Returns an array of all PHP files in the specified absolute path.
	 * Equivalent to glob( "$absolute_path/*.php" ).
	 *
	 * @param string $absolute_path The absolute path of the directory to search.
	 * @return array Array of absolute paths to the PHP files.
	 */
	public static function glob_php( $absolute_path ) {
		if ( function_exists( 'glob' ) ) {
			return glob( "$absolute_path/*.php" );
		}

		$absolute_path = untrailingslashit( $absolute_path );
		$files = array();
		if ( ! $dir = @opendir( $absolute_path ) ) {
			return $files;
		}

		while ( false !== $file = readdir( $dir ) ) {
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, -4 ) ) {
				continue;
			}

			$file = "$absolute_path/$file";

			if ( ! is_file( $file ) ) {
				continue;
			}

			$files[] = $file;
		}

		closedir( $dir );

		return $files;
	}

	/**
	 * Loads the currently active modules.
	 */
	public static function load_modules() {
		require_once( JETPACK__PLUGIN_DIR . 'modules/module-extras.php' );
	}

	/**
	 * Check whether or not a Jetpack module is active.
	 *
	 * @param string $module The slug of a Jetpack module.
	 * @return bool
	 *
	 * @static
	 */
	public static function is_module_active( $module ) {
		return in_array( $module, self::get_active_modules() );
	}

	public static function enable_module_configurable( $module ) {
		$module = Jetpack::get_module_slug( $module );
		add_filter( 'jetpack_module_configurable_' . $module, '__return_true' );
	}

	public static function module_configuration_url( $module ) {
		$module = Jetpack::get_module_slug( $module );
		return Jetpack::admin_url( array( 'page' => 'jetpack', 'configure' => $module ) );
	}

	public static function module_configuration_load( $module, $method ) {
		$module = Jetpack::get_module_slug( $module );
		add_action( 'jetpack_module_configuration_load_' . $module, $method );
	}

	public static function module_configuration_head( $module, $method ) {
		$module = Jetpack::get_module_slug( $module );
		add_action( 'jetpack_module_configuration_head_' . $module, $method );
	}

	public static function module_configuration_screen( $module, $method ) {
		$module = Jetpack::get_module_slug( $module );
		add_action( 'jetpack_module_configuration_screen_' . $module, $method );
	}

	/**
	 * Get a list of activated modules as an array of module slugs.
	 */
	public static function get_active_modules() {
		$active = Jetpack_Options::get_option( 'active_modules' );
		if ( ! is_array( $active ) )
			$active = array();
		if ( is_admin() && ( class_exists( 'VaultPress' ) || function_exists( 'vaultpress_contact_service' ) ) ) {
			$active[] = 'vaultpress';
		} else {
			$active = array_diff( $active, array( 'vaultpress' ) );
		}
		return array_unique( $active );
	}

	/**
	 * Get $content_width, but with a <s>twist</s> filter.
	 */
	public static function get_content_width() {
		$content_width = isset( $GLOBALS['content_width'] ) ? $GLOBALS['content_width'] : false;
		return apply_filters( 'jetpack_content_width', $content_width );
	}
}
