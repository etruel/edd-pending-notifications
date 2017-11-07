<?php
/**
 * Scripts
 *
 * @package     edd-pending-notification\PluginName\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      array $edd-pending-notification_settings_page The slug for the edd-pending-notification settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_pending_notifcation_admin_scripts( $hook ) {
    $act_page = isset($_GET['page']) ? $_GET['page']:'';
    if( $act_page == 'edd-payment-history') {
          //styles
        wp_enqueue_style( 'edd_pending_notifcation_admin_css', EDD_PENDING_NOTIFICATION_URL . '/assets/css/admin.css' );
        //scripts
        wp_enqueue_script( 'edd_pending_notifcation_admin_js', EDD_PENDING_NOTIFICATION_URL . '/assets/js/admin.js', array( 'jquery' ) );
        wp_localize_script( 'edd_pending_notifcation_admin_js', 'edd_pending_object',
        array(
            'nonce' => wp_create_nonce('edd_pending_notifcation_nonce'),
            'plugin_url' => plugin_dir_url( dirname( __FILE__ )),
            'ajax_url'=> admin_url( 'admin-ajax.php' ),
            'text_send_email' =>__('Send Email','edd-pending-notification')
          )
        );
      
    }
}
add_action( 'admin_enqueue_scripts', 'edd_pending_notifcation_admin_scripts', 100 );
