<?php
/*
Plugin Name: Slimpack
Plugin URI: http://sparanoid.com/work/slimpack/
Description: Slimpack — Lightweight Jetpack. Super-fast performance without modules that require contracting WordPress.com.
Version: 1.0.17
Author: Tunghsiao Liu
Author URI: http://sparanoid.com/
Author Email: t@sparanoid.com
Text Domain: slimpack
Domain Path: /languages/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Copyright 2016 Tunghsiao Liu (t@sparanoid.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	02110-1301	USA

*/

// Slimpack defines
define( 'SLIMPACK__BASE',              'slimpack/' );

// Jetpack defines
define( 'JETPACK__VERSION',            '3.8.2' );
define( 'JETPACK__PLUGIN_DIR',         plugin_dir_path( __FILE__ ) . SLIMPACK__BASE );
define( 'JETPACK__PLUGIN_FILE',        __FILE__ );

defined( 'JETPACK__GLOTPRESS_LOCALES_PATH' ) or define( 'JETPACK__GLOTPRESS_LOCALES_PATH', JETPACK__PLUGIN_DIR . 'locales.php' );

// Check if Jetpack is running
if ( class_exists( 'Jetpack' ) ) {
	trigger_error( sprintf( __( 'Jetpack is running! deactivate it and try again.', 'jetpack' ), 'WordPress.com Stats' ), E_USER_ERROR );
} else {
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack.php' );
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack-user-agent.php' );
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack-options.php' );

	require_once( JETPACK__PLUGIN_DIR . 'functions.compat.php' );

	require_once( JETPACK__PLUGIN_DIR . 'require-lib.php' );
}

register_activation_hook( __FILE__, 'slimpack_add_defaults' );
register_uninstall_hook( __FILE__, 'slimpack_delete_plugin_options' );
add_action( 'init', 'slimpack_i18n_init' );
add_action( 'admin_init', 'slimpack_init' );
add_action( 'admin_menu', 'slimpack_add_options_page' );
add_action( 'plugins_loaded', 'slimpack_conditions' );
add_filter( 'plugin_action_links', 'slimpack_plugin_action_links', 10, 2 );

// Slimpack
add_action( 'init', array( 'Jetpack', 'init' ) );
add_action( 'plugins_loaded', array( 'Jetpack', 'load_modules' ), 100 );
add_filter( 'is_jetpack_site', '__return_true' );

add_action( 'activated_plugin','slimpack_save_error' );
function slimpack_save_error(){
		update_option( 'sparanoid_plugin_error',	ob_get_contents() );
}

add_action( 'shutdown','slimpack_show_plugin_error' );
function slimpack_show_plugin_error(){
		echo get_option('plugin_error');
}

/**
 * Admin Options
 *
 * @since Slimpack 1.1.0
 */

// Delete plugin created table when plugin deleted
function slimpack_delete_plugin_options() {
	delete_option('slimpack_options');
}

// Default option settings
function slimpack_add_defaults() {

	$tmp = get_option('slimpack_options');

	if( ( (isset($tmp['chk_default_options_db']) && $tmp['G']=='1')) || (!is_array($tmp)) ) {
		$arr = array(
			"jp_carousel" => "1",
			"jp_contact_form" => "1",
			"jp_custom_css" => "1",
			"jp_custom_content_types" => "0",
			"jp_gravatar_hovercards" => "0",
			"jp_infinite_scroll" => "1",
			"jp_latex" => "0",
			"jp_markdown" => "1",
			"jp_omnisearch" => "0",
			"jp_sharedaddy" => "0",
			"jp_shortcodes" => "1",
			"jp_site_icon" => "1",
			"jp_verification_tools" => "1",
			"jp_widget_visibility" => "1",
			"jp_widgets" => "1"
		);
		update_option('slimpack_options', $arr);
	}
}

// Load the plugin text domain for translation
function slimpack_i18n_init() {
	load_plugin_textdomain( 'slimpack', false, dirname( plugin_basename( JETPACK__PLUGIN_FILE ) ) . '/languages/' );
}

// Initialized options to white list our options
function slimpack_init() {

	// Checks radio buttons have a valid choice (ie. no section is blank)
	// Primarily to check newly added options have correct initial values
	$tmp = get_option('slimpack_options');
	update_option('slimpack_options', $tmp);

	// Register settings
	register_setting( 'slimpack_plugin_options', 'slimpack_options' );
}

// Menu page
function slimpack_add_options_page() {
	add_options_page(
		__( 'Slimpack', 'slimpack' ),
		__( 'Slimpack', 'slimpack' ),
		'manage_options', 'slimpack', 'slimpack_render_form'
	);
}

// Menu page content
function slimpack_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e( 'Slimpack Options', 'slimpack' ); ?></h2>
		<!-- <h3>Slimpack Options</h3> -->
		<p><?php _e( 'Slimpack — Lightweight Jetpack. Super-fast performance without modules that require contracting WordPress.com.', 'slimpack' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields('slimpack_plugin_options'); ?>
			<?php $options = get_option('slimpack_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Active Jetpack Modules', 'slimpack' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="slimpack_options[jp_carousel]" type="checkbox" value="1" <?php if (isset($options['jp_carousel'])) { checked('1', $options['jp_carousel']); } ?>>
								<?php _e( 'Carousel', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_contact_form]" type="checkbox" value="1" <?php if (isset($options['jp_contact_form'])) { checked('1', $options['jp_contact_form']); } ?>>
								<?php _e( 'Contact Form', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_custom_css]" type="checkbox" value="1" <?php if (isset($options['jp_custom_css'])) { checked('1', $options['jp_custom_css']); } ?>>
								<?php _e( 'Custom CSS', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_custom_content_types]" type="checkbox" value="1" <?php if (isset($options['jp_custom_content_types'])) { checked('1', $options['jp_custom_content_types']); } ?>>
								<?php _e( 'Custom Content Types', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_gravatar_hovercards]" type="checkbox" value="1" <?php if (isset($options['jp_gravatar_hovercards'])) { checked('1', $options['jp_gravatar_hovercards']); } ?>>
								<?php _e( 'Gravatar Hovercards', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_infinite_scroll]" type="checkbox" value="1" <?php if (isset($options['jp_infinite_scroll'])) { checked('1', $options['jp_infinite_scroll']); } ?>>
								<?php _e( 'Infinite Scroll', 'slimpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_latex]" type="checkbox" value="1" <?php if (isset($options['jp_latex'])) { checked('1', $options['jp_latex']); } ?>>
								<?php _e( 'Beautiful Math', 'slimpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_markdown]" type="checkbox" value="1" <?php if (isset($options['jp_markdown'])) { checked('1', $options['jp_markdown']); } ?>>
								<?php _e( 'Markdown', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_omnisearch]" type="checkbox" value="1" <?php if (isset($options['jp_omnisearch'])) { checked('1', $options['jp_omnisearch']); } ?>>
								<?php _e( 'Omnisearch', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_sharedaddy]" type="checkbox" value="1" <?php if (isset($options['jp_sharedaddy'])) { checked('1', $options['jp_sharedaddy']); } ?>>
								<?php _e( 'Sharing', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_shortcodes]" type="checkbox" value="1" <?php if (isset($options['jp_shortcodes'])) { checked('1', $options['jp_shortcodes']); } ?>>
								<?php _e( 'Shortcode Embeds', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_site_icon]" type="checkbox" value="1" <?php if (isset($options['jp_site_icon'])) { checked('1', $options['jp_site_icon']); } ?>>
								<?php _e( 'Site Icon', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_verification_tools]" type="checkbox" value="1" <?php if (isset($options['jp_verification_tools'])) { checked('1', $options['jp_verification_tools']); } ?>>
								<?php _e( 'Site Verification', 'slimpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_widget_visibility]" type="checkbox" value="1" <?php if (isset($options['jp_widget_visibility'])) { checked('1', $options['jp_widget_visibility']); } ?>>
								<?php _e( 'Widget Visibility', 'slimpack' ); ?>
							</label><br>

							<label>
								<input name="slimpack_options[jp_widgets]" type="checkbox" value="1" <?php if (isset($options['jp_widgets'])) { checked('1', $options['jp_widgets']); } ?>>
								<?php _e( 'Widgets', 'slimpack' ); ?>
							</label><br>
						</fieldset>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
			</p>
			<hr>
			<p><?php _e( 'Love this plugin? Please consider', 'slimpack' ); ?> <a href="http://sparanoid.com/donate/"><?php _e( 'buying me a cup of coffee!', 'slimpack' ); ?></a></p>
			<p>
				<input class="button" type="button" value="<?php _e( 'Follow on Twitter', 'slimpack' ); ?>" onClick="window.open('http://twitter.com/tunghsiao')">
				<input class="button" type="button" value="<?php _e( 'Visit My Website', 'slimpack' ); ?>" onClick="window.open('http://sparanoid.com/')">
				<input class="button" type="button" value="<?php _e( 'View plugin at WordPress.org', 'slimpack' ); ?>" onClick="window.open('https://wordpress.org/plugins/slimpack/')">
			</p>
		</form>
	</div>
	<?php
}

/**
 * Plugin Functions
 *
 * @since Slimpack 1.1.0
 */

function slimpack_conditions() {

	$tmp = get_option('slimpack_options');

	if (isset($tmp['jp_carousel'])) {
		if($tmp['jp_carousel']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/carousel.php' );
		}
	}

	if (isset($tmp['jp_contact_form'])) {
		if($tmp['jp_contact_form']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/contact-form.php' );
		}
	}

	if (isset($tmp['jp_custom_css'])) {
		if($tmp['jp_custom_css']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/custom-css.php' );
		}
	}

	if (isset($tmp['jp_custom_content_types'])) {
		if($tmp['jp_custom_content_types']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/custom-content-types.php' );
		}
	}

	if (isset($tmp['jp_gravatar_hovercards'])) {
		if($tmp['jp_custom_css']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/gravatar-hovercards.php' );
			// SLIMPACK: jetpack_modules_loaded is defined in videopress.php
			// Since we don't have it, just init hovercards right after gravatar-hovercards module loaded.
			add_action( 'init', 'grofiles_hovercards_init' );
		}
	}

	if (isset($tmp['jp_infinite_scroll'])) {
		if($tmp['jp_infinite_scroll']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/infinite-scroll.php' );
		}
	}

	if (isset($tmp['jp_latex'])) {
		if($tmp['jp_latex']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/latex.php' );
		}
	}

	if (isset($tmp['jp_markdown'])) {
		if($tmp['jp_markdown']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/markdown.php' );
		}
	}

	if (isset($tmp['jp_omnisearch'])) {
		if($tmp['jp_omnisearch']=='1'){
			// SLIMPACK: icon hotfix
			require_once( JETPACK__PLUGIN_DIR . '_inc/genericons.php' );
			require_once( JETPACK__PLUGIN_DIR . 'modules/omnisearch.php' );
			jetpack_register_genericons();
		}
	}

	if (isset($tmp['jp_sharedaddy'])) {
		if($tmp['jp_sharedaddy']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/sharedaddy.php' );
		}
	}

	if (isset($tmp['jp_shortcodes'])) {
		if($tmp['jp_shortcodes']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/shortcodes.php' );
		}
	}

	if (isset($tmp['jp_site_icon'])) {
		if($tmp['jp_site_icon']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/site-icon.php' );
		}
	}

	if (isset($tmp['jp_verification_tools'])) {
		if($tmp['jp_verification_tools']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/verification-tools.php' );
		}
	}

	if (isset($tmp['jp_widget_visibility'])) {
		if($tmp['jp_widget_visibility']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/widget-visibility.php' );
		}
	}

	if (isset($tmp['jp_widgets'])) {
		if($tmp['jp_widgets']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/widgets.php' );
		}
	}
}

// Add a 'Settings' link on Plugins page
function slimpack_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="'.get_admin_url().'options-general.php?page=slimpack">'.__('Settings').'</a>';
		// Make sure the 'Settings' link at first
		array_unshift( $links, $posk_links );
	}

	return $links;
}
