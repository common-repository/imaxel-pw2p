<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 *
 *
 * WORDPRESS
 *
 *
 */

# ======================= #
# ==== PAGE SELECTOR ==== #
# ======================= #

function imaxel_combo_select_page_callback() {
$options = get_option('cart_page');
    wp_dropdown_pages(
        array(
             'name' => 'cart_page',
             'echo' => 1,
             'show_option_none' => __( '&mdash; Select &mdash;' ),
             'option_none_value' => '0',
             'selected' => $options
        )
    );
}
function imaxel_iweb_combo_select_page_callback() {
$options = get_option('cart_page');
    wp_dropdown_pages(
        array(
             'name' => 'cart_page',
             'echo' => 1,
             'show_option_none' => __( '&mdash; Select &mdash;' ),
             'option_none_value' => '0',
             'selected' => $options
        )
    );
}


# =========================== #
# ==== CUSTOMER SELECTOR ==== #
# =========================== #

function imaxel_customers_dropdown_list($client_id) {

    $args = array(
        'role' => 'customer'
    );
    $users = get_users($args);
    if( empty($users) )
      return;
    
    echo'<select name="imaxel_customer_id">';
    echo '<option value="">'.__('Select customer','Imaxel').'</option>';
    foreach( $users as $user ){
        echo '<option '; 
        if($client_id==$user->id){ echo ' selected="selected" ';}
        echo ' value="'.$user->id.'">'.$user->data->display_name.'</option>';
    }
    echo'</select>';
}

function imaxel_iweb_customers_dropdown_list($client_id) {

    $args = array(
        'role' => 'customer'
    );
    $users = get_users($args);
    if( empty($users) )
      return;
    
    echo'<select name="imaxel_customer_id">';
    echo '<option value="">'.__('Select customer','Imaxel').'</option>';
    foreach( $users as $user ){
        echo '<option '; 
        if($client_id==$user->id){ echo ' selected="selected" ';}
        echo ' value="'.$user->id.'">'.$user->data->display_name.'</option>';
    }
    echo'</select>';
}


/*
 *
 *
 * WOOCOMMERCE
 *
 *
 */

# ================================= #
# ==== GET WOOCOMMERCE VERSION ==== #
# ================================= #

function imaxel_wpbo_get_woo_version_number() {

	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	

	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	

	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {

		return NULL;
	}
} 
function imaxel_wpbo_get_imaxel_pw2p_number() {

	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	

	$plugin_folder = get_plugins( '/' . 'imaxel-pw2p' );
	$plugin_file = 'imaxel.php';
	

	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {

		return NULL;
	}
} 
function imaxel_wpbo_get_imaxel_pw2p_isactive()
{
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . 'imaxel-pw2p' );
	$plugin_file = 'imaxel.php';
	
	return is_plugin_active( $plugin_folder[$plugin_file] ) ;
}

function imaxel_iweb_wpbo_get_woo_version_number() {

	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	

	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	

	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {

		return NULL;
	}
} 


# ========================================= #
# ==== FORCE USER REGISTER TO SEE CART ==== #
# ========================================= #

add_action( 'template_redirect', 'imaxel_redirection_function', 1, 2 );
add_action( 'template_redirect', 'imaxel_iweb_redirection_function', 1, 2 );

function imaxel_redirection_function(){
    global $woocommerce;
    if( is_cart() && (is_user_logged_in()==false)){

        wp_safe_redirect( add_query_arg('marklast', 'yes', get_permalink( woocommerce_get_page_id('myaccount'))  ) );
    }
}

function imaxel_iweb_redirection_function(){
    global $woocommerce;
    if( is_cart() && (is_user_logged_in()==false)){

        wp_safe_redirect( add_query_arg('marklast', 'yes', get_permalink( woocommerce_get_page_id('myaccount'))  ) );
    }
}

add_filter( 'woocommerce_login_redirect', 'imaxel_wc_custom_user_redirect', 10, 2 );
add_filter( 'woocommerce_login_redirect', 'imaxel_iweb_wc_custom_user_redirect', 10, 2 );

function imaxel_wc_custom_user_redirect( $redirect ) {

	wp_safe_redirect( add_query_arg('marklast', 'yes',  get_permalink(esc_attr( get_option('cart_page') )) ) );	
}

function imaxel_iweb_wc_custom_user_redirect( $redirect ) {

	wp_safe_redirect( add_query_arg('marklast', 'yes',  get_permalink(esc_attr( get_option('cart_page') )) ) );	
}

//add_filter('wc_session_expiring' , array('WoocommerceLicenseAPI', 'imaxel_filter_ExtendSessionExpiring') );
//add_filter('wc_session_expiration' , array('WoocommerceLicenseAPI', 'imaxel_filter_ExtendSessionExpired') );

function imaxel_filter_ExtendSessionExpiring($seconds) {
	return (60 * 60 * 24 * 2) - (60 * 60 * 23 * 2);
}
function imaxel_filter_ExtendSessionExpired($seconds) {
	return (60 * 60 * 24 * 2) - (60 * 60 * 23 * 2);
}




# ============================================ #
# ==== REPLACE FILES WOOCOMMERCE TEMPLATE ==== #
# ============================================ #

 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_filter( 'woocommerce_locate_template', 'imaxel_myplugin_woocommerce_locate_template', 10, 3 );
	 
	function imaxel_myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
	 
	  global $woocommerce;
	  
	  $_template = $template;	 
	  
	  if ( ! $template_path ) $template_path = $woocommerce->template_url;
	  
	  $plugin_path  = imaxel_myplugin_plugin_path_imaxel() . '/woocommerce/';
	 
	  //echo $plugin_path;
	  // Look within passed path within the theme - this is priority
	 
	  $template = locate_template(
	    
	    array(	 
	      //The priority is on the plugin, we need variable.php from the plugin
	      //$template_path . $template_name,
	    
	      $template_name
	    
	    )
	  
	  );
	 
	  // Modification: Get the template from this plugin, if it exists
	  if ( ! $template && file_exists( $plugin_path . $template_name ) )
	  
	    $template = $plugin_path . $template_name;
	 
	  // Use default template
	  if ( ! $template )
	  
	    $template = $_template;
	 
	  // Return what we found
	  return $template;
	}
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_filter( 'woocommerce_locate_template', 'imaxel_iweb_myplugin_woocommerce_locate_template', 10, 3 );
	 
	function imaxel_iweb_myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
	 
	  global $woocommerce;
	  
	  $_template = $template;	 
	  
	  if ( ! $template_path ) $template_path = $woocommerce->template_url;
	  
	  $plugin_path  = imaxel_iweb_myplugin_plugin_path_imaxel() . '/woocommerce/';
	 
	  //echo $plugin_path;
	  // Look within passed path within the theme - this is priority
	 
	  $template = locate_template(
	    
	    array(	 
	      //The priority is on the plugin, we need variable.php from the plugin
	      //$template_path . $template_name,
	    
	      $template_name
	    
	    )
	  
	  );
	 
	  // Modification: Get the template from this plugin, if it exists
	  if ( ! $template && file_exists( $plugin_path . $template_name ) )
	  
	    $template = $plugin_path . $template_name;
	 
	  // Use default template
	  if ( ! $template )
	  
	    $template = $_template;
	 
	  // Return what we found
	  return $template;
	}
}

# ======================================================= #
# ==== GET PROJECT PRICE AND MODIFY CART WOOCOMMERCE ==== #
# ======================================================= #

function imaxel_calculate_gift_wrap_fee( $cart_object ) {
	error_log( print_r( '**********imaxel_calculate_gift_wrap_fee ENTRA', true ) );
	//error_log(print_R( $cart_object, TRUE ));
	/*foreach ( $cart_object->cart_contents as $key => $values ) { 
		$product = $values['data'];
		$myId   = $product->post->ID;
		$isIweb= get_post_meta( $myId, '_iweb');
		error_log( print_r( '**********valor de cart_object '.$isIweb[0].'' , true ) );
		if($isIweb==1){return;}
	}*/
	
    global $wpdb;
    $proyectos=$wpdb->get_results( "SELECT project FROM ".$wpdb->prefix."imaxel_projects",ARRAY_A);
	$list_projects='';
	foreach ( $proyectos as $project) { 
    	$list_projects.='|'.$project['project'];
    }
    $list_projects.='|';
	
	//proyectos iweb
	$proyectosIweb=$wpdb->get_results( "SELECT project FROM ".$wpdb->prefix."imaxel_iweb_projects",ARRAY_A);
	$list_projects_iweb='';
	foreach ( $proyectosIweb as $project) { 
    	$list_projects_iweb.='|'.$project['project'];
    }
    $list_projects_iweb.='|';
	
		
    foreach ( $cart_object->cart_contents as $key => $value ) { 
			$product = $value['data'];
			$myId   = $product->post->ID;
			$isIweb= get_post_meta( $myId, '_iweb');
			
			error_log(print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee IsIweb[0] empty() '.empty($isIweb).'' , true ));
			if(empty($isIweb)){
				continue;
			}
			error_log( print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee IsIweb '.$isIweb[0].'' , true ) );
			if($isIweb[0]==1){
				$data = imaxel_iweb_read_project($value['variation']['attribute_proyecto']);
				//$preciototal = preg_match('/\":([0-9]+(\.[0-9]{1,2})?),\"variant_code\"/', $data, $match);
				$preciototal = preg_match('/\"price\":([0-9]+(\.[0-9]{1,2})?),/', $data, $match);
				error_log( print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee preciototal '.$preciototal.'' , true ) );
				error_log( print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee data '.$data.'' , true ) );
				//error_log( print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee match '.$match.'' , true ) );
				error_log( print_r( '**********functions_helper imaxel_calculate_gift_wrap_fee match[1] '.$match[1].'' , true ) );
				//error_log(print_r('cart sample'.$value.'',true));
				$data_del_proyecto=$data;
				$value['data']->price = $match[1]; 
				$value['data']->name="Hola";
				//$value['data']->quantity=40;
				//error_log( print_r('resumen '.$value['data'].'',true));
				$nombre = $value['data']->post;
				$user_id_wp = get_current_user_id();
				//error_log( print_r( '**********functions_helper imaxel_iweb_calculate_gift_wrap_fee cambiamos el precio', true ) );
				if(isset($_GET["buyagain"]) && $_GET["buyagain"]==true){

        } else {
					if(strstr($list_projects_iweb,$value['variation']['attribute_proyecto'])==true){
						$data = array( 
								'product' => ''.$nombre->post_name.'',
								'price' => ''.$match[1].'',
								'status' => 'ready',
								'data_project' => ''.$data_del_proyecto,
								'client_id' => (int)$user_id_wp
						);
						
						$list_projects_iweb.='|'.$value['variation']['attribute_proyecto'];
						error_log( print_r( '**********imaxel_calculate_gift_wrap_fee IWEB imaxel_projects UPDATE', true ) );
						$result=$wpdb->update($wpdb->prefix.'imaxel_iweb_projects',$data,array('project'=>$value['variation']['attribute_proyecto']),array('%s','%s','%s','%s'),array('%s'));	
						
					}else{
						$data = array( 
								'project'=>''.$value['variation']['attribute_proyecto'].'',
								'product' => ''.$nombre->post_name.'',
								'price' => ''.$match[1].'',
								'status' => 'ready',
								'data_project' => ''.$data_del_proyecto,
								'client_id' => (int)$user_id_wp
						);
						
						$list_projects_iweb.='|'.$value['variation']['attribute_proyecto'];
						error_log( print_r( '**********imaxel_calculate_gift_wrap_fee IWEB imaxel_projects INSERT', true ) );
						$result=$wpdb->insert($wpdb->prefix.'imaxel_iweb_projects',$data,array('%s','%s','%s','%s','%s'));
					}	
				}
			}elseif($isIweb[0]==-1){
				$data = imaxel_read_project($value['variation']['attribute_proyecto']);
				$data_del_proyecto=$data;
				$preciototal = preg_match('/\":([0-9]+(\.[0-9]{1,2})?),\"variant_code\"/', $data, $match);
				$value['data']->price = $match[1]; 
				$nombre = $value['data']->post;
				$user_id_wp = get_current_user_id();
				if($_GET["buyagain"]==true){}else{
					if(strstr($list_projects,$value['variation']['attribute_proyecto'])==true){
						$data = array( 
								'product' => ''.$nombre->post_name.'',
								'price' => ''.$match[1].'',
								'status' => 'ready',
								'data_project' => ''.$data_del_proyecto,
								'client_id' => (int)$user_id_wp
						);
						
						$list_projects.='|'.$value['variation']['attribute_proyecto'];
						error_log( print_r( '**********imaxel_calculate_gift_wrap_fee HTML5 imaxel_projects UPDATE', true ) );
						$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$value['variation']['attribute_proyecto']),array('%s','%s','%s','%s'),array('%s'));	
						
					}else{
						$data = array( 
								'project'=>''.$value['variation']['attribute_proyecto'].'',
								'product' => ''.$nombre->post_name.'',
								'price' => ''.$match[1].'',
								'status' => 'ready',
								'data_project' => ''.$data_del_proyecto,
								'client_id' => (int)$user_id_wp
						);
						
						$list_projects.='|'.$value['variation']['attribute_proyecto'];
						error_log( print_r( '**********imaxel_calculate_gift_wrap_fee HTML5 imaxel_projects INSERT', true ) );
						$result=$wpdb->insert($wpdb->prefix.'imaxel_projects',$data,array('%s','%s','%s','%s','%s'));
					}	
				}
			}
	    	
			
    }
    
}
add_action( 'woocommerce_before_calculate_totals', 'imaxel_calculate_gift_wrap_fee', 1, 1 );



# ===================================== #
# ==== GET INFO ORDER WOOCOMMERCE  ==== #
# ===================================== #

function imaxel_get_itransact_args( $order ,$id_proyecto){
  global $woocommerce;
  global $wcdn;

  $order_id = $order->id;

  $coupon_code =  get_post_meta($order_id,'_coupon_code',true);
  $billing_first_name =  get_post_meta($order_id,'_billing_first_name',true);
  $billing_last_name = get_post_meta($order_id,'_billing_last_name',true);
  $billing_company = get_post_meta($order_id,'_billing_company',true);
  $billing_address = get_post_meta($order_id,'_billing_address_1',true);
  $billing_address2 = get_post_meta($order_id,'_billing_address_2',true);
  $billing_postcode = get_post_meta($order_id,'_billing_postcode',true);
  $billing_city = get_post_meta($order_id,'_billing_city',true);
  $billing_state = get_post_meta($order_id,'_billing_state',true);
  $billing_country = get_post_meta($order_id,'_billing_country',true);
  $billing_email = get_post_meta($order_id,'_billing_email',true);
  $billing_phone = get_post_meta($order_id,'_billing_phone',true);

  $shipping_first_name =  get_post_meta($order_id,'_shipping_first_name',true);
  $shipping_last_name = get_post_meta($order_id,'_shipping_last_name',true);
  $shipping_company = get_post_meta($order_id,'_shipping_company',true);
  $shipping_address = get_post_meta($order_id,'_shipping_address_1',true);
  $shipping_address2 = get_post_meta($order_id,'_shipping_address_2',true);
  $shipping_postcode = get_post_meta($order_id,'_shipping_postcode',true);
  $shipping_city = get_post_meta($order_id,'_shipping_city',true);
  $shipping_state = get_post_meta($order_id,'_shipping_state',true);
  $shipping_country = get_post_meta($order_id,'_shipping_country',true);


  $payment_method = get_post_meta($order_id,'_payment_method',true);
  $shipping_method = $order->get_items( 'shipping' );

  $order_comments = get_post_meta($order_id,'_order_comments',true);
 
  $data["coupon_code"]=$coupon_code;  
  $data['billing_first_name'] = $billing_first_name;
  $data['billing_last_name'] = $billing_last_name;
  $data['billing_company'] = $billing_company;

  $data['billing_address'] = $billing_address. " " . $billing_address2;
  $data['billing_postcode'] = $billing_postcode;
  $data['billing_city'] = $billing_city;
  $data['billing_state'] = $billing_state;
  $data['billing_country'] = $billing_country;

  $data['billing_phone'] = $billing_phone;
  $data['billing_email'] = $billing_email;

  $data['shipping_first_name'] = $shipping_first_name;
  $data['shipping_last_name'] = $shipping_last_name;
  $data['shipping_company'] = $shipping_company;

  $data['shipping_address'] = $shipping_address. " " . $shipping_address2;
  $data['shipping_postcode'] = $shipping_postcode;
  $data['shipping_city'] = $shipping_city;
  $data['shipping_state'] = $shipping_state;
  $data['shipping_country'] = $shipping_country;

  $data['customerReference'] = $order_id.'-'.$order->order_key;
  $data['salenumber'] = $order_id;

  $data['id_factura'] = $order_id;
  $data['total'] = number_format($order->get_total(), 2, '.', '');   

  $data['payment_method'] = $payment_method;
  if(is_array($shipping_method) && count($shipping_method)>0){
    $data['shipping_method'] = array_values($shipping_method)[0]["method_id"];
    $data['shipping_method_cost'] = array_values($shipping_method)[0]["cost"];
    $data['shipping_method_name'] = array_values($shipping_method)[0]["name"];
  }
  $data['order_comments'] = $order_comments;
  $data["jobs"] ='';

  $jobs = array();
  $items = $order->get_items();
  foreach($items as $item)    {
    $item_id = $item['product_id'];
    $product = new WC_Product($item_id);
    error_log(print_r( '**********functions_helper imaxel_get_itransact_args $product is ' , true ));
    $myId = $product->post->ID;
    $isIweb = get_post_meta( $myId, '_iweb');
    
    error_log(print_r( '**********functions_helper imaxel_get_itransact_args IsIweb[0] empty() '.empty($isIweb).'' , true ));
    error_log( print_r( '**********functions_helper imaxel_get_itransact_args IsIweb '.$isIweb[0].' producto: '.$item['name'].''  , true ) );
          
    //Edu: aquí solo deberían estar los jobs de html5
    if($isIweb[0]==-1){
      array_push($jobs,array($item['name'], $item['qty'], $item['proyecto']));
    }
  }
  $data["jobs"]=json_encode($jobs);

  $data['ret_addr'] = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))));

  return $data; 
}

function imaxel_iweb_get_itransact_args( $order ,$id_proyecto){
  global $woocommerce;
  global $wcdn;

  $order_id = $order->id;

  $coupon_code =  get_post_meta($order_id,'_coupon_code',true);
  $billing_first_name =  get_post_meta($order_id,'_billing_first_name',true);
  $billing_last_name = get_post_meta($order_id,'_billing_last_name',true);
  $billing_company = get_post_meta($order_id,'_billing_company',true);
  $billing_address = get_post_meta($order_id,'_billing_address_1',true);
  $billing_address2 = get_post_meta($order_id,'_billing_address_2',true);
  $billing_postcode = get_post_meta($order_id,'_billing_postcode',true);
  $billing_city = get_post_meta($order_id,'_billing_city',true);
  $billing_state = get_post_meta($order_id,'_billing_state',true);
  $billing_country = get_post_meta($order_id,'_billing_country',true);
  $billing_email = get_post_meta($order_id,'_billing_email',true);
  $billing_phone = get_post_meta($order_id,'_billing_phone',true);

  $shipping_first_name =  get_post_meta($order_id,'_shipping_first_name',true);
  $shipping_last_name = get_post_meta($order_id,'_shipping_last_name',true);
  $shipping_company = get_post_meta($order_id,'_shipping_company',true);
  $shipping_address = get_post_meta($order_id,'_shipping_address_1',true);
  $shipping_address2 = get_post_meta($order_id,'_shipping_address_2',true);
  $shipping_postcode = get_post_meta($order_id,'_shipping_postcode',true);
  $shipping_city = get_post_meta($order_id,'_shipping_city',true);
  $shipping_state = get_post_meta($order_id,'_shipping_state',true);
  $shipping_country = get_post_meta($order_id,'_shipping_country',true);


  $payment_method = get_post_meta($order_id,'_payment_method',true);
  $shipping_method = $order->get_items( 'shipping' );

  $order_comments = get_post_meta($order_id,'_order_comments',true);


  $data = array(); 
  $data["coupon_code"]=$coupon_code;  
  $data['billing_first_name'] = $billing_first_name;
  $data['billing_last_name'] = $billing_last_name;
  $data['billing_company'] = $billing_company;

  $data['billing_address'] = $billing_address. " " . $billing_address2;
  $data['billing_postcode'] = $billing_postcode;
  $data['billing_city'] = $billing_city;
  $data['billing_state'] = $billing_state;
  $data['billing_country'] = $billing_country;

  $data['billing_phone'] = $billing_phone;
  $data['billing_email'] = $billing_email;

  $data['shipping_first_name'] = $shipping_first_name;
  $data['shipping_last_name'] = $shipping_last_name;
  $data['shipping_company'] = $shipping_company;

  $data['shipping_address'] = $shipping_address. " " . $shipping_address2;
  $data['shipping_postcode'] = $shipping_postcode;
  $data['shipping_city'] = $shipping_city;
  $data['shipping_state'] = $shipping_state;
  $data['shipping_country'] = $shipping_country;

  $data['customerReference'] = $order_id.'-'.$order->order_key;
  $data['salenumber'] = $order_id;

  $data['id_factura'] = $order_id;
  $data['total'] = number_format($order->get_total(), 2, '.', '');   

  $data['payment_method'] = $payment_method;
  if(is_array($shipping_method) && count($shipping_method)>0){
    $data['shipping_method'] = array_values($shipping_method)[0]["method_id"];
    $data['shipping_method_cost'] = array_values($shipping_method)[0]["cost"];
    $data['shipping_method_name'] = array_values($shipping_method)[0]["name"];
  } 

  $data['order_comments'] = $order_comments;
  $data["jobs"] = array();

  $jobs = array();

  $items = $order->get_items();
  foreach($items as $item)    {
    $item_id = $item['product_id'];
    $product = new WC_Product($item_id);
    if(!empty($product)){
      // Producto no accesible
      $myId   = $product->post->ID;
      $isIweb= get_post_meta( $myId, '_iweb');
      
      error_log(print_r( '**********functions_helper imaxel_iweb_get_itransact_args IsIweb[0] empty() '.empty($isIweb).'' , true ));
      error_log( print_r( '**********functions_helper imaxel_iweb_get_itransact_args IsIweb '.$isIweb[0].'' , true ) );
      
      //Edu: aquí solo deberían estar los jobs de iweb
      if($isIweb[0]==1){
        array_push($jobs,array($item['name'], $item['qty'], $item['proyecto']));
      }
    }
  }
  $data["jobs"] = json_encode($jobs);

  $data['ret_addr'] = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))));


  return $data; 
}

# =================================== #
# ==== ALWAYS SOLD INDIVIDUALLY  ==== #
# =================================== #

function imaxel_wpa_119087_always_sold_individually( $individually, $product ){
   $myId   = $product->post->ID;
   $isIweb= get_post_meta( $myId, '_iweb');
   if( empty($isIweb)==false&&$isIweb=="-1")
   {
   
		$module=get_post_meta( $myId, '_html5WorkType');
		if( empty($module)==false&&$$module=="printspack")
		{
			$individually = true;
		}
		
   }
 
  return $individually;
}
add_filter( 'woocommerce_is_sold_individually', 'imaxel_wpa_119087_always_sold_individually', 10, 2 );

function imaxel_iweb_wpa_119087_always_sold_individually( $individually, $product )
{

   $myId   = $product->post->ID;
   $isIweb= get_post_meta( $myId, '_iweb');
   if( empty($isIweb)==false&&$isIweb=="1")
   {
		$WorkType=get_post_meta( $myId, '_iwebWorkType');
		if( empty($WorkType)==false&&$WorkType=="1")
		{
			$individually = true;
		}
   }
 
  return $individually;
  
}
add_filter( 'woocommerce_is_sold_individually', 'imaxel_iweb_wpa_119087_always_sold_individually', 10, 2 );


# ============================================== #
# ==== FUNCTION TO SET ATTRIBUTES, NOT USED ==== #
# ============================================== #


function imaxel_wcproduct_set_attributes($post_id, $attributes) {
    $i = 0;
    foreach ($attributes as $name => $value) {
        $product_attributes[$i] = array (
          'name' => htmlspecialchars( stripslashes( $name ) ), // set attribute name
          'value' => $value, // set attribute value
          'position' => 1,
          'is_visible' => 1,
          'is_variation' => 1,
          'is_taxonomy' => 0
        );
        $i++;
    }

    // Now update the post with its new attributes
    update_post_meta($post_id, '_product_attributes', $product_attributes);
}

function imaxel_iweb_wcproduct_set_attributes($post_id, $attributes) {
    $i = 0;
    foreach ($attributes as $name => $value) {
        $product_attributes[$i] = array (
            'name' => htmlspecialchars( stripslashes( $name ) ), // set attribute name
            'value' => $value, // set attribute value
            'position' => 1,
            'is_visible' => 1,
            'is_variation' => 1,
            'is_taxonomy' => 0
        );

        $i++;
    }

    // Now update the post with its new attributes
    update_post_meta($post_id, '_product_attributes', $product_attributes);
}


/*
 *
 *
 * PHP FUNCTIONS HELPERS
 *
 *
 */


# ========================= #
# ==== OBJECT TO ARRAY ==== #
# ========================= #


function imaxel_objectToArray($d) {
	if (is_object($d)) {
	// Gets the properties of the given object
	// with get_object_vars function
	$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
	/*
	* Return array converted to object
	* Using __FUNCTION__ (Magic constant)
	* for recursive call
	*/
	return array_map(__FUNCTION__, $d);
	}
	else {
	// Return array
	return $d;
	}
}

function imaxel_iweb_objectToArray($d) {
	if (is_object($d)) {
	// Gets the properties of the given object
	// with get_object_vars function
	$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
	/*
	* Return array converted to object
	* Using __FUNCTION__ (Magic constant)
	* for recursive call
	*/
	return array_map(__FUNCTION__, $d);
	}
	else {
	// Return array
	return $d;
	}
}

# ========================================= #
# ==== ALTERNATIVE TO FILE_GET_CONTENT ==== #
# ========================================= #

//Delete this
/*function http_get_contents($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if(FALSE === ($retval = curl_exec($ch))) {
    error_log(curl_error($ch));
  } else {
    return $retval;
  }
}*/

function imaxel_http_get_contents($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if(FALSE === ($retval = curl_exec($ch))) {
    error_log(curl_error($ch));
  } else {
    return $retval;
  }
}

function imaxel_url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
?>