<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    registration
 * @subpackage registration/includes
 */

/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    registration
 * @subpackage registration/includes
 * @author     Your Name <email@example.com>
 */
class registration_Activator
{

    private $db;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

    }

    public function activate()
    {
        // get sql content
        $sqlFilePath = plugin_dir_path( __FILE__ ) . 'crazycross.sql';
        $sqlFileContent = file_get_contents($sqlFilePath);

        // require dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // exec query
        dbDelta($sqlFileContent);

        $this->db->insert(
            'cc_settings',
            array(
                'name' => 'crazy_cross_date',
                'value' => date('Y-m-d')
            )
        );

        $this->db->insert(
            'cc_settings',
            array(
                'name' => 'mollie_key_bergeijk',
                'value' => 'live_36AAFWFSkhtaWahMwKVFhD9267u8P2'
            )
        );
    }
}
