<?php
/**
 * Plugin Name: Edd Pending Notification
 * Plugin URI: http://www.netmdp.com
 * Description: Add a Send email quick action to all pending payments to easily remember the user about his pending order.
 * Version: 1.0
 * Author: etruel
 * Author URI: https://etruel.com
 * License: GPL2+
 * Text Domain: edd-pending-notification
 * Domain Path: /languages/
 *
 *
 * @package         etruel\edd_pending_notification Stats
 * @author          Esteban Truelsegaard
 * @copyright       Copyright (c) 2016
 *
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'edd_pending_notification' ) ) {

    // Plugin version
    if(!defined('EDD_PENDING_NOTIFICATION_VER')) {
        define('EDD_PENDING_NOTIFICATION_VER', '1.0' );
    }
    
    /**
     * Main edd_pending_notification class
     *
     * @since       1.0.0
     */
    class edd_pending_notification {

        /**
         * @var         edd_pending_notification $instance The one true edd_pending_notification
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true edd_pending_notification
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
       public static function setup_constants() {
            // Plugin root file
            if(!defined('EDD_PENDING_NOTIFICATION_ROOT_FILE')) {
                define('EDD_PENDING_NOTIFICATION_ROOT_FILE', __FILE__ );
            }
            // Plugin path
            if(!defined('EDD_PENDING_NOTIFICATION_DIR')) {
                define('EDD_PENDING_NOTIFICATION_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin URL
            if(!defined('EDD_PENDING_NOTIFICATION_URL')) {
                define('EDD_PENDING_NOTIFICATION_URL', plugin_dir_url( __FILE__ ) );
            }
            if(!defined('EDD_PENDING_NOTIFICATION_STORE_URL')) {
                define('EDD_PENDING_NOTIFICATION_STORE_URL', 'https://etruel.com'); 
            } 
            if(!defined('EDD_PENDING_NOTIFICATION_ITEM_NAME')) {
                define('EDD_PENDING_NOTIFICATION_ITEM_NAME', 'edd_pending_notification'); 
            } 
        }


        /**
         * Include necessary files
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public static function includes() {
            // Include scripts
            require_once EDD_PENDING_NOTIFICATION_DIR . 'inc/script.php';
            require_once EDD_PENDING_NOTIFICATION_DIR . 'inc/controller-edd-pending-notification.php';
            require_once EDD_PENDING_NOTIFICATION_DIR . 'inc/class-wp-payment-emails-composer.php';
            require_once EDD_PENDING_NOTIFICATION_DIR . 'inc/fn-wp-payment-emails.php';
   
        }
             /**
         * Run action and filter hooks
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         *
         */
         public static function hooks() {
            // Register settings
        }
        

        
        public static function add_updater($args) {
            if (empty($args['edd_pending_notification'])) {
                $args['edd_pending_notification'] = array();
                $args['edd_pending_notification']['api_url'] = EDD_PENDING_NOTIFICATION_STORE_URL;
                $args['edd_pending_notification']['plugin_file'] = EDD_PENDING_NOTIFICATION_ROOT_FILE;
                $args['edd_pending_notification']['api_data'] = array(
                                                        'version'   => EDD_PENDING_NOTIFICATION_VER,                 // current version number
                                                        'item_name' => EDD_PENDING_NOTIFICATION_ITEM_NAME,   // name of this plugin
                                                        'author'    => 'Esteban Truelsegaard'  // author of this plugin
                                                    );
                    
            }
            return $args;
        }
        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_PENDING_NOTIFICATION_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_pending_notification_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd_pending_notification' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd_pending_notification', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd_pending_notification/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd_pending_notification/ folder
                load_textdomain( 'edd_pending_notification', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd_pending_notification/languages/ folder
                load_textdomain( 'edd_pending_notification', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd_pending_notification', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public static function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'edd_pending_notification_settings',
                    'name'  => '<strong>' . __( 'Plugin Name Settings', 'edd_pending_notification' ) . '</strong>',
                    'desc'  => __( 'Configure Plugin Name Settings', 'edd_pending_notification' ),
                    'type'  => 'header',
                )
            );

            return array_merge( $settings, $new_settings );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true edd_pending_notification
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \edd_pending_notification The one true edd_pending_notification
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */


function edd_pending_notification_load() {
     if(!class_exists( 'Easy_Digital_Downloads' ) ) {
         require_once 'inc/class.extension-activation.php';
         $activation = new edd_pending_notification_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
         $activation = $activation->run();

    }else {
        return edd_pending_notification::instance(); 
    }
}
add_action( 'plugins_loaded', 'edd_pending_notification_load', 999);



/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function edd_pending_notification_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'edd_pending_notification_activation' );
