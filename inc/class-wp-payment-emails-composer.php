<?php

	if ( !defined('ABSPATH') ) {
		header( 'Status: 403 Forbidden' );
		header( 'HTTP/1.1 403 Forbidden' );
		exit();
	}


	add_action( 'admin_menu','edd_pending_notification_add_menu' );
	function edd_pending_notification_add_menu() {
		add_menu_page(
			__( 'Edd Pending Email Template', 'edd-pending-notification' ),
			__( 'Edd Pending Email Template', 'edd-pending-notification' ),
			'manage_options',
			'edd-pending-notification',
			'edd_pending_notification_form',
			EDD_PENDING_NOTIFICATION_URL . '/images/mail.png'
		);
	}
	function edd_pending_notification_form() {
		$option = get_option('edd-pending-notification');
			$edd_pending_notification_title = isset($option['edd_pending_notification_title'] ) ? $option['edd_pending_notification_title'] : '';
			$edd_pending_notification_content = isset($option['edd_pending_notification_content'] ) ? $option['edd_pending_notification_content'] : '';

		?>
		<div class="wrap">
			<form method="POST" action="<?php echo admin_url( 'admin-post.php' ); ?>">
			
				<?php wp_nonce_field( 'edd-pending-notification-action', 'edd-pending-notification-nonce-field' ); ?>
			 
				<input type="hidden" name="action" value="edd_pending_notification_action">
				<label style="font-size: 20px; font-weight: bold;">
				<?php  _e('Title','edd-pending-notification'); ?>
				</label>
				<br>
				<input type="text" name="edd_pending_notification_title" style="width:100%;" value="<?php echo $edd_pending_notification_title; ?>">
				<br>
				<br>
				<?php
					wp_editor($edd_pending_notification_content, 'edd_pending_notification_content');
				 ?>
				 <br><strong><?php _e('Allowed labels','edd-pending-notification'); ?>:</strong><br>
				<p id="emailtags"><span>{first_name}</span>  <span>{last_name}</span> <span>{nickname}</span><span>{user_email}</span>  <span>{client_name}</span><span>{product_name}</span></p>
				<input type="submit" name="save" value="<?php _e('Save','edd-pending-notification'); ?>" class="button button-primary">
			</form>
		</div>
		<?php
	}
?>