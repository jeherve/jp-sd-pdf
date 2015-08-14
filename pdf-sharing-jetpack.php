<?php
/*
 * Plugin Name: PDF sharing button for Jetpack
 * Plugin URI: http://wordpress.org/plugins/pdf-sharing-jetpack/
 * Description: Add a PDF sharing button to the Jetpack Sharing module.
 * Author: Jeremy Herve
 * Version: 1.0
 * Author URI: http://jeremy.hu
 * License: GPL2+
 * Text Domain: pdf_sd_jp
 */

class Pdf_Sd_Button {
	private static $instance;

	static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new Pdf_Sd_Button;

		return self::$instance;
	}

	private function __construct() {
		$active_plugins = self::get_active_plugins();

		// Check if Jetpack and the sharing module is active, and if the "WP Post to PDF Enhanced" plugin is installed.
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'sharedaddy' ) && in_array( 'wp-post-to-pdf-enhanced/wp-post-to-pdf-enhanced.php', $active_plugins ) ) {
			add_action( 'plugins_loaded', array( $this, 'setup' ) );
			add_action( 'wp_print_styles', array( $this, 'get_css' ), 2 );
		} else {
			add_action( 'admin_notices',  array( $this, 'install_jetpack' ) );
		}
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

	public function setup() {
		add_filter( 'sharing_services', array( $this, 'inject_service' ) );
	}

	// Add the PDF Button to the list of services in Sharedaddy
	public function inject_service ( $services ) {
		include_once 'class.pdf-sharing-jetpack.php';
		if ( class_exists( 'Share_PDF' ) ) {
			$services['post-pdf'] = 'Share_PDF';
		}
		return $services;
	}

	// Prompt to install Jetpack
	public function install_jetpack() {
		echo '<div class="error"><p>';
		printf( __( 'To use the PDF Sharing plugin for Jetpack, you\'ll need to install and activate <a href="%1$s">Jetpack</a> first, and <a href="%2$s">activate the Sharing module</a>. Once you\'ve done so, install the <a href="%3$s">WP Post to PDF Enhanced plugin</a>.', 'pdf_sd_jp' ),
		'plugin-install.php?tab=search&s=jetpack&plugin-search-input=Search+Plugins',
		'admin.php?page=jetpack_modules',
		'plugin-install.php?tab=search&s=WP+Post+to+PDF+Enhanced&plugin-search-input=Search+Plugins'
		);
		echo '</p></div>';
	}

	public function get_css() {
		$icon_code = '\f440';
		$icon_css = "
		.sd-content ul li.share-post-pdf div.option.option-smart-off a:before,
		.sd-social-icon .sd-content ul li.share-post-pdf a:before,
		.sd-social-icon-text .sd-content li.share-post-pdf a:before,
		.sd-social-text .sd-content ul li.share-post-pdf a:before {
			content: '{$icon_code}';
		}
		";
		wp_add_inline_style( 'jetpack_css', $icon_css );
	}

}
// And boom.
Pdf_Sd_Button::get_instance();
