<?php
/**
 * @package OnzAuth
 * Plugin Name: OnzAuth
 * Plugin URI: https://github.com/zailky/wp-onzauth
 * Description: OnzAuth provides passwordless and biometric login for your WordPress site.
 * Version:     1.0.6
 * Author:      OnzAuth
 * Author URI:  https://tryonzauth.com/
 * License: GPLv2 or later
 * Text Domain: wp-onzauth
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

/**
 * Main OnzAuth Class
 *
 * The init class that runs the OnzAuth plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 */
class OnzAuth
{
    /**
     * Plugin Version
     *
     * @since 1.0.0
     * @var string The plugin version.
     */
    const VERSION = '1.0.6';

    /**
     * Minimum PHP Version
     *
     * @since 7.3.0
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.3';

    /**
     * Constructor
     *
     * @since 0.0.0
     * @access public
     */
    public function __construct()
    {
        // Load the translation.
        // add_action('init', array($this, 'i18n'));

        // Initialize the plugin.
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Initialize the plugin
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 0.0.0
     * @access public
     */
    public function init()
    {
        // Once we get here, We have passed all validation checks so we can safely include our widgets.
        require_once 'includes/class-configurations.php';
        if (is_admin()) {
            $basename = plugin_basename(__FILE__);
            new OnzAuth_Configurations( $basename );
        }

        require_once 'vendor/autoload.php';
        require_once 'includes/class-login.php';
        new OnzAuth_Login();
    }
}

// Instantiate OnzAuth.
new OnzAuth();