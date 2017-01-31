<?php
/**
 * Plugin Name: Edd Pending Notifications
 * Plugin URI: http://www.netmdp.com
 * Description: Add a Send email quick action to all pending payments to easily remember the user about his pending order.
 * Version: 1.0
 * Author: etruel
 * Author URI: https://etruel.com
 * License: GPL2+
 * Text Domain: edd-notification
 * Domain Path: /languages/
 */


define( 'WP_EDDPAYEMTEMAILS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_EDDPAYMENTEMAILS_URL', plugin_dir_url( __FILE__ ) );
require WP_EDDPAYEMTEMAILS_DIR . 'inc/class-wp-payment-emails-composer.php';
require WP_EDDPAYEMTEMAILS_DIR . 'inc/fn-wp-payment-emails.php';
new WPEDDPaymentEmails_Composer;


?>