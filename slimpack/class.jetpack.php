<?php

/**
 * Slimpack ver.
 */

/*
Options:
jetpack_options (array)
	An array of options.
	@see Jetpack_Options::get_option_names()

jetpack_register (string)
	Temporary verification secrets.

jetpack_activated (int)
	1: the plugin was activated normally
	2: the plugin was activated on this site because of a network-wide activation
	3: the plugin was auto-installed
	4: the plugin was manually disconnected (but is still installed)

jetpack_active_modules (array)
	Array of active module slugs.

jetpack_do_activate (bool)
	Flag for "activating" the plugin on sites where the activation hook never fired (auto-installs)
*/

class Jetpack {
	var $xmlrpc_server = null;

	private $xmlrpc_verification = null;

	var $HTTP_RAW_POST_DATA = null; // copy of $GLOBALS['HTTP_RAW_POST_DATA']

	/**
	 * @var array The handles of styles that are concatenated into jetpack.css
	 */
	var $concatenated_style_handles = array(
		'jetpack-carousel',
		'grunion.css',
		'the-neverending-homepage',
		'jetpack_likes',
		'jetpack_related-posts',
		'sharedaddy',
		'jetpack-slideshow',
		'presentations',
		'jetpack-subscriptions',
		'tiled-gallery',
		'widget-conditions',
		'jetpack_display_posts_widget',
		'gravatar-profile-widget',
		'widget-grid-and-list',
		'jetpack-widgets',
		'goodreads-widget',
	);

	var $plugins_to_deactivate = array(
		'stats'               => array( 'stats/stats.php', 'WordPress.com Stats' ),
		'shortlinks'          => array( 'stats/stats.php', 'WordPress.com Stats' ),
		'sharedaddy'          => array( 'sharedaddy/sharedaddy.php', 'Sharedaddy' ),
		'twitter-widget'      => array( 'wickett-twitter-widget/wickett-twitter-widget.php', 'Wickett Twitter Widget' ),
		'after-the-deadline'  => array( 'after-the-deadline/after-the-deadline.php', 'After The Deadline' ),
		'contact-form'        => array( 'grunion-contact-form/grunion-contact-form.php', 'Grunion Contact Form' ),
		'contact-form'        => array( 'mullet/mullet-contact-form.php', 'Mullet Contact Form' ),
		'custom-css'          => array( 'safecss/safecss.php', 'WordPress.com Custom CSS' ),
		'random-redirect'     => array( 'random-redirect/random-redirect.php', 'Random Redirect' ),
		'videopress'          => array( 'video/video.php', 'VideoPress' ),
		'widget-visibility'   => array( 'jetpack-widget-visibility/widget-visibility.php', 'Jetpack Widget Visibility' ),
		'widget-visibility'   => array( 'widget-visibility-without-jetpack/widget-visibility-without-jetpack.php', 'Widget Visibility Without Jetpack' ),
		'sharedaddy'          => array( 'jetpack-sharing/sharedaddy.php', 'Jetpack Sharing' ),
		'omnisearch'          => array( 'jetpack-omnisearch/omnisearch.php', 'Jetpack Omnisearch' ),
		'gravatar-hovercards' => array( 'jetpack-gravatar-hovercards/gravatar-hovercards.php', 'Jetpack Gravatar Hovercards' ),
		'latex'               => array( 'wp-latex/wp-latex.php', 'WP LaTeX' )
	);

	var $capability_translations = array(
		'administrator' => 'manage_options',
		'editor'        => 'edit_others_posts',
		'author'        => 'publish_posts',
		'contributor'   => 'edit_posts',
		'subscriber'    => 'read',
	);

	/**
	 * Map of modules that have conflicts with plugins and should not be auto-activated
	 * if the plugins are active.  Used by filter_default_modules
	 *
	 * Plugin Authors: If you'd like to prevent a single module from auto-activating,
	 * change `module-slug` and add this to your plugin:
	 *
	 * add_filter( 'jetpack_get_default_modules', 'my_jetpack_get_default_modules' );
	 * function my_jetpack_get_default_modules( $modules ) {
	 *     return array_diff( $modules, array( 'module-slug' ) );
	 * }
	 *
	 * @var array
	 */
	private $conflicting_plugins = array(
		'comments'          => array(
			'Intense Debate'                    => 'intensedebate/intensedebate.php',
			'Disqus'                            => 'disqus-comment-system/disqus.php',
			'Livefyre'                          => 'livefyre-comments/livefyre.php',
			'Comments Evolved for WordPress'    => 'gplus-comments/comments-evolved.php',
			'Google+ Comments'                  => 'google-plus-comments/google-plus-comments.php',
			'WP-SpamShield Anti-Spam'           => 'wp-spamshield/wp-spamshield.php',
		),
		'contact-form'      => array(
			'Contact Form 7'                    => 'contact-form-7/wp-contact-form-7.php',
			'Gravity Forms'                     => 'gravityforms/gravityforms.php',
			'Contact Form Plugin'               => 'contact-form-plugin/contact_form.php',
			'Easy Contact Forms'                => 'easy-contact-forms/easy-contact-forms.php',
			'Fast Secure Contact Form'          => 'si-contact-form/si-contact-form.php',
		),
		'minileven'         => array(
			'WPtouch'                           => 'wptouch/wptouch.php',
		),
		'latex'             => array(
			'LaTeX for WordPress'               => 'latex/latex.php',
			'Youngwhans Simple Latex'           => 'youngwhans-simple-latex/yw-latex.php',
			'Easy WP LaTeX'                     => 'easy-wp-latex-lite/easy-wp-latex-lite.php',
			'MathJax-LaTeX'                     => 'mathjax-latex/mathjax-latex.php',
			'Enable Latex'                      => 'enable-latex/enable-latex.php',
			'WP QuickLaTeX'                     => 'wp-quicklatex/wp-quicklatex.php',
		),
		'protect'           => array(
			'Limit Login Attempts'              => 'limit-login-attempts/limit-login-attempts.php',
			'Captcha'                           => 'captcha/captcha.php',
			'Brute Force Login Protection'      => 'brute-force-login-protection/brute-force-login-protection.php',
			'Login Security Solution'           => 'login-security-solution/login-security-solution.php',
			'WPSecureOps Brute Force Protect'   => 'wpsecureops-bruteforce-protect/wpsecureops-bruteforce-protect.php',
			'BulletProof Security'              => 'bulletproof-security/bulletproof-security.php',
			'SiteGuard WP Plugin'               => 'siteguard/siteguard.php',
			'Security-protection'               => 'security-protection/security-protection.php',
			'Login Security'                    => 'login-security/login-security.php',
			'Botnet Attack Blocker'             => 'botnet-attack-blocker/botnet-attack-blocker.php',
			'Wordfence Security'                => 'wordfence/wordfence.php',
			'All In One WP Security & Firewall' => 'all-in-one-wp-security-and-firewall/wp-security.php',
			'iThemes Security'                  => 'better-wp-security/better-wp-security.php',
		),
		'random-redirect'   => array(
			'Random Redirect 2'                 => 'random-redirect-2/random-redirect.php',
		),
		'related-posts'     => array(
			'YARPP'                             => 'yet-another-related-posts-plugin/yarpp.php',
			'WordPress Related Posts'           => 'wordpress-23-related-posts-plugin/wp_related_posts.php',
			'nrelate Related Content'           => 'nrelate-related-content/nrelate-related.php',
			'Contextual Related Posts'          => 'contextual-related-posts/contextual-related-posts.php',
			'Related Posts for WordPress'       => 'microkids-related-posts/microkids-related-posts.php',
			'outbrain'                          => 'outbrain/outbrain.php',
			'Shareaholic'                       => 'shareaholic/shareaholic.php',
			'Sexybookmarks'                     => 'sexybookmarks/shareaholic.php',
		),
		'sharedaddy'        => array(
			'AddThis'                           => 'addthis/addthis_social_widget.php',
			'Add To Any'                        => 'add-to-any/add-to-any.php',
			'ShareThis'                         => 'share-this/sharethis.php',
			'Shareaholic'                       => 'shareaholic/shareaholic.php',
		),
		'verification-tools' => array(
			'WordPress SEO by Yoast'            => 'wordpress-seo/wp-seo.php',
			'WordPress SEO Premium by Yoast'    => 'wordpress-seo-premium/wp-seo-premium.php',
			'All in One SEO Pack'               => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
		),
		'widget-visibility' => array(
			'Widget Logic'                      => 'widget-logic/widget_logic.php',
			'Dynamic Widgets'                   => 'dynamic-widgets/dynamic-widgets.php',
		),
	);

	/**
	 * Plugins for which we turn off our Facebook OG Tags implementation.
	 *
	 * Note: WordPress SEO by Yoast and WordPress SEO Premium by Yoast automatically deactivate
	 * Jetpack's Open Graph tags via filter when their Social Meta modules are active.
	 *
	 * Plugin authors: If you'd like to prevent Jetpack's Open Graph tag generation in your plugin, you can do so via this filter:
	 * add_filter( 'jetpack_enable_open_graph', '__return_false' );
	 */
	private $open_graph_conflicting_plugins = array(
		'2-click-socialmedia-buttons/2-click-socialmedia-buttons.php',
		                                                         // 2 Click Social Media Buttons
		'add-link-to-facebook/add-link-to-facebook.php',         // Add Link to Facebook
		'add-meta-tags/add-meta-tags.php',                       // Add Meta Tags
		'all-in-one-seo-pack/all_in_one_seo_pack.php',           // All in One SEO Pack
		'easy-facebook-share-thumbnails/esft.php',               // Easy Facebook Share Thumbnail
		'facebook/facebook.php',                                 // Facebook (official plugin)
		'facebook-awd/AWD_facebook.php',                         // Facebook AWD All in one
		'facebook-featured-image-and-open-graph-meta-tags/fb-featured-image.php',
		                                                         // Facebook Featured Image & OG Meta Tags
		'facebook-meta-tags/facebook-metatags.php',              // Facebook Meta Tags
		'wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php',
		                                                         // Facebook Open Graph Meta Tags for WordPress
		'facebook-revised-open-graph-meta-tag/index.php',        // Facebook Revised Open Graph Meta Tag
		'facebook-thumb-fixer/_facebook-thumb-fixer.php',        // Facebook Thumb Fixer
		'facebook-and-digg-thumbnail-generator/facebook-and-digg-thumbnail-generator.php',
		                                                         // Fedmich's Facebook Open Graph Meta
		'header-footer/plugin.php',                              // Header and Footer
		'network-publisher/networkpub.php',                      // Network Publisher
		'nextgen-facebook/nextgen-facebook.php',                 // NextGEN Facebook OG
		'social-networks-auto-poster-facebook-twitter-g/NextScripts_SNAP.php',
		                                                         // NextScripts SNAP
		'opengraph/opengraph.php',                               // Open Graph
		'open-graph-protocol-framework/open-graph-protocol-framework.php',
		                                                         // Open Graph Protocol Framework
		'seo-facebook-comments/seofacebook.php',                 // SEO Facebook Comments
		'seo-ultimate/seo-ultimate.php',                         // SEO Ultimate
		'sexybookmarks/sexy-bookmarks.php',                      // Shareaholic
		'shareaholic/sexy-bookmarks.php',                        // Shareaholic
		'sharepress/sharepress.php',                             // SharePress
		'simple-facebook-connect/sfc.php',                       // Simple Facebook Connect
		'social-discussions/social-discussions.php',             // Social Discussions
		'social-sharing-toolkit/social_sharing_toolkit.php',     // Social Sharing Toolkit
		'socialize/socialize.php',                               // Socialize
		'only-tweet-like-share-and-google-1/tweet-like-plusone.php',
		                                                         // Tweet, Like, Google +1 and Share
		'wordbooker/wordbooker.php',                             // Wordbooker
		'wpsso/wpsso.php',                                       // WordPress Social Sharing Optimization
		'wp-caregiver/wp-caregiver.php',                         // WP Caregiver
		'wp-facebook-like-send-open-graph-meta/wp-facebook-like-send-open-graph-meta.php',
		                                                         // WP Facebook Like Send & Open Graph Meta
		'wp-facebook-open-graph-protocol/wp-facebook-ogp.php',   // WP Facebook Open Graph protocol
		'wp-ogp/wp-ogp.php',                                     // WP-OGP
		'zoltonorg-social-plugin/zosp.php',                      // Zolton.org Social Plugin
	);

	/**
	 * Plugins for which we turn off our Twitter Cards Tags implementation.
	 */
	private $twitter_cards_conflicting_plugins = array(
	//	'twitter/twitter.php',                       // The official one handles this on its own.
	//	                                             // https://github.com/twitter/wordpress/blob/master/src/Twitter/WordPress/Cards/Compatibility.php
		'eewee-twitter-card/index.php',              // Eewee Twitter Card
		'ig-twitter-cards/ig-twitter-cards.php',     // IG:Twitter Cards
		'jm-twitter-cards/jm-twitter-cards.php',     // JM Twitter Cards
		'kevinjohn-gallagher-pure-web-brilliants-social-graph-twitter-cards-extention/kevinjohn_gallagher___social_graph_twitter_output.php',
		                                             // Pure Web Brilliant's Social Graph Twitter Cards Extension
		'twitter-cards/twitter-cards.php',           // Twitter Cards
		'twitter-cards-meta/twitter-cards-meta.php', // Twitter Cards Meta
		'wp-twitter-cards/twitter_cards.php',        // WP Twitter Cards
	);

	/**
	 * Message to display in admin_notice
	 * @var string
	 */
	var $message = '';

	/**
	 * Error to display in admin_notice
	 * @var string
	 */
	var $error = '';

	/**
	 * Modules that need more privacy description.
	 * @var string
	 */
	var $privacy_checks = '';

	/**
	 * Stats to record once the page loads
	 *
	 * @var array
	 */
	var $stats = array();

	/**
	 * Allows us to build a temporary security report
	 *
	 * @var array
	 */
	static $security_report = array();

	/**
	 * Jetpack_Sync object
	 */
	var $sync;

	/**
	 * Verified data for JSON authorization request
	 */
	var $json_api_authorization_request = array();

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

		/**
		 * These actions run checks to load additional files.
		 * They check for external files or plugins, so they need to run as late as possible.
		 */
		add_action( 'wp_head', array( $this, 'check_open_graph' ),       1 );
		add_action( 'plugins_loaded', array( $this, 'check_twitter_tags' ),     999 );
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

		/**
		 * As jetpack_register_genericons is by default fired off a hook,
		 * the hook may have already fired by this point.
		 * So, let's just trigger it manually.
		 */
		require_once( JETPACK__PLUGIN_DIR . '_inc/genericons.php' );
		jetpack_register_genericons();
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
	 * Gets all plugins currently active in values, regardless of whether they're
	 * traditionally activated or network activated.
	 *
	 * @todo Store the result in core's object cache maybe?
	 */
	public static function get_active_plugins() {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			// Due to legacy code, active_sitewide_plugins stores them in the keys,
			// whereas active_plugins stores them in the values.
			$network_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			if ( $network_plugins ) {
				$active_plugins = array_merge( $active_plugins, $network_plugins );
			}
		}

		sort( $active_plugins );

		return $active_plugins;
	}

	/**
	 * Checks whether a specific plugin is active.
	 *
	 * We don't want to store these in a static variable, in case
	 * there are switch_to_blog() calls involved.
	 */
	public static function is_plugin_active( $plugin = 'jetpack/jetpack.php' ) {
		return in_array( $plugin, self::get_active_plugins() );
	}

	/**
	 * Check if Jetpack's Open Graph tags should be used.
	 * If certain plugins are active, Jetpack's og tags are suppressed.
	 *
	 * @uses Jetpack::get_active_modules, add_filter, get_option, apply_filters
	 * @action plugins_loaded
	 * @return null
	 */
	public function check_open_graph() {
		if ( in_array( 'publicize', Jetpack::get_active_modules() ) || in_array( 'sharedaddy', Jetpack::get_active_modules() ) ) {
			add_filter( 'jetpack_enable_open_graph', '__return_true', 0 );
		}

		$active_plugins = self::get_active_plugins();

		if ( ! empty( $active_plugins ) ) {
			foreach ( $this->open_graph_conflicting_plugins as $plugin ) {
				if ( in_array( $plugin, $active_plugins ) ) {
					add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );
					break;
				}
			}
		}

		if ( apply_filters( 'jetpack_enable_open_graph', false ) ) {
			require_once JETPACK__PLUGIN_DIR . 'functions.opengraph.php';
		}
	}

	/**
	 * Check if Jetpack's Twitter tags should be used.
	 * If certain plugins are active, Jetpack's twitter tags are suppressed.
	 *
	 * @uses Jetpack::get_active_modules, add_filter, get_option, apply_filters
	 * @action plugins_loaded
	 * @return null
	 */
	public function check_twitter_tags() {

		$active_plugins = self::get_active_plugins();

		if ( ! empty( $active_plugins ) ) {
			foreach ( $this->twitter_cards_conflicting_plugins as $plugin ) {
				if ( in_array( $plugin, $active_plugins ) ) {
					add_filter( 'jetpack_disable_twitter_cards', '__return_true', 99 );
					break;
				}
			}
		}

		if ( apply_filters( 'jetpack_disable_twitter_cards', true ) ) {
			require_once JETPACK__PLUGIN_DIR . 'class.jetpack-twitter-cards.php';
		}
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
