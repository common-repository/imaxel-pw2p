<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
# ============================================== #
# ==== SEND TO PRODUCTION OR DELETE PROJECT ==== #
# ============================================== #
//TO CREATE BUTTONS IN ADMIN PANEL

function imaxel_iweb_my_custom_checkout_field_display_admin_order_meta($order,$id_proyecto,$status,$product_id="",$variation_id="",$status_woo=""){
	
	$nonce = wp_create_nonce("my_imaxel_iweb_continue_edit_nonce");
	$data=null;
	$url="";
	//IF ORDER -> GET ALL THE DATA TO SEND TO PRODUCTION
	//error_log( '$order is:');
	//error_log( print_r($order,true));
	if($order=="") {
		
	} else {
		$data = imaxel_iweb_get_itransact_args($order,$id_proyecto);
			
		if(array_key_exists('shipping_method',$data)==false){
			$data["shipping_method"]="";
		}
		if(array_key_exists('shipping_method_cost',$data)==false){
			$data["shipping_method_cost"]=0;
		}
		if(array_key_exists('shipping_method_name',$data)==false){
			$data["shipping_method_name"]="";
		}
		
		//WITH NONCE!
		
		$url =''.admin_url('admin-ajax.php?action=my_imaxel_iweb_order&coupon_code='.urlencode($data["coupon_code"]).'&billing_first_name='.urlencode($data["billing_first_name"]).'&billing_last_name='.urlencode($data["billing_last_name"]).'&billing_company='.urlencode($data["billing_company"]).'&billing_address='.urlencode($data["billing_address"]).'&billing_postcode='.urlencode($data["billing_postcode"]).'&billing_city='.urlencode($data["billing_city"]).'&billing_state='.urlencode($data["billing_state"]).'&billing_country='.urlencode($data["billing_country"]).'&billing_email='.urlencode($data["billing_email"]).'&billing_phone='.urlencode($data["billing_phone"]).'&shipping_first_name='.urlencode($data["shipping_first_name"]).'&shipping_last_name='.urlencode($data["shipping_last_name"]).'&shipping_company='.urlencode($data["shipping_company"]).'&shipping_address='.urlencode($data["shipping_address"]).'&shipping_postcode='.urlencode($data["shipping_postcode"]).'&shipping_city='.urlencode($data["shipping_city"]).'&shipping_state='.urlencode($data["shipping_state"]).'&shipping_country='.urlencode($data["shipping_country"]).'&order_comments='.urlencode($data["order_comments"]).'&pedidoimaxel='.urlencode($data["salenumber"]).'&shipping_method='.urlencode($data["shipping_method"]).'&shipping_method_cost='.urlencode($data["shipping_method_cost"]).'&shipping_method_name='.urlencode($data["shipping_method_name"]).'&payment_method='.urlencode($data["payment_method"]).'&total='.urlencode($data["total"]).'&jobs='.urlencode($data["jobs"]).'&nonce='.$nonce.'');

	}
    	
	//CREATED ICONS
	if($status=="created"){

		//EDIT
		$link = admin_url('admin-ajax.php?action=my_imaxel_iweb_continue_edit&projectid='.$id_proyecto.'&productsID='.$product_id.'&variation_id='.$variation_id.'&attribute_proyecto='.(int)$id_proyecto.'&panel=imaxel-projects&nonce='.$nonce);
		echo '<a id="iweb_continueediting'.$id_proyecto.'" data-projectid="'.$id_proyecto.'" data-productsID="'.$product_id.'" data-variation_id="'.$variation_id.'" data-attribute_proyecto="'.(int)$id_proyecto.'" data-panel="imaxel-projects" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$id_proyecto.'" href="'.$link.'"><img  align="left" src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
	
	//READY ICONS
	} elseif($status=="ready") {
	
		//EDIT
		$link = admin_url('admin-ajax.php?action=my_imaxel_iweb_continue_edit&projectid='.$id_proyecto.'&productsID='.$product_id.'&variation_id='.$variation_id.'&attribute_proyecto='.(int)$id_proyecto.'&panel=imaxel-projects&nonce='.$nonce);
		echo '<a id="iweb_continueediting'.$id_proyecto.'" data-projectid="'.$id_proyecto.'" data-productsID="'.$product_id.'" data-variation_id="'.$variation_id.'" data-attribute_proyecto="'.(int)$id_proyecto.'" data-panel="imaxel-projects" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$id_proyecto.'" href="'.$link.'"><img  align="left" src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';

		
		//PRODUCE OR REPRODUCE - NEED ORDER ID
		if( empty($order)==false){ 
			if($status_woo=="processing"){
				//RED
				echo '<a href="#" title="'.__('Produce ORDER','Imaxel').' '.$order->id.'" id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/produce2.png"/></a>';
			} else {
				//ORANGE
				echo '<a href="#" title="'.__('Produce ORDER','Imaxel').' '.$order->id.'" id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/produce.png"/></a>';
			}
		}else{ 
			echo '<img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>'; 
		}
	
	
	//PRODUCED ICONS
	} elseif( $status=="produced" ) {
		echo '<img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
		echo '<img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
		
		//REPRODUCE
		if( $data!=null ){
			echo '<a href="#"  title="'.__('Reproduce ORDER','Imaxel').' '.$order->id.'" id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/reproduce.png"/></a>';
		} else {
			echo '<a href="#"  title="'.__('Reproduce ORDER','Imaxel').' '.$order->id.'" id="sendimaxel'.$id_proyecto.'" data-id="" data-project="'.$id_proyecto.'" data-client=""><img src="'.imaxel_iweb_myplugin_plugin_url_imaxel() . '/img/reproduce.png"/></a>';
		}
	} else {
		
	}

    
	//AJAX HERE - PRODUCE OR CONTINUE OR CANCEL(NOT USED)
	echo '<script>';
	echo 'jQuery(document).ready(function() {';
	
	if(!empty($order)) {
		echo '
		jQuery("a#sendimaxel'.$id_proyecto.'").click(function(){

			var msg = \''.( 
				$status=="produced" ? __('Reprocess will duplicate the production of the ORDER '.$order->id.', Are you really sure?','Imaxel') : 
				$status=="ready" ? __('This action will send the ORDER '.$order->id.' to produce in Imaxel, Are you sure?','Imaxel') :
				''
			).'\';

			if( msg=="" || !confirm(msg) ){
				return false;
			}
			
			jQuery.ajax({
				url:\''.$url.'\',
				type:\'GET\',
				success: function(data){
					var parseajson = jQuery.parseJSON(data);
					if(parseajson.id!=\'\'){
						// post_meta = jQuery(this).val(),
						var 
							post_meta = \'1\',
							ID = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-id"),
							PROJECT = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-project"),
							CLIENT = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-client"),
							PROJECTSLIST = jQuery.parseJSON( '.json_encode($data["jobs"]).' ) || [];
						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								action: "update_meta_iweb",
								post_id: ID,
								meta: post_meta,
								projectinfo: data,
								project: PROJECT,
								projectlist: PROJECTSLIST,
								client: CLIENT,
							},
							success: function( datab ) {
								for(var i = 0; i < PROJECTSLIST.length; i++){
									var arrayprojectsb = PROJECTSLIST[i];
									//console.log(arrayprojectsb[2]);
									jQuery("span.smsimaxel"+ arrayprojectsb[2]).attr(\'style\',\'color: blue\');
									jQuery("span.smsimaxel"+ arrayprojectsb[2]).html(\'Ordered\');
									jQuery("a#sendimaxel"+ arrayprojectsb[2]).attr(\'style\',\'display:none\');
									jQuery("a#iweb_continueediting"+ arrayprojectsb[2]).attr(\'style\',\'display:none\');
								}
								jQuery("span.smsimaxel'.$id_proyecto.'").attr(\'style\',\'color: blue\');
								jQuery("span.smsimaxel'.$id_proyecto.'").html(\'Ordered\');
										
								jQuery("span.smsimaxel'.$id_proyecto.'").animate({opacity:0},200,"linear",function(){
									jQuery(this).animate({opacity:1},200);
								});
								
							},
							error: function( datab ) {
								console.log("Error:", err);
							}
						});
					}
				}
			});
			return false; 
		});
		';
	}
	echo '
		jQuery("#iweb_continueediting'.$id_proyecto.'").click(function(){
			var 
				projectid = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-projectid"),
				attribute_proyecto = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-attribute_proyecto"),
				productCode = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-productCode"),
				productsID = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-productsID"),
				variation_id = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-variation_id"),
				page_id = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-page_id"),
				panel = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-panel"),
				nonce = jQuery("#iweb_continueediting'.$id_proyecto.'").attr("data-nonce");

			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data : {
					action: "my_imaxel_iweb_continue_edit", 
					projectid: projectid, 
					attribute_proyecto: attribute_proyecto, 
					productCode : productCode,
					productsID: productsID,
					variation_id: variation_id,
					page_id: page_id,
					panel: panel,
					nonce: nonce,
				},
				success: function( datac ) {
					window.location.href = datac;			
				},
				error: function( datac ) {
					//alert(\'Error!\') 
				}
			});
			return false;
		});

		jQuery("a#deleteimaxel'.$id_proyecto.'").click(function(){
			var PROJECT = jQuery("#deleteimaxel'.$id_proyecto.'").attr("data-project");
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: "delete_meta",
					project: PROJECT,
					nonce: nonce,
				},
				success: function( datab ) {
					jQuery("span.smsimaxel'.$id_proyecto.'").attr(\'style\',\'color: red\');
					jQuery("span.smsimaxel'.$id_proyecto.'").html(\'Cancelled\');
				},
				error: function( datab ) {
					alert(\'Error!\') 
				}
			});
		
		});
	
	});';
	echo '</script>';
}


/*
	

*/


# ===================================== #
# ==== STATUS CHANGE EXE FUNCTIONS ==== #
# ===================================== #
//ON STATUS ON PROCESSING
add_action( 'woocommerce_order_status_processing', 'imaxel_iweb_mysite_woocommerce_order_status_processing' );
function imaxel_iweb_mysite_woocommerce_order_status_processing( $order_id ) {
	error_log( print_r( '**********imaxel_iweb_helpers imaxel_iweb_mysite_woocommerce_order_status_processing ENTRA', true ) );
	
	global $wpdb;
	global $woocommerce;
	global $wcdn;
	error_log( "Not ready ".$order_id."");
	$nonce = wp_create_nonce("my_imaxel_iweb_continue_edit_nonce");
	
	$automatic_send=esc_attr( get_option('automatic_send') );
	error_log( "Automatic ".$automatic_send."");
	if($automatic_send==1){
		######### SEND TO PRODUCCTION AUTOMATICALLY ########

		$ord = new WC_Order($order_id);
		$data = imaxel_iweb_get_itransact_args($ord,'');
		$transactionJobs = json_decode($data["jobs"]);
		if(count($transactionJobs)>0 ) {
			//
			// We have iWeb jobs in the transaction, lets process the iWeb order.
			//

			// Update order projects status in woo comerce to "ready"
			foreach( $ord->get_items() as $item ){
				$itemData = array( 
					'status' => 'ready',
					'client_id' => $ord->user_id
				);
				// error_log( "Processing ".$item['proyecto']."");
				$result = $wpdb->update( $wpdb->prefix.'imaxel_iweb_projects', $itemData, array('project'=>$item['proyecto']), array('%s'), array('%s') );
			}
			
			// Lets "produce" the jobs 
			$orderData=@file_get_contents(''.admin_url('admin-ajax.php?action=my_imaxel_iweb_order&coupon_code='.urlencode($data["coupon_code"]).'&billing_first_name='.urlencode($data["billing_first_name"]).'&billing_last_name='.urlencode($data["billing_last_name"]).'&billing_company='.urlencode($data["billing_company"]).'&billing_address='.urlencode($data["billing_address"]).'&billing_postcode='.urlencode($data["billing_postcode"]).'&billing_city='.urlencode($data["billing_city"]).'&billing_state='.urlencode($data["billing_state"]).'&billing_country='.urlencode($data["billing_country"]).'&billing_email='.urlencode($data["billing_email"]).'&billing_phone='.urlencode($data["billing_phone"]).'&shipping_first_name='.urlencode($data["shipping_first_name"]).'&shipping_last_name='.urlencode($data["shipping_last_name"]).'&shipping_company='.urlencode($data["shipping_company"]).'&shipping_address='.urlencode($data["shipping_address"]).'&shipping_postcode='.urlencode($data["shipping_postcode"]).'&shipping_city='.urlencode($data["shipping_city"]).'&shipping_state='.urlencode($data["shipping_state"]).'&shipping_country='.urlencode($data["shipping_country"]).'&order_comments='.urlencode($data["order_comments"]).'&pedidoimaxel='.urlencode($data["salenumber"]).'&shipping_method='.urlencode($data["shipping_method"]).'&shipping_method_cost='.urlencode($data["shipping_method_cost"]).'&shipping_method_name='.urlencode($data["shipping_method_name"]).'&payment_method='.urlencode($data["payment_method"]).'&total='.urlencode($data["total"]).'&jobs='.urlencode($data["jobs"]).'&nonce='.$nonce.''));
			error_log( "Respuesta de imaxel_iweb_order ".$orderData."");
		
			// If order was produced without problems then register locally in database
			if(empty($orderData)==false)
			{
				$jsonOrderIweb=json_decode($orderData);
				error_log("DealerOrderNumber Iweb".$jsonOrderIweb->dealerordernumber." OrderId ".$order_id."");
				update_post_meta( $order_id, '_iwebDealerOrderNumber', ''.$jsonOrderIweb->dealerordernumber.'' );
				
				for($i=0; $i<count($transactionJobs); $i++){
					$trxJob = $transactionJobs[$i];
					
					//error_log(print_r( '**********imaxel_iweb_mysite_woocommerce_order_status_processing $trxJob is ' , true ));
					//error_log(print_r( $trxJob , true ) );

					if( count($trxJob)==3 ){
						$trxProjectId = ''.$trxJob[2];
						
						$apiProjectStr = imaxel_iweb_read_project($trxProjectId);
						$apiProject = json_decode($apiProjectStr);

						//Update table, project in production successfully						

						global $wpdb;
						$result=$wpdb->update( $wpdb->prefix.'imaxel_iweb_projects',
							array( 
								'price' => (property_exists($apiProject,"design") && property_exists($apiProject->design,"price") ? ''.$apiProject->design->price : ''),
								'data_project' => $apiProjectStr,
								'status' => 'produced'
							),
							array(
								'project'=>$trxProjectId
							),
							array('%s','%s','%s'),
							array('%s')
						);	 //,'%s'
					}
				}

			
			}
		}
	}
}
?>