<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


# ================================ #
# ==== SEND TO PRODUCTION EXE ==== #
# ================================ #
add_action( 'wp_ajax_update_meta_iweb', 'imaxel_iweb_my_function' );
add_action('wp_ajax_nopriv_update_meta_iweb', 'imaxel_iweb_my_function' );
function imaxel_iweb_my_function() {
		// NONCE IN ON THE FIRST POST
	// error_log("***imaxel_iweb_my_function START***");

	// THE DATA
	$postProjects = is_array($_POST['projectlist']) ? $_POST['projectlist'] : json_decode($_POST['projectlist']);
	
	//error_log(print_r( '**********imaxel_iweb_my_function $postProjects are ' , true ));
	//error_log(print_r($postProjects,true) );

	for($i=0; $i<count($postProjects); $i++){
		$postProject = $postProjects[$i];
		
		//error_log(print_r( '**********imaxel_iweb_my_function $postProject is ' , true ));
		//error_log(print_r( $postProject , true ) );

		if( count($postProject)==3 ){
			$postProjectId = ''.$postProject[2];
			
			//DATA FOR UPDATE TABLE
			$apiProjectStr = imaxel_iweb_read_project( $postProjectId );
			$apiProject = json_decode($apiProjectStr);
			
			//Update table, project in production successfully						
			$price = (property_exists($apiProject,"design") && property_exists($apiProject->design,"price") ? ''.$apiProject->design->price : '');
			$data = array( 
				'price' => $price,
				'data_project' => $apiProjectStr,
				'status' => 'produced'
			);
			global $wpdb;
			$result=$wpdb->update($wpdb->prefix.'imaxel_iweb_projects',$data,array('project'=>$postProjectId),array('%s','%s','%s'),array('%s'));	 //,'%s'
		}
	}
	//error_log("***imaxel_iweb_my_function END ***");
	//OK! AND DIE
	echo 'Meta Updated';
	die();
}

# ============================ #
# ==== DELETE PROJECT EXE ==== #
# ============================ #
add_action( 'wp_ajax_delete_meta', 'imaxel_iweb_my_function_delete' );

function imaxel_iweb_my_function_delete() {
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_iweb_editor_nonce")) {
		exit();
	}
   	
   	//IF NOUNCE OK, MARK AS CANCELLED
   	$project = (int)$_REQUEST['project'];
   	$data = array( 
		'status' => 'cancelled'
	);
	global $wpdb;
	$result=$wpdb->update($wpdb->prefix.'imaxel_iweb_projects',$data,array('project'=>$project),array('%s'),array('%s'));	

	//OK! AND DIE
	die();
}

# ============================== #
# ==== IMAXEL API FUNCTIONS ==== #
# ============================== #

# ============================ #
# ==== BUTTON EDITOR URL  ==== #
# ============================ #
//THIS FUNCTION IS NEEDED TO GO TO THE EDITOR FROM PRODUCT SINGLE PAGE
//add_action('wp_ajax_imaxel_iweb_editor', 'imaxel_iweb_editor');
//add_action('wp_ajax_nopriv_imaxel_iweb_editor', 'imaxel_iweb_editor');
add_action('wp_ajax_imaxel_editor_iweb', 'imaxel_iweb_editor');
add_action('wp_ajax_nopriv_imaxel_editor_iweb', 'imaxel_iweb_editor');

function imaxel_iweb_editor(){ 
	error_log( print_r( '**********imaxel_iweb_editor ENTRA', true ) );
	//NONCE CHECK
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_iweb_editor_nonce")) {
      exit();
   	}   
   
   	//DATA FOR REQUEST
    $endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	
	if($endpoint=="")
		return "";
	
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	if(strstr($CART,'?')==true){ $lasturl='%26'; }else{ $lasturl='%3F'; }
	$CART_URL = urlencode($CART).''.$lasturl;
	
	if(strstr($CART,'?')==true){ $lasturlb='&'; }else{ $lasturlb='?'; }
	$CART_NORMAL_URL = $CART.''.$lasturlb;
	
	$IHOME_URL = get_home_url();
	$IHOME_URL_EN = urlencode(get_home_url());
	
	$IADMIN_URL = get_admin_url();
	$IADMIN_URL_EN = urlencode(get_admin_url());
	$output='';
	
	//PREPARE POST LIKE IMAXEL WANT
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime, date_interval_create_from_date_string('10 minutes'));
	$locale = get_locale();
	$policy ='{
	  "productCode": "'.$_REQUEST["productCode"].'",
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime->format('c').'"
	}';
	

	$policy = base64_encode($policy);
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));	
	
	$params = array(
	   "productCode" => "".$_REQUEST["productCode"]."",
	   "policy" => "".$policy."",
	   "signedPolicy" => "".urlencode($signedPolicy).""
	);
	
	sleep(2);
	
	
	$proyecto_datos = imaxel_iweb_httpPost($endpoint."/projects",$params);
	if(strstr($proyecto_datos, 'Invalid')==true)
	{ 
		imaxel_iweb_editor(); 
		die();
	}
	if(empty($proyecto_datos)==true||$proyecto_datos==null)
	{
		return;
	}
	
	$datetime2 = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime2, date_interval_create_from_date_string('10 minutes'));
	
	$id_proyect = preg_match('/{\"id\":\"(.+)\",\"app\":{\"id\":\"/', $proyecto_datos, $match);
	$the_id_project=(int)$match[1];
	if($the_id_project==0){
		
		//imaxel_iweb_editor();
		$result=array(
			'type' => 'error',
			'url_editor'=> 'No se ha podido conseguir id de proyecto',
			);
	}else{
		$policy2 ='{
		  "projectId": "'.$the_id_project.'",
		  "backURL": "'.$IHOME_URL_EN.'",
		  "lng": "'.$locale.'",
		  "addToCartURL":"'.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.$the_id_project.'",
		  "publicKey": "'.$PUBLIC_KEY.'",
		  "expirationDate": "'.$datetime2->format('c').'",
		  "redirect": "1"
		}';
		
		$policy2 = base64_encode($policy2);
		$signedPolicy2 = base64_encode(hash_hmac("SHA256", $policy2, $PRIVATE_KEY, true));
		
		
		$url = $endpoint.'/projects/'.$the_id_project.'/editUrl?backURL='.$IHOME_URL_EN.'&lng='.$locale.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.$the_id_project.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
	}
	
	//IF We have and URL to redirect, go ahead.
	//No url, repeat the process - sometimes imaxel server fail, sooooo i repeat the check.
	
	if ($the_id_project == 0) { 
		$page = $_SERVER['PHP_SELF'];
		//imaxel_iweb_editor();
		$result=array(
			'type' => 'error',
			'url_editor'=> 'La url del editor no se ha formado correctamente',
		);
			//echo $url;
	} else { 

		//Everything goes ok!
		$user_id_wp = get_current_user_id();
		$output.= $endpoint.'/projects/'.$the_id_project.'/editUrl?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.$the_id_project.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
		
		//Create row in table with the data
		global $wpdb;
		$data = array( 
		   		'project'=>''.$the_id_project.'',
		   		'product' => ''.sanitize_title(get_the_title((int)$_REQUEST["productsID"])).'',
		   		'product_id' => ''.(int)$_REQUEST["productsID"].'',
		   		'variation_id' => ''.(int)$_REQUEST["variation_id"].'',
				'status' => 'created',
				'client_id' => (int)$user_id_wp
		);
		$result=$wpdb->insert($wpdb->prefix.'imaxel_iweb_projects',$data,array('%s','%s','%s','%s','%s','%s'));
        $result=array(
			'type' => 'success',
			'url_editor'=> ''.$output.'',
		);
        
   	}
   	
   	if($result['type']=="success") {
		error_log( print_r( '**********success', true ) );
		error_log( print_r( '**********output:'.$output.'' , true ) );
		//$pepe=wp_redirect( $output, 301 );
		//error_log( print_r( '**********wp_redirect result:'.$pepe.'' , true ) );
		//header("HTTP/1.1 301 Moved Permanently");
		//header("Location: ".$output.""); //GO TO THE EDITOR!!!
		//header("HTTP/1.1 301 Moved Permanently");
		//header("Location: ".'http://www.imaxel.com/'."");
		echo $output;
		
	}else{
		error_log( print_r( '**********No success', true ) );
	    //header("Location: ".$_SERVER["HTTP_REFERER"]); //BACK TO HOME BABY
	    echo $_SERVER["HTTP_REFERER"];
	}
	//return $result;
	//header('Content: application/json');    
	//return json_encode($result);
    die();
   
	
}

# ================================================ #
# ==== CONTINUE EDIT FROM ADMIN OR MY ACCOUNT ==== #
# ================================================ #
//THIS FUNCTION IS FOR CONTINUE EDIT A PROJECTO BY ADMIN OR USER

add_action("wp_ajax_my_imaxel_iweb_continue_edit", "imaxel_iweb_my_function_continue_edit");
add_action("wp_ajax_nopriv_my_imaxel_iweb_continue_edit", "imaxel_iweb_my_function_continue_edit");

function imaxel_iweb_my_function_continue_edit(){
	
	error_log( print_r( '**********imaxel_iweb_my_function_continue_edit ENTRA', true ) );
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_iweb_continue_edit_nonce")) {
		
      exit();
    }  
    
	
	/*EDITAR PROYECTO*/
	$endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	if($endpoint=="")
		return "";
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	if(strstr($CART,'?')==true){ $lasturl='%26'; }else{ $lasturl='%3F'; }
	$CART_URL = urlencode($CART).''.$lasturl;
	
	if(strstr($CART,'?')==true){ $lasturlb='&'; }else{ $lasturlb='?'; }
	$CART_NORMAL_URL = $CART.''.$lasturlb;
	
	$IHOME_URL = get_home_url();
	$IHOME_URL_EN = urlencode(get_home_url());
	
	$IADMIN_URL = get_admin_url();
	$IADMIN_URL_EN = urlencode(get_admin_url());
	$output='';

	$datetime2 = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime2, date_interval_create_from_date_string('10 minutes'));
	
	/*$policy2 ='{
	  "projectId": "'.(int)$_REQUEST["projectid"].'",
	  "backURL": "'.$IHOME_URL_EN.'",
	  "addToCartURL": "'.$IHOME_URL_EN.'%3Fpage_id%3D'.(int)$_REQUEST["page_id"].'",
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime2->format('c').'"
	}';*/

	$policy2 ='{
		  "projectId": "'.(int)$_REQUEST["projectid"].'",
		  "backURL": "'.$IHOME_URL_EN.'",
		  "addToCartURL":"'.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$_REQUEST["projectid"].'",
		  "publicKey": "'.$PUBLIC_KEY.'",
		  "expirationDate": "'.$datetime2->format('c').'"
		}';
		
	$policy2 = base64_encode($policy2);
	$signedPolicy2 = base64_encode(hash_hmac("SHA256", $policy2, $PRIVATE_KEY, true));
	
	$url = $endpoint.'/projects/'.(int)$_REQUEST["projectid"].'/editUrl?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$_REQUEST["projectid"].''.$urlplus.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
	
	//?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$_REQUEST["projectid"].''.$urlplus.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';

	//alert ($url);
	//IF WE HAVE AN URL GO AHEAD, ELSE REPEAT THE PROCESS
	if ($url === false) { 
		$page = $_SERVER['PHP_SELF'];
		return imaxel_iweb_my_function_continue_edit();
	} else { 
		$user_id_wp = get_current_user_id();
		
		//REDIRECT TO MY ACCOUNT
		if($_REQUEST["page_id"]!=""){
			//echo $endpoint.'/projects/'.(int)$_REQUEST["projectid"].'/editUrl?backURL='.$IHOME_URL_EN.'%3Fpage_id%3D'.(int)$_REQUEST["page_id"].'&addToCartURL='.$IHOME_URL_EN.'%3Fpage_id%3D'.(int)$_REQUEST["page_id"].'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
			echo $url;
		//REDIRECT TO ADMIN PANEL
		}elseif($_REQUEST["panel"]!=""){
			echo $endpoint.'projects/'.(int)$_REQUEST["projectid"].'/editUrl?backURL='.$IADMIN_URL_EN.'admin.php%3Fpage%3D'.$_REQUEST["panel"].'&addToCartURL='.$IADMIN_URL_EN.'admin.php%3Fpage%3D'.$_REQUEST["panel"].'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
		
		//REDIRECT DEFAULT
		}else{
			echo $endpoint.'projects/'.(int)$_REQUEST["projectid"].'/editUrl?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$_REQUEST["projectid"].''.$urlplus.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';
		}
		
	}
	die();
}


# ======================================== #
# ==== DUPLACATE PROJECT IN MYACCOUNT ==== #
# ======================================== #
//SOME TIMES WE WANT TO MAKE A COPY FROM OTHER PROJECT

add_action("wp_ajax_my_imaxel_iweb_duplicate_edit", "imaxel_iweb_my_function_duplicate_edit");
add_action("wp_ajax_nopriv_my_imaxel_iweb_duplicate_edit", "imaxel_iweb_my_function_duplicate_edit");

function imaxel_iweb_my_function_duplicate_edit(){
	error_log( print_r( '**********imaxel_iweb_my_function_duplicate_edit ENTRA!!', true ) );
	error_log(print_r('variation_id:'.$_REQUEST["variation_id"].'',true));
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_iweb_continue_edit_nonce")) {
		error_log( print_r( '**********imaxel_iweb_my_function_duplicate_edit NO NONCE', true ) );
      exit();
    }
    
    $endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	
	if($endpoint=="")
		return "";
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	if(strstr($CART,'?')==true){ $lasturl='%26'; }else{ $lasturl='%3F'; }
	$CART_URL = urlencode($CART).''.$lasturl;
	
	if(strstr($CART,'?')==true){ $lasturlb='&'; }else{ $lasturlb='?'; }
	$CART_NORMAL_URL = $CART.''.$lasturlb;
	
	$IHOME_URL = get_home_url();
	$IHOME_URL_EN = urlencode(get_home_url());
	
	$IADMIN_URL = get_admin_url();
	$IADMIN_URL_EN = urlencode(get_admin_url());
	$output='';

	
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime, date_interval_create_from_date_string('10 minutes'));

	$policy ='{
	  "projectId": "'.(int)$_REQUEST["copyprojectid"].'",
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime->format('c').'"
	}';
	
	$policy = base64_encode($policy);
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));	
	
	$params = array(
	   "projectId" => "".(int)$_REQUEST["copyprojectid"]."",
	   "policy" => "".$policy."",
	   "signedPolicy" => "".urlencode($signedPolicy).""
	);

	$proyecto_datos = imaxel_iweb_httpPost($endpoint."/projects",$params);
	$data_obj = json_decode(''.$proyecto_datos.'');
	error_log(print_R($proyecto_datos,true));
	$datetime2 = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime2, date_interval_create_from_date_string('10 minutes'));
	//$id_proyect = preg_match('/{\"id\":\"(.+)\",\"app\":{\"id\":\"/', $proyecto_datos, $match);
	$id_proyect = $data_obj->id;
	
	if(strstr($proyecto_datos, 'Invalid')==true){ imaxel_iweb_my_function_duplicate_edit(); die(); }

	//SOMETHING GOES WRONG, WE NEED THE NEW PROJECT ID, REPEAT OPERATION
	/*if ((int)$match[1] == 0) { 
		imaxel_iweb_my_function_duplicate_edit();
		die();
	}*/
	
	//WE HAVE THE NEW PROJECT ID, WE NEED NOW THE URL
	$url="";
	while($url==""){
	$policy2 ='{
	  "projectId": "'.(int)$id_proyect.'",
	  "backURL": "'.$IHOME_URL_EN.'",
	  "addToCartURL": "'.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$id_proyect.'",
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime2->format('c').'"
	}';
	
	$policy2 = base64_encode($policy2);
	$signedPolicy2 = base64_encode(hash_hmac("SHA256", $policy2, $PRIVATE_KEY, true));
	
	$url = $endpoint.'/projects/'.(int)$id_proyect.'/editUrl?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$id_proyect.''.$urlplus.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'';
	}
	
	//TRY UNTIL GET IT
	//ANYTHING WRONG; REPEAT
	if ((int)$id_proyect ==0) { 
		imaxel_iweb_my_function_duplicate_edit();
	}else{ 

		$user_id_wp = get_current_user_id();
		
		//CREATE THE NEW ROW IN DATABASE, STATUS IS CREATED
		global $wpdb;
		$data = array( 
			'project'=>''.$id_proyect.'',
			'product' => ''.sanitize_title(get_the_title((int)$_REQUEST["productsID"])).'',
			'product_id' => ''.(int)$_REQUEST["productsID"].'',
			'variation_id' => ''.(int)$_REQUEST["variation_id"].'',
			'status' => 'created',
			'client_id' => (int)$user_id_wp
		);
		error_log(print_R($data,true));
		$result=$wpdb->insert($wpdb->prefix.'imaxel_iweb_projects',$data,array('%s','%s','%s','%s','%s','%s'));
						
		//REDIRECT TO CART IF WE WANT TO BUY (WE HAVE AND ORDER ID, WE NEED A NEW ONE)
		if($_REQUEST["buy"]=="yes"){
			echo ''.$CART_NORMAL_URL.'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.(int)$id_proyect.''.$urlplus.'';
		
		//GO TO EDITOR IF ONLY WANT TO DUPLICATE
		}else{
			echo $endpoint.'/projects/'.(int)$id_proyect.'/editUrl?backURL='.$IHOME_URL_EN.'&addToCartURL='.$CART_URL.'add-to-cart%3D'.(int)$_REQUEST["productsID"].'%26variation_id%3D'.(int)$_REQUEST["variation_id"].'%26attribute_proyecto%3D'.(int)$id_proyect.''.$urlplus.'&policy='.$policy2.'&signedPolicy='.urlencode($signedPolicy2).'&redirect=1';	
		}


	}
	
	die();
}

# =================================== #
# ==== SEND TO PRODUCTION IMAXEL ==== #
# =================================== #


add_action("wp_ajax_my_imaxel_iweb_order", "imaxel_iweb_order");
add_action("wp_ajax_nopriv_my_imaxel_iweb_order", "imaxel_iweb_order");
function imaxel_iweb_order(){
	
	error_log( print_r( '**********imaxel_iweb_api imaxel_iweb_order', true ) );
	$endpoint=esc_attr( get_option('url_base_iweb_api') );
	if($endpoint=="")
		return "";
	error_log( print_r( '**********imaxel_iweb_order entrada', true ) );
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=esc_attr( get_option('cart_page') );
	$CART_URL = get_permalink($CART);
	
	date_default_timezone_set('Europe/Madrid');	
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime, date_interval_create_from_date_string('10 minutes'));
	parse_str($_SERVER['QUERY_STRING'], $output);
	

	$trxJobs = json_decode($output["jobs"]);
	$apiJobs = array();

	for($i=0; $i<count($trxJobs); $i++){
		$trxJob = $trxJobs[$i]; 
		if(count($trxJob)==3){
			array_push( $apiJobs, array(
				"project" => array(
					"id"=>"".$trxJob[2]
				),
				"units" => intval($trxJob[1])
			));
		}
	}

	$apiCheckout = array(
		"billing" => array(
				"email" => "".$output["billing_email"],
				"firstName" => "".$output["billing_first_name"],
				"lastName" => "".$output["billing_last_name"],
				"phone" => "".$output["billing_phone"]
		),
		"saleNumber"=>"".$output["pedidoimaxel"],
		"payment"=>array(
				"name"=> "".$output["payment_method"],
				"instructions"=>""
		),
		"shippingMethod" => array(
			"amount"=> ($output["shipping_method_cost"]=="" ? 0 : (double)$output["shipping_method_cost"]),
			"name" => "".$output["shipping_method"],
			"instructions" => "".$output["shipping_method_name"]
		),
		// OJO: HARDCODED
		"discount"=>array(
				"amount"=> 0,
				"name"=> "",
				"code"=> ""
		),
		"total" => (double)$output["total"]
	);
		
	if($output["shipping_method"]=="local_pickup"){
		//
		// OJO: ESTO ESTÁ HARCODED!!!
		//
		$apiCheckout["pickpoint"]=array(
			"address"=> "C/falsa 123",
			"city"=> "Madrid",
			"postalCode"=> "28001",
			"province"=> "Madrid",
			"country"=> "Espana",
			"email"=> "correo@correo.es",
			"firstName"=> "Pedidos",
			"lastName"=> "DEMO",
			"phone"=> "",
			"instructions"=> "".$output["order_comments"]
		);
	} else {
		$apiCheckout["recipient"] = array(
			"address"=> "".$output["shipping_address"],
			"city"=>"".$output["shipping_city"],
			"postalCode"=>"".$output["shipping_postcode"],
			"province"=>"".$output["shipping_state"],
			"country"=>"".$output["shipping_country"],
			"email"=>"".$output["billing_email"],
			"firstName"=>"".$output["shipping_first_name"],
			"lastName"=>"".$output["shipping_last_name"],
			"phone"=>"".$output["billing_phone"]
		);
	}

	
		//SPLIT JOBS AND CHECKOUT
	//JUST PUT JOBS
	$endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	$expirationDate = new DateTime("".date('y-m-d H:i:s.u'));
	$expirationDate->add( date_interval_create_from_date_string('10 minutes') );
	$policy = base64_encode(json_encode(array(
		"jobs"=> $apiJobs,
		"checkout" => $apiCheckout,
		"publicKey" => $PUBLIC_KEY,
		"expirationDate" => $expirationDate->format('c')
	)))	;
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));
	
	//PUT JOBS
	//PUT CHECKOUT
	$jsonParams = json_encode(array(
		"jobs" => $apiJobs,
		"checkout" => $apiCheckout,
		"policy" => $policy,
		"signedPolicy" => $signedPolicy
	));
	error_log( print_r( '**********imaxel_iweb_order httpPostOrder', true ) );
	$remoteOrderStr = ''.imaxel_iweb_httpPostOrder($endpoint.'/orders', $jsonParams);
	//error_log( "Data ".$proyecto_datos."");

	//SEND BACK THE PROJECT DATA
	echo $remoteOrderStr;

	die();

}


# =========================== #
# ==== READ PROJECT DATA ==== #
# =========================== #


function imaxel_iweb_read_project($id_project){
	
	error_log( print_r( '**********imaxel_iweb_read_project ENTRA. $id_project='.$id_project.'', true ) );
	$endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	if($endpoint=="")
		return "";
	
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	if(strstr($CART,'?')==true){ $lasturl='%26'; }else{ $lasturl='%3F'; }
	$CART_URL = urlencode($CART).''.$lasturl;
	
	if(strstr($CART,'?')==true){ $lasturlb='&'; }else{ $lasturlb='?'; }
	$CART_NORMAL_URL = $CART.''.$lasturlb;
	
	$IHOME_URL = get_home_url();
	$IHOME_URL_EN = urlencode(get_home_url());
	
	$IADMIN_URL = get_admin_url();
	$IADMIN_URL_EN = urlencode(get_admin_url());
	
	if($PUBLIC_KEY==""){
		return "";
	}else{
		
	$endpoint.='/projects/'.(int)$id_project.'';
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime, date_interval_create_from_date_string('10 minutes'));

	$policy ='{
	  "projectId": "'.(int)$id_project.'",
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime->format('c').'"
	}';
	

	$policy = base64_encode($policy);
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));
	
	$params = array(
	   "policy" => "".$policy."",
	   "signedPolicy" => "".urlencode($signedPolicy).""
	);
	
	$proyecto_datos = @file_get_contents($endpoint.'?policy='.$policy.'&signedPolicy='.urlencode($signedPolicy).''); 
	if($proyecto_datos==""){
		//imaxel_iweb_read_project($id_project);
		error_log( print_r( '**********imaxel_iweb_read_project proyecto_datos vacio!!!!!', true ) );
	}else{
		error_log( print_r( '**********imaxel_iweb_read_project proyecto_datos: '.$proyecto_datos.''  , true ) );
		return $proyecto_datos;	
	}
	}
}


# ====================================== #
# ==== GET ALL PRODUCTS FROM IMAXEL ==== #
# ====================================== #


function imaxel_iweb_get_all_products(){
	
	$endpoint=esc_attr( get_option('url_base_iweb_api') ); 
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	if($endpoint=="")
		return "";
	
	$URL_BASE=esc_attr( get_option('url_base') );
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	if(strstr($CART,'?')==true){ $lasturl='%26'; }else{ $lasturl='%3F'; }
	$CART_URL = urlencode($CART).''.$lasturl;
	
	if(strstr($CART,'?')==true){ $lasturlb='&'; }else{ $lasturlb='?'; }
	$CART_NORMAL_URL = $CART.''.$lasturlb;
	
	$IHOME_URL = get_home_url();
	$IHOME_URL_EN = urlencode(get_home_url());
	
	$IADMIN_URL = get_admin_url();
	$IADMIN_URL_EN = urlencode(get_admin_url());
	
	$endpoint.="/products";
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));

	date_add($datetime, date_interval_create_from_date_string('10 minutes'));
	if($PUBLIC_KEY==""){
		return "";
	}
	
	
	$policy ='{
	  "publicKey": "'.$PUBLIC_KEY.'",
	  "expirationDate": "'.$datetime->format('c').'"
	}';

	$policy = base64_encode($policy);
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));
	
	$params = array(
	   "policy" => "".$policy."",
	   "signedPolicy" => "".urlencode($signedPolicy).""
	);
	try
	{
		$proyecto_datos = @file_get_contents($endpoint.'?policy='.$policy.'&signedPolicy='.urlencode($signedPolicy).''); 
		
		return $proyecto_datos;
		//if($proyecto_datos==""){
		//	imaxel_iweb_get_all_products();
		//}
		//else{
		//	return $proyecto_datos;	
		//}
	}
	catch(Exception $e)
	{
		 echo 'Excepci�n capturada: ',  $e->getMessage(), "\n";
	}
	
	
	
}

?>