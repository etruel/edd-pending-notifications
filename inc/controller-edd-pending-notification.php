<?php

	if ( !defined('ABSPATH') ) {
		header( 'Status: 403 Forbidden' );
		header( 'HTTP/1.1 403 Forbidden' );
		exit();
	}

	add_action('admin_post_edd_pending_notification_action','edd_pending_notification_action_callback');
	function edd_pending_notification_action_callback(){
		//nonce referer
		check_admin_referer( 'edd-pending-notification-action', 'edd-pending-notification-nonce-field' );

		$option['edd_pending_notification_title'] = sanitize_text_field($_REQUEST['edd_pending_notification_title']);
		$option['edd_pending_notification_content'] = $_REQUEST['edd_pending_notification_content'];
		update_option('edd-pending-notification',$option);
   		wp_redirect(admin_url('admin.php?page=edd-pending-notification'));

	}

