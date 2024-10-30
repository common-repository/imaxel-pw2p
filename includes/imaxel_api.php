<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


# ================================ #
# ==== SEND TO PRODUCTION EXE ==== #
# ================================ #
add_action( 'wp_ajax_update_meta', 'imaxel_my_function' );
add_action('wp_ajax_nopriv_update_meta', 'imaxel_my_function' );
function imaxel_my_function() {
	// NONCE IN ON THE FIRST POST
	// error_log("***imaxel_my_function update_meta START***");

	// THE DATA
	$postProjects = is_array($_POST['projectlist']) ? $_POST['projectlist'] : json_decode($_POST['projectlist']);
	
	//error_log(print_r( '**********functions_helper imaxel_my_function $postProjects are ' , true ));
	//error_log( print_r($postProjects,true) );

	for($i=0; $i<count($postProjects); $i++){
		$postProject = $postProjects[$i];
		
		//error_log(print_r( '**********functions_helper imaxel_my_function $postProject is ' , true ));
		//error_log(print_r( $postProject , true ));

		if( count($postProject)==3 ){
			$postProjectId = ''.$postProject[2];
			
			// update table: Project sent for production succefully
			$projectDataStr = imaxel_read_project( $postProjectId );
			$projectData = json_decode($projectDataStr);
			$data = array( 
				'price' => ''.$projectData->design->price,
				'data_project' => $projectDataStr,
				'status' => 'produced'
			);
			global $wpdb;
			$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$postProjectId),array('%s','%s','%s'),array('%s'));	 //,'%s'

		}
	}
	//error_log("***imaxel_my_function update_meta END ***");
	//OK! AND DIE
	echo 'Meta Updated';
	die();
}


# ============================ #
# ==== DELETE PROJECT EXE ==== #
# ============================ #
add_action( 'wp_ajax_delete_meta', 'imaxel_my_function_delete' );
function imaxel_my_function_delete() {
	
  if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_editor_nonce")) {
    exit();
  } 
   	
  //IF NOUNCE OK, MARK AS CANCELLED
  $project = (int)$_REQUEST['project'];
  $data = array( 
	  'status' => 'cancelled'
	);
	global $wpdb;
	$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$project),array('%s'),array('%s'));	

	die();
}



# ============================== #
# ==== IMAXEL API FUNCTIONS ==== #
# ============================== #


# ============================ #
# ==== BUTTON EDITOR URL  ==== #
# ============================ #
//THIS FUNCTION IS NEEDED TO GO TO THE EDITOR FROM PRODUCT SINGLE PAGE
add_action('wp_ajax_imaxel_editor', 'imaxel_editor');
add_action('wp_ajax_nopriv_imaxel_editor', 'imaxel_editor');
function imaxel_editor(){ 
	error_log( print_r( '**********imaxel_editor ENTRA', true ) );
	//NONCE CHECK
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_editor_nonce")) {
     exit();
 	}   
  // API INFO

	$the_id_project = imaxel_api_createProject($_REQUEST["productCode"]);

	if($the_id_project==0){
		$result = array(
			'type' => 'error',
			'msg'=> 'Internal problem creating project',
		);
	} else {
		//
		// 2.- Registramos proyecto en la BBDD
		//
		
		//Everything goes ok!
		$user_id_wp = get_current_user_id();
		
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
		$result=$wpdb->insert($wpdb->prefix.'imaxel_projects',$data,array('%s','%s','%s','%s','%s','%s'));
		//
		// 3. Generamos la redirección al imaxel_editor
		//
		$cart_url = get_permalink(esc_attr( get_option('cart_page') ));
		$addToCartURL= ''.$cart_url.''.(strstr($cart_url,'?') === FALSE ? '?' : '&').'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.$the_id_project;			
		$backURL = get_home_url();

		$url = imaxel_api_editorUrl($the_id_project, $backURL, $addToCartURL );

		$result = array(
			'type' => 'success',
			'url'=> $url
		);
	
	}
	
	if($result['type']=="success") {
		//header("Location: ".$output.""); //GO TO THE EDITOR!!!
		echo $result['url'];
	} else {
		//header("Location: ".$_SERVER["HTTP_REFERER"]); //BACK TO HOME BABY
		echo $_SERVER["HTTP_REFERER"];
	}
	die();
}


# ================================================ #
# ==== CONTINUE EDIT FROM ADMIN OR MY ACCOUNT ==== #
# ================================================ #
//THIS FUNCTION IS FOR CONTINUE EDIT A PROJECTO BY ADMIN OR USER

add_action("wp_ajax_my_imaxel_continue_edit", "imaxel_my_function_continue_edit");
add_action("wp_ajax_nopriv_my_imaxel_continue_edit", "imaxel_my_function_continue_edit");
function imaxel_my_function_continue_edit(){
	error_log( print_r( '**********imaxel_my_function_continue_edit ENTRA', true ) );
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_continue_edit_nonce")) {
		exit();
	}  
 
	$user_id_wp = get_current_user_id();
	$addToCartURL ="";
	$backURL="";
	
	if( $_REQUEST["page_id"] != "" ){
		// REDIRECT TO MY ACCOUNT
		error_log( print_r( '**********imaxel_my_function_continue_edit page_id', true ) );
		$cart_url = get_permalink(esc_attr( get_option('cart_page') ));
		$addToCartURL = ''.$cart_url.''.(strstr($cart_url,'?') === FALSE ? '?' : '&').'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.(int)$_REQUEST["projectid"].'';
		$backURL = ''.get_home_url();
	} elseif( $_REQUEST["panel"] != "" ){
		// REDIRECT TO ADMIN PANEL
		error_log( print_r( '**********imaxel_my_function_continue_edit panel', true ) );
		$addToCartURL = ''.get_admin_url().'admin.php?page='.$_REQUEST["panel"];
		$backURL = ''.get_admin_url().'admin.php?page='.$_REQUEST["panel"];	
	} else {
		// REDIRECT DEFAULT
		error_log( print_r( '**********imaxel_my_function_continue_edit default', true ) );
		$cart_url = get_permalink(esc_attr( get_option('cart_page') ));
		$addToCartURL = ''.$cart_url.''.(strstr($cart_url,'?') === FALSE ? '?' : '&').'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.(int)$_REQUEST["projectid"].'';
		$backURL = ''.get_home_url();
	}

	/**
	 *  API CALL
	 */
	$url = imaxel_api_editorUrl((int)$_REQUEST["projectid"], $backURL, $addToCartURL);
	
	echo $url;

	//}
	die();
}


# ======================================== #
# ==== DUPLACATE PROJECT IN MYACCOUNT ==== #
# ======================================== #
//SOME TIMES WE WANT TO MAKE A COPY FROM OTHER PROJECT

add_action("wp_ajax_my_imaxel_duplicate_edit", "imaxel_my_function_duplicate_edit");
add_action("wp_ajax_nopriv_my_imaxel_duplicate_edit", "imaxel_my_function_duplicate_edit");
function imaxel_my_function_duplicate_edit(){
	error_log( print_r( '**********imaxel_my_function_duplicate_edit ENTRA', true ) );
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_continue_edit_nonce")) {
		exit();
	}
	error_log( print_r( '**********imaxel_my_function_duplicate_edit copyprojectid==='.$_REQUEST["copyprojectid"], true ) );
	$projectId = imaxel_api_duplicateProject((int)$_REQUEST["copyprojectid"]);
	if($projectId==0){
		error_log( print_r( '**********imaxel_my_function_duplicate_edit $projectId==0', true ) );
		$result = array(
			'type' => 'error',
			'msg'=> 'Internal problem creating project',
		);
	} else {
		error_log( print_r( '**********imaxel_my_function_duplicate_edit $projectId!=0', true ) );
		// Create row in table with the data
		global $wpdb;
		$data = array( 
			'project'=>''.$projectId.'',
			'product' => ''.sanitize_title(get_the_title((int)$_REQUEST["productsID"])).'',
			'product_id' => ''.(int)$_REQUEST["productsID"].'',
			'variation_id' => ''.(int)$_REQUEST["variation_id"].'',
			'status' => 'created',
			'client_id' => (int)get_current_user_id()
		);
		$result=$wpdb->insert($wpdb->prefix.'imaxel_projects',$data,array('%s','%s','%s','%s','%s','%s'));
		//
		// 3. Generamos la redirección al imaxel_editor
		//

		$cart_url = get_permalink(esc_attr( get_option('cart_page') ));
		$cart_url = $cart_url.''.(strstr($cart_url,'?') === FALSE ? '?' : '&');
		if($_REQUEST["buy"]=="yes"){
			// REDIRECT TO CART IF WE WANT TO BUY (WE HAVE AND ORDER ID, WE NEED A NEW ONE)
			$result = array(
				'type'=>'success',
				'url'=> $cart_url.'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.(int)$match[1].''.$urlplus.''
			);
		}else{
			//GO TO EDITOR IF ONLY WANT TO DUPLICATE
			$addToCartURL= $cart_url.'add-to-cart='.(int)$_REQUEST["productsID"].'&variation_id='.(int)$_REQUEST["variation_id"].'&attribute_proyecto='.$projectId;			
			$backURL = get_home_url();

			$url = imaxel_api_editorUrl($projectId, $backURL, $addToCartURL );

			$result = array(
				'type' => 'success',
				'url'=> $url
			);
		}	
	
	}
	

	if($result['type']=="success") {
		//header("Location: ".$output.""); //GO TO THE EDITOR!!!
		echo $result['url'];
	} else {
	   //header("Location: ".$_SERVER["HTTP_REFERER"]); //BACK TO HOME BABY
	  echo $_SERVER["HTTP_REFERER"];
	}

	die();

}

# =================================== #
# ==== SEND TO PRODUCTION IMAXEL ==== #
# =================================== #


add_action("wp_ajax_my_imaxel_order", "imaxel_order");
add_action("wp_ajax_nopriv_my_imaxel_order", "imaxel_order");
function imaxel_order(){
	
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_imaxel_continue_edit_nonce")) {
		exit();
	}
	error_log("imaxel_order HTML5" );
	
	$endpoint="http://services.imaxel.com/api/v3";
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
		"billing"=>array(
				"email"=>"".$output["billing_email"],
				"firstName"=>"".$output["billing_first_name"],
				"lastName"=>"".$output["billing_last_name"],
				"phone"=>"".$output["billing_phone"]
		),
		"saleNumber"=>"".$output["pedidoimaxel"],
		"payment"=>array(
				"name"=> "".$output["payment_method"],
				"instructions"=> ""
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
		$apiCheckout["pickpoint"] = array(
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

	$apiCheckout["shippingMethod"] = array(
		"amount"=> ($output["shipping_method_cost"]=="" ? 0 : (double)$output["shipping_method_cost"]),
		"name" => "".$output["shipping_method"],
		"instructions" => "".$output["shipping_method_name"]
	);
	

	//SPLIT JOBS AND CHECKOUT
	//JUST PUT JOBS
	$endpoint="http://services.imaxel.com/api/v3";
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
	$expirationDate = new DateTime("".date('y-m-d H:i:s.u'));
	$expirationDate->add( date_interval_create_from_date_string('10 minutes') );
	$policy = base64_encode(json_encode(array(
		"jobs"=> $apiJobs,
		"publicKey" => $PUBLIC_KEY,
		"expirationDate" => $expirationDate->format('c')
	)));
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));
	
	//PUT JOBS
	//PUT CHECKOUT
	$jsonParams = json_encode(array(
		"jobs" => $apiJobs,
		"checkout" => $apiCheckout,
		"policy" => $policy,
		"signedPolicy" => $signedPolicy
	));
	//error_log( "Parametros ".$jsonParams."");
	$remoteOrderStr = ''.imaxel_httpPostOrder($endpoint."/orders", $jsonParams);
	//error_log( "Data ".$remoteOrderStr."");

	//SEND BACK THE PROJECT DATA
	echo $remoteOrderStr;

	die();
}


# =========================== #
# ==== READ PROJECT DATA ==== #
# =========================== #


function imaxel_read_project($id_project){
	
	error_log( print_r( '**********imaxel_read_project ENTRA', true ) );
	
	$endpoint="http://services.imaxel.com/api/v3";
	$PUBLIC_KEY=esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY=esc_attr( get_option('private_key') );
		
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));
	date_add($datetime, date_interval_create_from_date_string('10 minutes'));

	$policy = base64_encode(json_encode(array(
		"projectId"=>''.(int)$id_project,
		"publicKey"=> $PUBLIC_KEY,
		"expirationDate"=> $datetime->format('c')
	)));
	$signedPolicy = base64_encode(hash_hmac("SHA256", $policy, $PRIVATE_KEY, true));
	
	$proyecto_datos = @file_get_contents($endpoint . '/projects/'.(int)$id_project.'?policy='.urlencode($policy).'&signedPolicy='.urlencode($signedPolicy) ); 
	if($proyecto_datos==""){
		error_log( print_r( '**********imaxel_read_project END without proyect data', true ) );
		//imaxel_read_project($id_project);
	} else {
		error_log( print_r( '**********imaxel_read_project END', true ) );
		return $proyecto_datos;	
	}
}


# ====================================== #
# ==== GET ALL PRODUCTS FROM IMAXEL ==== #
# ====================================== #


function imaxel_get_all_products(){
	
	$endpoint="http://services.imaxel.com/api/v3";
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
	
	$endpoint.="/products";
	$datetime = new DateTime("".date('y-m-d H:i:s.u'));

	date_add($datetime, date_interval_create_from_date_string('10 minutes'));
	if($PUBLIC_KEY==""){
		return "";
	}else{
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
	
	//OLD version
	//$proyecto_datos = @file_get_contents($endpoint.'?policy='.$policy.'&signedPolicy='.urlencode($signedPolicy).''); 
	$proyecto_datos = imaxel_url_get_contents($endpoint.'?policy='.urlencode($policy).'&signedPolicy='.urlencode($signedPolicy).'');
	
	if($proyecto_datos==""){
		imaxel_get_all_products();
	}else{
		return $proyecto_datos;	
	}
	}	
	
}


# ===================================== #
# ==== PRIVATE API SUPPORT METHODS ==== #
# ===================================== #


function imaxel_api_createProject( $productCode ){

  $endpoint="http://services.imaxel.com/api/v3";
	$PUBLIC_KEY = esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY = esc_attr( get_option('private_key') );

	$expirationDate = new DateTime("".date('y-m-d H:i:s.u'));
	$expirationDate->add(date_interval_create_from_date_string('10 minutes'));
	$encodedPolicy = base64_encode(json_encode(array(
		'productCode' => $productCode,
		'publicKey'=> $PUBLIC_KEY,
		'expirationDate' => $expirationDate->format('c')
	)));
	$signedPolicy = base64_encode(hash_hmac("SHA256", $encodedPolicy, $PRIVATE_KEY, true));	
	$params = array(
	   "productCode" => "".$_REQUEST["productCode"]."",
	   "policy" => $encodedPolicy,
	   "signedPolicy" => urlencode($signedPolicy)
	);

	sleep(2);
	
	$project_data = imaxel_httpPost( $endpoint.'/projects', $params);

	if( strstr($project_data, 'Invalid')==true ){
		return 0;
	} else {
		$id_proyect = preg_match('/{\"id\":\"(.+)\",\"app\":{\"id\":\"/', $project_data, $match);
		return (int)$match[1];
	}
}

function imaxel_api_duplicateProject( $projectId ){

  $endpoint="http://services.imaxel.com/api/v3";
	$PUBLIC_KEY = esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY = esc_attr( get_option('private_key') );

	$expirationDate = new DateTime("".date('y-m-d H:i:s.u'));
	$expirationDate->add(date_interval_create_from_date_string('10 minutes'));

	$encodedPolicy = base64_encode(json_encode(array(
		'projectId' => ''.$projectId,
		'publicKey'=> $PUBLIC_KEY,
		'expirationDate' => $expirationDate->format('c')
	)));
	$signedPolicy = base64_encode(hash_hmac("SHA256", $encodedPolicy, $PRIVATE_KEY, true));	
	
	$params = array(
	   "projectId" => "".$projectId,
	   "policy" => $encodedPolicy,
	   "signedPolicy" => urlencode($signedPolicy)
	);
	$project_data = imaxel_httpPost( $endpoint.'/projects', $params);

	if(strstr($project_data, 'Invalid')==true  ){
		return 0;
	} else {
		$id_project = preg_match('/{\"id\":\"(.+)\",\"app\":{\"id\":\"/', $project_data, $match);
		error_log( print_r( '**********imaxel_editor imaxel_api_duplicateProject', true ) );
		error_log( print_r( $project_data, true ) );
		error_log( print_r( $id_project, true ) );
		error_log( print_r( $match, true ) );
		return (int)$match[1];

	}
	
}

function imaxel_api_editorUrl( $projectId, $backURL, $addToCartURL ){
	$endpoint="http://services.imaxel.com/api/v3";
	$PUBLIC_KEY = esc_attr( get_option('public_key') ); 
	$PRIVATE_KEY = esc_attr( get_option('private_key') );
	
	$expirationDate = new DateTime("".date('y-m-d H:i:s.u'));
	$expirationDate->add(date_interval_create_from_date_string('10 minutes'));
	$encodedPolicy = base64_encode(json_encode(array(
			"projectId" => ''.$projectId,
			"backURL" => $backURL,
			"addToCartURL" => $addToCartURL,
			"publicKey"=> $PUBLIC_KEY,
			"expirationDate" => $expirationDate->format('c')
	)));
	$signedPolicy = base64_encode(hash_hmac("SHA256", $encodedPolicy, $PRIVATE_KEY, true));	
	$url = $endpoint.'/projects/'.$projectId.'/editUrl?backURL='.urlencode($backURL).'&addToCartURL='.urlencode($addToCartURL).'&policy='.$encodedPolicy.'&signedPolicy='.urlencode($signedPolicy).'&redirect=1';

	return $url;	
}

?>