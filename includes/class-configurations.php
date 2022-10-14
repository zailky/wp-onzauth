<?php

/**
 * Configurations class.
 *
 * @category   Class
 * @package    OnzAuth
 * @subpackage WordPress
 * @author     OnzAuth <support@onzauth.com>
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link
 * @since      0.0.0
 * php version 7.3.9
 */

class OnzAuth_Configurations
{
    private $onzauth_options;

    public function __construct( $basename )
    {
        // Load options template.
        add_action('admin_menu', array($this, 'add_submenu'));

        // Add settings link
        add_filter("plugin_action_links_$basename", array($this, 'add_settings_link'));

        // Initialize options page.
        add_action('admin_init', array($this, 'settings_page_init'));
    }

    /**
     * Add options page menu.
     *
     * @since 0.0.0
     * @access public
     */
    public function add_submenu()
    {
        add_options_page(
            'OnzAuth Config', // page_title
            'OnzAuth Config', // menu_title
            'manage_options', // capability
            'onzauth', // menu_slug
            array($this, 'create_settings_page') // function
        );
    }

    /**
     * Add settings link in plugin.
     *
     * @since 0.0.0
     * @access public
     */
    public function add_settings_link( $links )
    {
        $settings = array(
            '<a href="' . admin_url( 'options-general.php?page=onzauth' ) . '">Settings</a>',
        );
        $links = array_merge( $links, $settings );
        return $links;
    }

    /**
     * Options page template.
     *
     * @since 0.0.0
     * @access public
     */
    public function create_settings_page()
    {
        $this->onzauth_options = get_option('onzauth_option_name');?>

        <div class="wrap">
          <h2>OnzAuth Passwordless Authentication Configurations</h2>
          <p></p>
          <?php settings_errors();?>

          <form method="post" action="options.php">
            <?php
                settings_fields('onzauth_option_group');
                do_settings_sections('onzauth-config');
                submit_button();
            ?>
          </form>
        </div>
        <?php
    }

    /**
     * Register settings.
     *
     * @since 0.0.0
     * @access public
     */
    public function settings_page_init()
    {
        register_setting(
            'onzauth_option_group', // option_group
            'onzauth_option_name', // option_name
            array($this, 'sanitise_callback') // sanitize_callback
        );

        add_settings_section(
            'onzauth_setting_section', // id
            'Settings', // title
            array($this, 'section_info'), // callback
            'onzauth-config' // page
        );

        add_settings_field(
            'client_id', // id
            'Client ID', // title
            array($this, 'client_id_callback'), // callback
            'onzauth-config', // page
            'onzauth_setting_section' // section
        );

        add_settings_field(
            'redirect_uri', // id
            'Redirect URI (Optional)', // title
            array($this, 'redirect_uri_callback'), // callback
            'onzauth-config', // page
            'onzauth_setting_section' // section
        );

        add_settings_field(
            'user_role', // id
            'User Role', // title
            array($this, 'user_role_callback'), // callback
            'onzauth-config', // page
            'onzauth_setting_section' // section
        );

        add_settings_field(
            'admin_login', // id
            'Administration Login', // title
            array($this, 'admin_login_callback'), // callback
            'onzauth-config', // page
            'onzauth_setting_section' // section
        );

        add_settings_field(
            'wc_login', // id
            'WooCommerce Login', // title
            array($this, 'wc_login_callback'), // callback
            'onzauth-config', // page
            'onzauth_setting_section' // section
        );
    }

    /**
     * Sanitize options.
     *
     * @since 0.0.0
     * @access public
     */
    public function sanitise_callback($input)
    {
        $sanitary_values = array();
        if (isset($input['client_id'])) {
            $sanitary_values['client_id'] = sanitize_text_field($input['client_id']);
        }

        if (isset($input['redirect_uri'])) {
            $sanitary_values['redirect_uri'] = sanitize_text_field($input['redirect_uri']);
        }

        if (isset($input['user_role'])) {
            $sanitary_values['user_role'] = sanitize_text_field($input['user_role']);
        }

        if (isset($input['admin_login'])) {
            $sanitary_values['admin_login'] = sanitize_text_field($input['admin_login']);
        }

        if (isset($input['wc_login'])) {
            $sanitary_values['wc_login'] = sanitize_text_field($input['wc_login']);
        }

        return $sanitary_values;
    }

    /**
     * Section template.
     *
     * @since 0.0.0
     * @access public
     */
    public function section_info()
    {
    }

    /**
     * Option: Client ID Callback.
     *
     * @since 0.0.0
     * @access public
     */
    public function client_id_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="onzauth_option_name[client_id]" id="client_id" value="%s">
            <p class="description">' . esc_html__('OnzAuth Client ID', 'onzauth') . '</p>',
            isset($this->onzauth_options['client_id']) ? esc_attr($this->onzauth_options['client_id']) : ''
        );
    }

    /**
     * Option: Redirect URI Callback.
     *
     * @since 0.0.0
     * @access public
     */
    public function redirect_uri_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="onzauth_option_name[redirect_uri]" id="redirect_uri" value="%s">
            <p class="description">' . esc_html__('Redirect user to this page after successful authentication. (Optional)', 'onzauth') . '</p>',
            isset($this->onzauth_options['redirect_uri']) ? esc_attr($this->onzauth_options['redirect_uri']) : ''
        );
    }

    /**
     * Option: User role Callback.
     *
     * @since 0.0.0
     * @access public
     */
    public function user_role_callback()
    {
        global $wp_roles;
        
        $allowed_html = array(
            'option' => array(
                'value' => array(),
            )
        );

        $roles = $wp_roles->roles;

        if (!empty($roles)) {
            $options = '';
            foreach ($roles as $id => $role) {
                $selected = isset($this->onzauth_options['user_role']) ? selected($id, $this->onzauth_options['user_role'], false) : '';
                $options .= '<option value="' . esc_attr($id) . '" ' . esc_attr($selected) . '>' . esc_html__($role["name"]) . '</option>';
            }
            
            echo '<select name="onzauth_option_name[user_role]" id="user_role">' . wp_kses($options, $allowed_html) . '</select>
                <p class="description">' . esc_html__('Default role to users registered by OnzAuth.', 'onzauth') . '</p>';
        }

    }

    /**
     * Option: Admin login Callback.
     *
     * @since 0.0.0
     * @access public
     */
    public function admin_login_callback()
    {
        $checked = isset($this->onzauth_options['admin_login']) ? checked($this->onzauth_options['admin_login'], true, false) : '';
        echo '<input class="regular-text" type="checkbox" name="onzauth_option_name[admin_login]" value="1" id="admin_login" ' . $checked . '>
            <p class="description">' . esc_html__('Change admin login form to use OnzAuth', 'onzauth') . '</p>';

    }

    /**
     * Option: WooCommerce login Callback.
     *
     * @since 0.0.0
     * @access public
     */
    public function wc_login_callback()
    {
        $checked = isset($this->onzauth_options['wc_login']) ? checked($this->onzauth_options['wc_login'], true, false) : '';
        echo '<input class="regular-text" type="checkbox" name="onzauth_option_name[wc_login]" value="1" id="wc_login" ' . $checked . '>
            <p class="description">' . esc_html__('Change WooCommerce login form to use OnzAuth', 'onzauth') . '</p>';

    }
}
