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
	private function __construct() {
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
	 * List available Jetpack modules. Simply lists .php files in /modules/.
	 * Make sure to tuck away module "library" files in a sub-directory.
	 */
	public static function get_available_modules( $min_version = false, $max_version = false ) {
		static $modules = null;

		if ( ! isset( $modules ) ) {
			$available_modules_option = Jetpack_Options::get_option( 'available_modules', array() );
			// Use the cache if we're on the front-end and it's available...
			if ( ! is_admin() && ! empty( $available_modules_option[ JETPACK__VERSION ] ) ) {
				$modules = $available_modules_option[ JETPACK__VERSION ];
			} else {
				$files = Jetpack::glob_php( JETPACK__PLUGIN_DIR . 'modules' );

				$modules = array();

				foreach ( $files as $file ) {
					if ( ! $headers = Jetpack::get_module( $file ) ) {
						continue;
					}

					$modules[ Jetpack::get_module_slug( $file ) ] = $headers['introduced'];
				}

				Jetpack_Options::update_option( 'available_modules', array(
					JETPACK__VERSION => $modules,
				) );
			}
		}

		$mods = apply_filters( 'jetpack_get_available_modules', $modules, $min_version, $max_version );

		if ( ! $min_version && ! $max_version ) {
			return array_keys( $mods );
		}

		$r = array();
		foreach ( $mods as $slug => $introduced ) {
			if ( $min_version && version_compare( $min_version, $introduced, '>=' ) ) {
				continue;
			}

			if ( $max_version && version_compare( $max_version, $introduced, '<' ) ) {
				continue;
			}

			$r[] = $slug;
		}

		return $r;
	}

	/**
	 * Like core's get_file_data implementation, but caches the result.
	 */
	public static function get_file_data( $file, $headers ) {
		//Get just the filename from $file (i.e. exclude full path) so that a consistent hash is generated
		$file_name = basename( $file );
		$file_data_option = Jetpack_Options::get_option( 'file_data', array() );
		$key              = md5( $file_name . serialize( $headers ) );
		$refresh_cache    = is_admin() && isset( $_GET['page'] ) && 'jetpack' === substr( $_GET['page'], 0, 7 );

		// If we don't need to refresh the cache, and already have the value, short-circuit!
		if ( ! $refresh_cache && isset( $file_data_option[ JETPACK__VERSION ][ $key ] ) ) {
			return $file_data_option[ JETPACK__VERSION ][ $key ];
		}

		$data = get_file_data( $file, $headers );

		// Strip out any old Jetpack versions that are cluttering the option.
		$file_data_option = array_intersect_key( (array) $file_data_option, array( JETPACK__VERSION => null ) );
		$file_data_option[ JETPACK__VERSION ][ $key ] = $data;
		Jetpack_Options::update_option( 'file_data', $file_data_option );

		return $data;
	}

	public static function translate_module_tag( $untranslated_tag ) {
		// Tags are aggregated by tools/build-module-headings-translations.php
		// and output in modules/module-headings.php
		return _x( $untranslated_tag, 'Module Tag', 'jetpack' );
	}

	/**
	 * Generate a module's path from its slug.
	 */
	public static function get_module_path( $slug ) {
		return JETPACK__PLUGIN_DIR . "modules/$slug.php";
	}

	/**
	 * Load module data from module file. Headers differ from WordPress
	 * plugin headers to avoid them being identified as standalone
	 * plugins on the WordPress plugins page.
	 */
	public static function get_module( $module ) {
		$headers = array(
			'name'                  => 'Module Name',
			'description'           => 'Module Description',
			'jumpstart_desc'        => 'Jumpstart Description',
			'sort'                  => 'Sort Order',
			'recommendation_order'  => 'Recommendation Order',
			'introduced'            => 'First Introduced',
			'changed'               => 'Major Changes In',
			'deactivate'            => 'Deactivate',
			'free'                  => 'Free',
			'requires_connection'   => 'Requires Connection',
			'auto_activate'         => 'Auto Activate',
			'module_tags'           => 'Module Tags',
			'feature'               => 'Feature',
		);

		$file = Jetpack::get_module_path( Jetpack::get_module_slug( $module ) );

		$mod = Jetpack::get_file_data( $file, $headers );
		if ( empty( $mod['name'] ) ) {
			return false;
		}

		$mod['jumpstart_desc']          = _x( $mod['jumpstart_desc'], 'Jumpstart Description', 'jetpack' );
		$mod['name']                    = _x( $mod['name'], 'Module Name', 'jetpack' );
		$mod['description']             = _x( $mod['description'], 'Module Description', 'jetpack' );
		$mod['sort']                    = empty( $mod['sort'] ) ? 10 : (int) $mod['sort'];
		$mod['recommendation_order']    = empty( $mod['recommendation_order'] ) ? 20 : (int) $mod['recommendation_order'];
		$mod['deactivate']              = empty( $mod['deactivate'] );
		$mod['free']                    = empty( $mod['free'] );
		$mod['requires_connection']     = ( ! empty( $mod['requires_connection'] ) && 'No' == $mod['requires_connection'] ) ? false : true;

		if ( empty( $mod['auto_activate'] ) || ! in_array( strtolower( $mod['auto_activate'] ), array( 'yes', 'no', 'public' ) ) ) {
			$mod['auto_activate'] = 'No';
		} else {
			$mod['auto_activate'] = (string) $mod['auto_activate'];
		}

		if ( $mod['module_tags'] ) {
			$mod['module_tags'] = explode( ',', $mod['module_tags'] );
			$mod['module_tags'] = array_map( 'trim', $mod['module_tags'] );
			$mod['module_tags'] = array_map( array( __CLASS__, 'translate_module_tag' ), $mod['module_tags'] );
		} else {
			$mod['module_tags'] = array( self::translate_module_tag( 'Other' ) );
		}

		if ( $mod['feature'] ) {
			$mod['feature'] = explode( ',', $mod['feature'] );
			$mod['feature'] = array_map( 'trim', $mod['feature'] );
		} else {
			$mod['feature'] = array( self::translate_module_tag( 'Other' ) );
		}

		/**
		 * Filter the feature array on a module
		 *
		 * This filter allows you to control where each module is filtered: Recommended,
		 * Jumpstart, and the default "Other" listing.
		 *
		 * @since 3.5
		 *
		 * @param array   $mod['feature'] The areas to feature this module:
		 *     'Jumpstart' adds to the "Jumpstart" option to activate many modules at once
		 *     'Recommended' shows on the main Jetpack admin screen
		 *     'Other' should be the default if no other value is in the array
		 * @param string  $module The slug of the module, e.g. sharedaddy
		 * @param array   $mod All the currently assembled module data
		 */
		$mod['feature'] = apply_filters( 'jetpack_module_feature', $mod['feature'], $module, $mod );

		/**
		 * Filter the returned data about a module.
		 *
		 * This filter allows overriding any info about Jetpack modules. It is dangerous,
		 * so please be careful.
		 *
		 * @since 3.6
		 *
		 * @param array   $mod    The details of the requested module.
		 * @param string  $module The slug of the module, e.g. sharedaddy
		 * @param string  $file   The path to the module source file.
		 */
		return apply_filters( 'jetpack_get_module', $mod, $module, $file );
	}

	/**
	 * Loads the currently active modules.
	 */
	public static function load_modules() {
		require_once( JETPACK__PLUGIN_DIR . 'modules/module-extras.php' );
	}

	/**
	 * Returns the requested option.  Looks in jetpack_options or jetpack_$name as appropriate.
 	 *
	 * @param string $name    Option name
	 * @param mixed  $default (optional)
	 */
	public static function get_option( $name, $default = false ) {
		return Jetpack_Options::get_option( $name, $default );
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

	public static function is_module( $module ) {
		return ! empty( $module ) && ! validate_file( $module, Jetpack::get_available_modules() );
	}

	public static function deactivate_module( $module ) {
		do_action( 'jetpack_pre_deactivate_module', $module );

		$jetpack = Jetpack::init();

		$active = Jetpack::get_active_modules();
		$new    = array_filter( array_diff( $active, (array) $module ) );

		do_action( "jetpack_deactivate_module_$module", $module );

		// A flag for Jump Start so it's not shown again.
		if ( 'new_connection' === Jetpack_Options::get_option( 'jumpstart' ) ) {
			Jetpack_Options::update_option( 'jumpstart', 'jetpack_action_taken' );

			//Jump start is being dismissed send data to MC Stats
			$jetpack->stat( 'jumpstart', 'manual,deactivated-'.$module );

			$jetpack->do_stats( 'server_side' );
		}

		return Jetpack_Options::update_option( 'active_modules', array_unique( $new ) );
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

	/*
	 * A graceful transition to using Core's site icon.
	 *
	 * All of the hard work has already been done with the image
	 * in all_done_page(). All that needs to be done now is update
	 * the option and display proper messaging.
	 *
	 * @todo remove when WP 4.3 is minimum
	 *
	 * @since 3.6.1
	 *
	 * @return bool false = Core's icon not available || true = Core's icon is available
	 */
	public static function jetpack_site_icon_available_in_core() {
		global $wp_version;
		$core_icon_available = function_exists( 'has_site_icon' ) && version_compare( $wp_version, '4.3-beta' ) >= 0;

		if ( ! $core_icon_available ) {
			return false;
		}

		// No need for Jetpack's site icon anymore if core's is already set
		if ( has_site_icon() ) {
			if ( Jetpack::is_module_active( 'site-icon' ) ) {
				Jetpack::log( 'deactivate', 'site-icon' );
				Jetpack::deactivate_module( 'site-icon' );
			}
			return true;
		}

		// Transfer Jetpack's site icon to use core.
		$site_icon_id = Jetpack::get_option( 'site_icon_id' );
		if ( $site_icon_id ) {
			// Update core's site icon
			update_option( 'site_icon', $site_icon_id );

			// Delete Jetpack's icon option. We still want the blavatar and attached data though.
			delete_option( 'site_icon_id' );
		}

		// No need for Jetpack's site icon anymore
		if ( Jetpack::is_module_active( 'site-icon' ) ) {
			Jetpack::log( 'deactivate', 'site-icon' );
			Jetpack::deactivate_module( 'site-icon' );
		}

		return true;
	}
}
