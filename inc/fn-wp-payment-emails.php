<?php

	if ( !defined('ABSPATH') ) {
		header( 'Status: 403 Forbidden' );
		header( 'HTTP/1.1 403 Forbidden' );
		exit();
	}

	
	// action to bring us the data
	add_filter('edd_payment_row_actions','edd_pending_notification_row_actions',99,2);
	function edd_pending_notification_row_actions( $row_actions, $payment ){
		$button_show = false;
		if($payment->status=='pending'){
			$customer_id = edd_get_payment_customer_id( $payment->ID );
			$customer    = new EDD_Customer( $customer_id );
			if ( $customer->id > 0) {
				$row_actions['view_customer'] = '<a payment_id="'.$payment->ID.'" href="#" id="sendData" >'.__("Send Email","edd-pending-notification").'</a> <span style="display:none; color:black; font-weight:bold;" class="msj_span"></span>';
			}
		}
		return $row_actions;
	}


	add_action( 'wp_ajax_edd_pending_notification_email_payment',  'edd_pending_notification_email_payment_callback');
	function edd_pending_notification_email_payment_callback(){
			check_ajax_referer('edd_pending_notifcation_nonce' );
			$option  = get_option( 'edd-pending-notification');
			$content_temp = '';
			$subject = isset($option['edd_pending_notification_title'] ) ? $option['edd_pending_notification_title'] : '';
			$content = isset($option['edd_pending_notification_content'] ) ? $option['edd_pending_notification_content'] : '';
			//$content = explode(PHP_EOL . PHP_EOL,$content);
			$htmlcontent = apply_filters( 'the_content', $content);
			/*$htmlcontent = '';
			foreach($content as $line){
			    $htmlcontent .= '<p>' . str_replace(PHP_EOL, '<br />' , $line) . '</p>';
			}   
			$htmlcontent = str_replace('&nbsp;','<br>',$htmlcontent); 
			$htmlcontent = str_replace('\n','<br>',$htmlcontent);
			*/
			$content_temp = '';
			$customer_id = edd_get_payment_customer_id($_POST['payment_id']);
			$customer    = new EDD_Customer( $customer_id );
			//get user data
			$user_info   = edd_get_payment_meta_user_info($_POST['payment_id']);
			$email		 = $user_info['email'];
			//we replace the content of the tags with those of the user
			$content_temp = edd_pending_notification_replace_content($htmlcontent,$email,$_POST['payment_id']);
			//send email
			wp_mail($email, $subject, $content_temp, array( 'Content-Type: text/html; charset=UTF-8' ) );
			_e('Message sent succesfully','edd-pending-notification');
			wp_die();
	}


	//REPLACE ATTR CONTENT {ELEMTENT}
	function edd_pending_notification_replace_content($content,$email,$payment_id){
			// user data
			$mycontent = $content;
			$user = get_user_by( 'email', $email);
			$nick = $user->nickname;
			$first = $user->first_name;
			$last = $user->last_name;
			$birth = $user->user_birth;
			$list_order = '';

			$attr = '';
			$user = '';
			$content = explode('{',$content);
			for($i=0; $i<count($content);$i++){
				$content[$i] = explode('}',$content[$i]);
				for($j=0; $j<count($content[$i]);$j++){
					//search attr elements in {}
					$attr = $content[$i][$j];
					switch ($attr) {
						case 'user_email':
							$mycontent = str_replace('{user_email}',$email, $mycontent);
						break;
						case 'nickname':
							$mycontent = str_replace('{nickname}',$nick, $mycontent);
						break;
						case 'first_name':
							$mycontent = str_replace('{first_name}',$first, $mycontent);
						break;
						case 'last_name':
							$mycontent = str_replace('{last_name}',$last, $mycontent);
						break;
						case 'user_birth':
							$mycontent = str_replace('{user_birth}',$birth, $mycontent);
						break;
						case 'client_name':
							$mycontent = str_replace('{client_name}',$first, $mycontent);
						break;
						case 'product_name':
							$list_order = edd_pending_notification_details_order($payment_id);
							$mycontent = str_replace('{product_name}',$list_order, $mycontent);
						break;
					}
				}
			}
			return $mycontent;
	}
	// get the list of products
	function edd_pending_notification_details_order($payment_id){
		$downloads = edd_get_payment_meta_cart_details($payment_id, false );
		$list_payment = '';
		$list_payment.="<ul>";
		for($i=0; $i<count($downloads);$i++){
			$list_payment.='<li>';
			$list_payment.=$downloads[$i]['name'];
			$list_payment.='</li>';
		}
		$list_payment.="</ul>";
		return $list_payment;
	}
