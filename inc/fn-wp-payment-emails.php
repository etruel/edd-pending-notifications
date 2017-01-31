<?php
	

//convertir en clase
add_filter('edd_payment_row_actions','payment_row_actions',99,2);
add_action( 'wp_ajax_email_payment',  'email_payment_callback');
add_action( 'admin_footer','reparaciones_load_scripts' );



//obtener la lista de productos
function details_order($payment_id){
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


//REPLACE ATTR CONTENT {ELEMTENT}
function replace_content($content,$email,$payment_id){

		//variables user
		$micontent = $content;
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
						$micontent = str_replace('{user_email}',$email, $micontent);
					break;
					case 'nickname':
						$micontent = str_replace('{nickname}',$nick, $micontent);
					break;
					case 'first_name':
						$micontent = str_replace('{first_name}',$first, $micontent);
					break;
					case 'last_name':
						$micontent = str_replace('{last_name}',$last, $micontent);
					break;
					case 'user_birth':
						$micontent = str_replace('{user_birth}',$birth, $micontent);
					break;
					case 'client_name':
						$micontent = str_replace('{client_name}',$first, $micontent);
					break;
					//si es {product_name} buscaremos toda la lista de productos del usuario
					case 'product_name':
						$list_order = details_order($payment_id);
						$micontent = str_replace('{product_name}',$list_order, $micontent);
					break;
				}
			}
		}

		//refresh content attr
		return $micontent;
}

//accion para traernos los datos
function payment_row_actions( $row_actions, $payment ){
	$button_show = false;
	if($payment->status_nicename=='Pending' || $payment->status_nicename=='Pendiente'){
		//creamos la funcion para llamar el email junto con el quick action
		$customer_id = edd_get_payment_customer_id( $payment->ID );
		$customer    = new EDD_Customer( $customer_id );
		if ( $customer->id > 0) {	// Customer already exists
			$row_actions['view_customer'] = '<a payment_id="'.$payment->ID.'" href="#" id="enviarData" >Enviar Email</a> <span style="display:none; color:black; font-weight:bold;" class="msj_span">ENVIANDO MENSAJE</span>';
		}
	}
	return $row_actions;
}

//ajax function callback
function email_payment_callback(){
		check_ajax_referer('email_payment_ajax' );
		$option  = get_option( 'wp-payment-emails');
		$content_temp = '';
		$subject = isset($option['title'] ) ? $option['title'] : '';
		$content = isset($option['content'] ) ? $option['content'] : '';

		//$content_temp = '';
		$customer_id = edd_get_payment_customer_id( $_POST['data']['payment_id']);
		$customer    = new EDD_Customer( $customer_id );
		
		//obteniendo datos de usuarioo
		$user_info   = edd_get_payment_meta_user_info($_POST['data']['payment_id']);
		$email		 = $user_info['email'];

		//reemplazamos el contenido de los tags por los del usuario
		$content_temp = replace_content($content,$email,$_POST['data']['payment_id']);
		//enviando el mensaje
		wp_mail($email, $subject, $content_temp, array( 'Content-Type: text/html; charset=UTF-8' ) );
		sleep(2);
		print('Mensaje Enviado');

}

//funcion para enviar el mensaje via ajax
function reparaciones_load_scripts(){
	$nonce = wp_create_nonce('email_payment_ajax');
?><script type="text/javascript">
		jQuery(document).ready(function($){
				//creando la funcion ajax
				function ajax_post(element){
					var data = {
						payment_id : $("#enviarData").attr("payment_id")
					}
					$.post( 
							"<?php echo admin_url( 'admin-ajax.php' ); ?>", 
							{
							action : "email_payment",
							_ajax_nonce : "<?php echo $nonce; ?>",
							data:data
							},
							function( result ) {
								if(result.indexOf('Mensaje Enviado')>-1){
									element.parent().find('span.msj_span').text("MENSAJE ENVIADO").delay(1000).fadeOut(1000);
								}
						});
				}
				//abrimos el boton download
				$("#enviarData").click(function(){
					//alert("hola tico");
					$(this).parent().find('span.msj_span').show('fast');
					$(this).parent().find('span.msj_span').text('ENVIANDO MENSAJE...');
					ajax_post($(this));
					return false;
				});
		});
	</script>
<?php
}
