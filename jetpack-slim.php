<?php
/*
Plugin Name: Jetpack Slim
Plugin URI: http://sparanoid.com/work/jetpack-slim/
Description: Jetpack Slim — Use Jetpack without internet connection (no debug mode required). Super-fast performance without contracting to Jetpack server.
Version: 1.0.0
Author: Tunghsiao Liu
Author URI: http://sparanoid.com/
Author Email: t@sparanoid.com
Text Domain: jetpack-slim
Domain Path: /languages/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Copyright 2014 Tunghsiao Liu (t@sparanoid.com)

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

define( 'JETPACK_SLIM__BASE',					'jetpack-slim/' );
define( 'JETPACK__PLUGIN_DIR',				 plugin_dir_path( __FILE__ ) . JETPACK_SLIM__BASE );
define( 'JETPACK__PLUGIN_FILE',				__FILE__ );

if ( !class_exists( 'Jetpack' ) ) {
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack.php' );
}

if ( !function_exists( 'jetpack_is_mobile' ) ) {
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack-user-agent.php' );
}

if ( !class_exists( 'Jetpack_Options' ) ) {
	require_once( JETPACK__PLUGIN_DIR . 'class.jetpack-options.php' );
}

if ( !function_exists( 'jetpack_require_lib' ) ) {
	require_once( JETPACK__PLUGIN_DIR . 'require-lib.php' );
}

register_activation_hook( __FILE__, 'jtpkslm_add_defaults' );
register_uninstall_hook( __FILE__, 'jtpkslm_delete_plugin_options' );
add_action( 'init', 'jtpkslm_i18n_init' );
add_action( 'admin_init', 'jtpkslm_init' );
add_action( 'admin_menu', 'jtpkslm_add_options_page' );
add_action( 'plugins_loaded', 'jtpkslm_conditions' );
add_filter( 'plugin_action_links', 'jtpkslm_plugin_action_links', 10, 2 );

// Jetpack Slim
add_action( 'init', array( 'Jetpack', 'init' ) );
add_action( 'plugins_loaded', array( 'Jetpack', 'load_modules' ), 100 );

add_action( 'activated_plugin','jtpkslm_save_error' );
function jtpkslm_save_error(){
		update_option( 'sparanoid_plugin_error',	ob_get_contents() );
}

add_action( 'shutdown','jtpkslm_show_plugin_error' );
function jtpkslm_show_plugin_error(){
		echo get_option('plugin_error');
}

/**
 * Admin Options
 *
 * @since Jetpack Slim 1.1.0
 */

// Delete plugin created table when plugin deleted
function jtpkslm_delete_plugin_options() {
	delete_option('jtpkslm_options');
}

// Default option settings
function jtpkslm_add_defaults() {

	$tmp = get_option('jtpkslm_options');

	if( ( (isset($tmp['chk_default_options_db']) && $tmp['G']=='1')) || (!is_array($tmp)) ) {
		$arr = array(
			"jp_carousel" => "1",
			"jp_contact_form" => "1",
			"jp_custom_css" => "1",
			"jp_infinite_scroll" => "1",
			"jp_latex" => "0",
			"jp_markdown" => "1",
			"jp_shortcodes" => "1",
			"jp_site_icon" => "1",
			"jp_verification_tools" => "1",
			"jp_widget_visibility" => "1",
			"jp_widgets" => "1",
			"radio_strict_filtering" => "strict_on"
		);
		update_option('jtpkslm_options', $arr);
	}
}

// Load the plugin text domain for translation
function jtpkslm_i18n_init() {
	load_plugin_textdomain( 'jetpack-slim', false, dirname( plugin_basename( JETPACK__PLUGIN_FILE ) ) . '/languages/' );
}

// Initialized options to white list our options
function jtpkslm_init() {

	// Checks radio buttons have a valid choice (ie. no section is blank)
	// Primarily to check newly added options have correct initial values
	$tmp = get_option('jtpkslm_options');

	// Check strict filtering option has a starting value
	if(!$tmp['radio_strict_filtering']) {
		$tmp["radio_strict_filtering"] = "strict_off";
		update_option('jtpkslm_options', $tmp);
	}

	// Register settings
	register_setting( 'jtpkslm_plugin_options', 'jtpkslm_options' );
}

// Menu page
function jtpkslm_add_options_page() {
	add_options_page(
		__( 'Jetpack Slim', 'jetpack-slim' ),
		__( 'Jetpack Slim', 'jetpack-slim' ),
		'manage_options', __FILE__, 'jtpkslm_render_form'
	);
}

// Menu page content
function jtpkslm_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e( 'Jetpack Slim Options', 'jetpack-slim' ); ?></h2>
		<!-- <h3>Jetpack Slim Options</h3> -->
		<p><?php _e( 'Jetpack Slim — Use Jetpack without internet connection (no debug mode required). Super-fast performance without contracting to Jetpack server.', 'jetpack-slim' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields('jtpkslm_plugin_options'); ?>
			<?php $options = get_option('jtpkslm_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Active Jetpack Modules', 'jetpack-slim' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="jtpkslm_options[jp_carousel]" type="checkbox" value="1" <?php if (isset($options['jp_carousel'])) { checked('1', $options['jp_carousel']); } ?>>
								<?php _e( 'Carousel', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_contact_form]" type="checkbox" value="1" <?php if (isset($options['jp_contact_form'])) { checked('1', $options['jp_contact_form']); } ?>>
								<?php _e( 'Contact Form', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_custom_css]" type="checkbox" value="1" <?php if (isset($options['jp_custom_css'])) { checked('1', $options['jp_custom_css']); } ?>>
								<?php _e( 'Custom CSS', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_infinite_scroll]" type="checkbox" value="1" <?php if (isset($options['jp_infinite_scroll'])) { checked('1', $options['jp_infinite_scroll']); } ?>>
								<?php _e( 'Infinite Scroll', 'jetpack-slim' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_latex]" type="checkbox" value="1" <?php if (isset($options['jp_latex'])) { checked('1', $options['jp_latex']); } ?>>
								<?php _e( 'Beautiful Math', 'jetpack-slim' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_markdown]" type="checkbox" value="1" <?php if (isset($options['jp_markdown'])) { checked('1', $options['jp_markdown']); } ?>>
								<?php _e( 'Markdown', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_shortcodes]" type="checkbox" value="1" <?php if (isset($options['jp_shortcodes'])) { checked('1', $options['jp_shortcodes']); } ?>>
								<?php _e( 'Shortcode Embeds', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_site_icon]" type="checkbox" value="1" <?php if (isset($options['jp_site_icon'])) { checked('1', $options['jp_site_icon']); } ?>>
								<?php _e( 'Site Icon', 'jetpack' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_verification_tools]" type="checkbox" value="1" <?php if (isset($options['jp_verification_tools'])) { checked('1', $options['jp_verification_tools']); } ?>>
								<?php _e( 'Site Verification', 'jetpack-slim' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_widget_visibility]" type="checkbox" value="1" <?php if (isset($options['jp_widget_visibility'])) { checked('1', $options['jp_widget_visibility']); } ?>>
								<?php _e( 'Widget Visibility', 'jetpack-slim' ); ?>
							</label><br>

							<label>
								<input name="jtpkslm_options[jp_widgets]" type="checkbox" value="1" <?php if (isset($options['jp_widgets'])) { checked('1', $options['jp_widgets']); } ?>>
								<?php _e( 'Widgets', 'jetpack-slim' ); ?>
							</label><br>
						</fieldset>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
			</p>
			<hr>
			<p><?php _e( 'Love this plugin? Please consider', 'jetpack-slim' ); ?> <a href="http://sparanoid.com/donate/"><?php _e( 'buying me a cup of coffee!', 'jetpack-slim' ); ?></a></p>
			<p>
				<input class="button" type="button" value="<?php _e( 'Follow on Twitter', 'jetpack-slim' ); ?>" onClick="window.open('http://twitter.com/tunghsiao')">
				<input class="button" type="button" value="<?php _e( 'Visit My Website', 'jetpack-slim' ); ?>" onClick="window.open('http://sparanoid.com/')">
				<input class="button" type="button" value="<?php _e( 'View plugin at WordPress.org', 'jetpack-slim' ); ?>" onClick="window.open('https://wordpress.org/plugins/jetpack-slim/')">
			</p>
		</form>
	</div>
	<?php
}

/**
 * Plugin Functions
 *
 * @since Jetpack Slim 1.1.0
 */

function jtpkslm_conditions() {

	$tmp = get_option('jtpkslm_options');

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

	if (isset($tmp['jp_infinite_scroll'])) {
		if($tmp['jp_infinite_scroll']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/infinite-scroll.php' );
		}
	}

	if (isset($tmp['jp_latex'])) {
		if($tmp['jp_infinite_scroll']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/latex.php' );
		}
	}

	if (isset($tmp['jp_markdown'])) {
		if($tmp['jp_markdown']=='1'){
			require_once( JETPACK__PLUGIN_DIR . 'modules/markdown.php' );
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
function jtpkslm_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="'.get_admin_url().'options-general.php?page=jetpack-slim/jetpack-slim.php">'.__('Settings').'</a>';
		// Make sure the 'Settings' link at first
		array_unshift( $links, $posk_links );
	}

	return $links;
}
