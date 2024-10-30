<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
# ============================================== #
# ==== SEND TO PRODUCTION OR DELETE PROJECT ==== #
# ============================================== #
//TO CREATE BUTTONS IN ADMIN PANEL

function imaxel_my_custom_checkout_field_display_admin_order_meta($order,$id_proyecto,$status,$product_id="",$variation_id="",$status_woo=""){
	
	$nonce = wp_create_nonce("my_imaxel_continue_edit_nonce");
	$data = null;
	//IF ORDER -> GET ALL THE DATA TO SEND TO PRODUCTION
	if($order==""){

	} else{
		$data = imaxel_get_itransact_args($order,$id_proyecto);
		
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
		$url =''.admin_url('admin-ajax.php?action=my_imaxel_order&coupon_code='.urlencode($data["coupon_code"]).'&billing_first_name='.urlencode($data["billing_first_name"]).'&billing_last_name='.urlencode($data["billing_last_name"]).'&billing_company='.urlencode($data["billing_company"]).'&billing_address='.urlencode($data["billing_address"]).'&billing_postcode='.urlencode($data["billing_postcode"]).'&billing_city='.urlencode($data["billing_city"]).'&billing_state='.urlencode($data["billing_state"]).'&billing_country='.urlencode($data["billing_country"]).'&billing_email='.urlencode($data["billing_email"]).'&billing_phone='.urlencode($data["billing_phone"]).'&shipping_first_name='.urlencode($data["shipping_first_name"]).'&shipping_last_name='.urlencode($data["shipping_last_name"]).'&shipping_company='.urlencode($data["shipping_company"]).'&shipping_address='.urlencode($data["shipping_address"]).'&shipping_postcode='.urlencode($data["shipping_postcode"]).'&shipping_city='.urlencode($data["shipping_city"]).'&shipping_state='.urlencode($data["shipping_state"]).'&shipping_country='.urlencode($data["shipping_country"]).'&order_comments='.urlencode($data["order_comments"]).'&pedidoimaxel='.urlencode($data["salenumber"]).'&shipping_method='.urlencode($data["shipping_method"]).'&shipping_method_cost='.urlencode($data["shipping_method_cost"]).'&shipping_method_name='.urlencode($data["shipping_method_name"]).'&payment_method='.urlencode($data["payment_method"]).'&total='.urlencode($data["total"]).'&jobs='.urlencode($data["jobs"]).'&nonce='.$nonce.'');
  	}
    
    
    	
	//CREATED ICONS
	if( $status=="created" ){
		//EDIT
		$link = admin_url('admin-ajax.php?action=my_imaxel_continue_edit&projectid='.$id_proyecto.'&productsID='.$product_id.'&variation_id='.$variation_id.'&attribute_proyecto='.(int)$id_proyecto.'&panel=imaxel-projects&nonce='.$nonce);
		echo '<a id="continueediting'.$id_proyecto.'" data-projectid="'.$id_proyecto.'" data-productsID="'.$product_id.'" data-variation_id="'.$variation_id.'" data-attribute_proyecto="'.(int)$id_proyecto.'" data-panel="imaxel-projects" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$id_proyecto.'" href="'.$link.'"><img  align="left" src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
	
	//READY ICONS
	} elseif ( $status=="ready" ) {
	
		//EDIT
		$link = admin_url('admin-ajax.php?action=my_imaxel_continue_edit&projectid='.$id_proyecto.'&productsID='.$product_id.'&variation_id='.$variation_id.'&attribute_proyecto='.(int)$id_proyecto.'&panel=imaxel-projects&nonce='.$nonce);
		echo '<a id="continueediting'.$id_proyecto.'" data-projectid="'.$id_proyecto.'" data-productsID="'.$product_id.'" data-variation_id="'.$variation_id.'" data-attribute_proyecto="'.(int)$id_proyecto.'" data-panel="imaxel-projects" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$id_proyecto.'" href="'.$link.'"><img  align="left" src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';

	
		//PRODUCE OR REPRODUCE - NEED ORDER ID
		if($order->id!=""){ 
			if($status_woo=="processing"){
				//RED
				echo '<a href="#" title="'.__('Produce ORDER','Imaxel').' '.$order->id.'"  id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/produce2.png"/></a>';
			}else{
				//ORANGE
				echo '<a href="#" title="'.__('Produce ORDER','Imaxel').' '.$order->id.'"  id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/produce.png"/></a>';
			}
		} else { 
			echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>'; 
		}
		
	
	//PRODUCED ICONS
	} elseif($status=="produced") {
		echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
		echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
		
		//REPRODUCE
		if($data!==null){
			echo '<a href="#" id="sendimaxel'.$id_proyecto.'" data-id="'.$data["salenumber"].'" data-project="'.$id_proyecto.'" data-client="'.$data["billing_first_name"].' '.$data["billing_last_name"].'"><img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/reproduce.png"/></a>';
		}
	} else {
		
	}

    
  //AJAX HERE - PRODUCE OR CONTINUE OR CANCEL(NOT USED)
  echo '<script>';
  echo '
  jQuery(document).ready(function() {
		';
	if(!empty($order)) {
	echo '	    
		jQuery("a#sendimaxel'.$id_proyecto.'").click(function(){
			var msg = \''.( 
				$status=="produced" ? __('Reprocess will duplicate the production of the ORDER '.$order->id.', Are you really sure?','Imaxel') : 
				$status=="ready" ? __('This action will send the ORDER '.$order->id.' to produce in Imaxel, Are you sure?','Imaxel') :
				''
			).'\';

			if( msg=="" ||  !confirm(msg) ){
				return false;
			}

			jQuery.ajax({
				url:\''.$url.'\',
				type:\'GET\',
				success: function(data){
				
					var parseajson = jQuery.parseJSON(data);
					if(parseajson.id!=\'\'){
						//post_meta = jQuery(this).val(),
						var post_meta = \'1\',
							ID = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-id"),
							PROJECT = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-project"),
							CLIENT = jQuery("#sendimaxel'.$id_proyecto.'").attr("data-client"),
							PROJECTSLIST = jQuery.parseJSON( '.json_encode($data["jobs"]).' ) || [];
					
						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								action: "update_meta",
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
									console.log(arrayprojectsb[2]);
									jQuery("span.smsimaxel"+ arrayprojectsb[2] +"").attr(\'style\',\'color: blue\');
									jQuery("span.smsimaxel"+ arrayprojectsb[2] +"").html(\'Ordered\');
									jQuery("a#sendimaxel"+ arrayprojectsb[2]).attr(\'style\',\'display:none\');
									jQuery("a#continueediting"+ arrayprojectsb[2]).attr(\'style\',\'display:none\');
								}
								jQuery("span.smsimaxel'.$id_proyecto.'").attr(\'style\',\'color: blue\');
								jQuery("span.smsimaxel'.$id_proyecto.'").html(\'Ordered\');
								
								jQuery("span.smsimaxel'.$id_proyecto.'").animate({opacity:0},200,"linear",function(){
									jQuery(this).animate({opacity:1},200);
								});
							},
							error: function( datab ) {
								console.log("Error in update_meta", datab);
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
		jQuery("#continueediting'.$id_proyecto.'").click(function(){
			var projectid = jQuery("#continueediting'.$id_proyecto.'").attr("data-projectid"),
				attribute_proyecto = jQuery("#continueediting'.$id_proyecto.'").attr("data-attribute_proyecto"),
				productCode = jQuery("#continueediting'.$id_proyecto.'").attr("data-productCode"),
				productsID = jQuery("#continueediting'.$id_proyecto.'").attr("data-productsID"),
				variation_id = jQuery("#continueediting'.$id_proyecto.'").attr("data-variation_id"),
				page_id = jQuery("#continueediting'.$id_proyecto.'").attr("data-page_id"),
				panel = jQuery("#continueediting'.$id_proyecto.'").attr("data-panel"),
				nonce = jQuery("#continueediting'.$id_proyecto.'").attr("data-nonce");
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data : {
					action: "my_imaxel_continue_edit", 
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
					//alert(datac);
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
			//console.log(PROJECT);
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

# ============================= #
# ==== MY ACCOUNT PROJECTS ==== #
# ============================= #

add_action( 'woocommerce_before_my_account', 'imaxel_my_projects_imaxel' );

function imaxel_my_projects_imaxel( $user_id) {
	
	
	global $wpdb;
	
	$nonce = wp_create_nonce("my_imaxel_continue_edit_nonce");
	$nonce_iweb = wp_create_nonce("my_imaxel_iweb_continue_edit_nonce");
	
	//CANCEL A PROJECT
	if( isset($_GET["cancel"]) && (int)$_GET["cancel"]!=0 ){
		
		$data = array( 
			'status' => 'cancelled'
		);
		if( $_GET["source"]=='iweb' ){
			$result=$wpdb->update($wpdb->prefix.'imaxel_iweb_projects',$data,array('project'=>(int)$_GET["cancel"],'client_id'=>get_current_user_id()),array('%s'),array('%s','%s'));	
		} else {
			$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>(int)$_GET["cancel"],'client_id'=>get_current_user_id()),array('%s'),array('%s','%s'));	
		}
		
	}
		
	//PREPARE TABLE
	$CART=get_permalink(esc_attr( get_option('cart_page') ));
	$CART_URL = $CART.(strpos($CART,'?') === false ? '?' : '&');
	$filters = array(
	    'post_status' => 'any',
	    'post_type' => 'shop_order',
	    'posts_per_page' => 2000,
	    'paged' => 1,
	    'orderby' => 'modified',
	    'order' => 'DESC',
	    'meta_query' => array(
		    array(
		        'key' => '_customer_user',
		        'value'   => get_current_user_id(),
		        'compare' => '='
		    )
		)
	);
	
	$loop = new WP_Query($filters);
	//LOOP DATA ORDERS
	while ($loop->have_posts()) {
	    $loop->the_post();
	    $order = new WC_Order($loop->post->ID);
	    $user_id=$order->user_id;
	    $data_extra = $order->get_items();
	    foreach($data_extra as $producto){
		    $order_data["".$producto["proyecto"].""]=array(
			    'order_id'=>$order->id,
			    'status_WC'=>$order->get_status(),
			    'line_total'=>$producto["line_total"],
			    'client_id'=>''.$order->billing_first_name . ' ' . $order->billing_last_name.'',
			    'user_id'=>''.$user_id.''
		    );
		    $order_data["".$producto["proyecto"]."_WC"]=new WC_Order($loop->post->ID);
		}
	} 
	wp_reset_query(); 

	// Wich project identifiers are actually in WooCommerce Cart?
	$cartProjectIds = array();
	foreach(WC()->cart->get_cart() as $cartLine){
		if( is_array($cartLine["variation"]) && isset($cartLine["variation"]["attribute_proyecto"]) ) {
			array_push($cartProjectIds, $cartLine["variation"]["attribute_proyecto"]);
		}
	}

	

	//LOOP PROJECTS
	//Paginations and filters here in future updates
	$j=0;
	global $wpdb;
	/*	
		select * from (
		select * from wp_imaxel_iweb_projects
		UNION ALL
		select * from wp_imaxel_projects
		) s
		order by time desc;
	*/
	$query = "SELECT * FROM (";
	$query.= "SELECT *, 'html5' as source FROM ".$wpdb->prefix."imaxel_projects WHERE status IN ('created','ready','produced') AND client_id=".get_current_user_id()." AND project NOT IN ('".implode($cartProjectIds,"','") ."')";
	$query.= "UNION ALL ";
	$query.= "SELECT *, 'iweb' as source FROM ".$wpdb->prefix."imaxel_iweb_projects WHERE status IN ('created','ready','produced') AND client_id=".get_current_user_id()." AND project NOT IN ('".implode($cartProjectIds,"','") ."')";
	$query.= ") s ORDER BY time DESC";
	//$project_array = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."imaxel_projects  WHERE status IN ('created','ready','produced') AND client_id=".get_current_user_id()." ORDER BY id DESC" );
	$project_array = $wpdb->get_results($query);
	$project_array=imaxel_objectToArray($project_array); 

	//TABLE IN MY ACCOUNT
	if ( $project_array ) : ?>

	<h2><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', __( 'My projects', 'IMAXEL' ) ); ?></h2>
	<table class="shop_table shop_table_responsive my_account_orders">

		<thead>
			<tr>
				<th class="order-number"><span class="nobr"><?php _e( 'Project', 'woocommerce' ); ?></span></th>
				<th class="order-date"><span class="nobr"><?php _e( 'Updated', 'woocommerce' ); ?></span></th>
				<th class="order-status"><span class="nobr"><?php _e( 'Order', 'woocommerce' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Product', 'woocommerce' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Price', 'woocommerce' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Status', 'woocommerce' ); ?></span></th>
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>

		<tbody><?php
		foreach($project_array as $project){
			$hasOrder = array_key_exists(''.$project["project"],  $order_data);
			$projectOrder = $hasOrder ? $order_data["".$project["project"].""] : null;

			echo '<tr id="project'.$project["project"].'">';
			echo '<td>'.$project["project"].'</td>';
			echo '<td>'.$project["time"].'</td>';
			echo '<td>'.($hasOrder?$projectOrder["order_id"]:'');
			echo '  <input type="hidden" name="post_id" value="'.($hasOrder?$projectOrder["order_id"]:'').'"/>';
			echo '</td>';
			
			$sku = get_post_meta($project["variation_id"], '_sku', true);
			echo '<td><strong>'.$sku.' - </strong>'; 
			if (strlen($project["product"]) > 25){
				echo substr($project["product"], 0, 25) . '...';
			}else{
				echo $project["product"]; 
			}			
			echo '</td>';

			echo '<td>'; 
			if($hasOrder && $projectOrder["line_total"]!=0){
				echo round($projectOrder["line_total"],2); 
				echo get_woocommerce_currency_symbol(); 
			}else{
				echo $project["price"];
				if($project["price"]!=""){ echo get_woocommerce_currency_symbol(); }
			}
			echo '</td>';
				
			echo '<td>'; 
			if($project["status"]=="created"){
				echo '<span style="color: orange;" class="smsimaxel'.$project["project"].'">'.__('Started','Imaxel').'</span>';
			}elseif($project["status"]=="ready"){
				echo '<span style="color: green;" class="smsimaxel'.$project["project"].'">'.__('Ready','Imaxel').'</span>';
			}elseif($project["status"]=="produced"){
				echo '<span style="color: blue;" class="smsimaxel'.$project["project"].'">'.__('Ordered','Imaxel').'</span>';
			}elseif($project["status"]=="cancelled"){
				echo '<span style="color: red;" class="smsimaxel'.$project["project"].'">'.__('Cancelled','Imaxel').'</span>';
			}else{

			}
			echo '</td>';

			echo '<td>';
			if( $project["status"]=="created" ){
				//DELETE
				echo '<a onclick="return confirm(\''.__('Are you really sure you want to delete this project','Imaxel').'\')" href="'.get_permalink().'?cancel='.$project["project"].'&source='. $project["source"] .'" title="'.__('Delete project','Imaxel').' '.$project["project"].'"><img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/delete.png"/></a>';
				//AJAX EDIT
				if($project["source"]=='html5'){
					$link = admin_url('admin-ajax.php?action=my_imaxel_continue_edit&projectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&page_id='.(isset($_GET["page_id"])?(int)$_GET["page_id"]:0).'&nonce='.$nonce);
				
					echo '<a id="continueediting'.$project["project"].'" data-projectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-page_id="'.(int)$_GET["page_id"].'" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$link.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
				}else{
					$link = admin_url('admin-ajax.php?action=my_imaxel_iweb_continue_edit&projectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&page_id='.(int)$_GET["page_id"].'&nonce='.$nonce_iweb);
				
					echo '<a id="iweb_continueediting'.$project["project"].'" data-projectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-page_id="'.(int)$_GET["page_id"].'" data-nonce="'.$nonce_iweb.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$link.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
				}
			
			} elseif( $project["status"]=="ready" ){
				
				if( !$hasOrder ){ 
					// DELETE
					echo '<a onclick="return confirm(\''.__('Are you really sure you want to delete this project','Imaxel').'\')" title="'.__('Delete project','Imaxel').' '.$project["project"].'" href="'.get_permalink().'?cancel='.$project["project"].'&source='. $project["source"] .'">';
					echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/delete.png"/>';
					echo '</a>'; 
					// AJAX EDIT
					if( $project["source"]=='html5' ){
						$link = admin_url('admin-ajax.php?action=my_imaxel_continue_edit&projectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&page_id='.(int)$_GET["page_id"].'&nonce='.$nonce);			
						echo '<a id="continueediting'.$project["project"].'" data-projectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-page_id="'.(int)$_GET["page_id"].'" data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$link.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
					} else {
						$link = admin_url('admin-ajax.php?action=my_imaxel_iweb_continue_edit&projectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&page_id='.(int)$_GET["page_id"].'&nonce='.$nonce_iweb);
				
						echo '<a id="iweb_continueediting'.$project["project"].'" data-projectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-page_id="'.(int)$_GET["page_id"].'" data-nonce="'.$nonce_iweb.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$link.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/edit.png"/></a>';
					}
					// AJAX DUPLICATE
					$linkd = admin_url('admin-ajax.php?action=my_imaxel_duplicate_edit&copyprojectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&buy=&nonce='.$nonce);
					echo '<a id="duplicate'.$project["project"].'" data-copyprojectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-buy=""  data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$linkd.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/duplicate.png"/></a>';
					// ADD TO CART
					$CART=get_permalink(esc_attr( get_option('cart_page') ));
					echo '<a href="'.$CART.(strpos($CART,'?') === false ? '?' : '&').'add-to-cart='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'" title="'.__('Buy order','Imaxel').' '.$project["project"].'"><img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/buy.png"/></a>';
				} else { 
					echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
					echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
					echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
				}
				
				
			}elseif( $project["status"]=="produced" ){
				echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
				echo '<img src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/spacer.png"/>';
				
				//AJAX DUPLICATE
				if($project["source"]=='html5'){
					$linkd = admin_url('admin-ajax.php?action=my_imaxel_duplicate_edit&copyprojectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&buy=&nonce='.$nonce);
				
					echo '<a id="duplicate'.$project["project"].'" data-copyprojectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-buy=""  data-nonce="'.$nonce.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$linkd.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/duplicate.png"/></a>';		
				}elseif($project["source"]=='iweb'){
					$linkd = admin_url('admin-ajax.php?action=my_imaxel_iweb_duplicate_edit&copyprojectid='.$project["project"].'&productsID='.$project["product_id"].'&variation_id='.$project["variation_id"].'&attribute_proyecto='.(int)$project["project"].'&buy=&nonce='.$nonce);
					
					echo '<a id="iweb_duplicate'.$project["project"].'" data-copyprojectid="'.$project["project"].'" data-productsID="'.$project["product_id"].'" data-variation_id="'.$project["variation_id"].'" data-attribute_proyecto="'.(int)$project["project"].'" data-buy=""  data-nonce="'.$nonce_iweb.'" title="'.__('Edit project','Imaxel').' '.$project["project"].'" href="'.$linkd.'"><img  src="'.imaxel_myplugin_plugin_url_imaxel() . '/img/duplicate.png"/></a>';
				}
				
			}
							
			echo '</td>';
			echo '</tr>';
					
			//AJAX CONTINUE OR DUPLICATE
			echo '<script>';
			echo 'jQuery(document).ready(function() {';
			if(!$hasOrder){
			echo '
				jQuery("a#continueediting'.$project["project"].'").click(function(){
					var projectid = jQuery("a#continueediting'.$project["project"].'").attr("data-projectid");
					var attribute_proyecto = jQuery("a#continueediting'.$project["project"].'").attr("data-attribute_proyecto");
					var productCode = jQuery("a#continueediting'.$project["project"].'").attr("data-productCode");
					var productsID = jQuery("a#continueediting'.$project["project"].'").attr("data-productsID");
					var variation_id = jQuery("a#continueediting'.$project["project"].'").attr("data-variation_id");
					var page_id = jQuery("a#continueediting'.$project["project"].'").attr("data-page_id");
					var panel = jQuery("a#continueediting'.$project["project"].'").attr("data-panel");
					var nonce = jQuery("a#continueediting'.$project["project"].'").attr("data-nonce");
						
					jQuery.ajax({
						type: "POST",
						url: "'.$link.'",
						data : {
							action: "my_imaxel_continue_edit", 
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
				
				jQuery("a#iweb_continueediting'.$project["project"].'").click(function(){
					var 
						projectid = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-projectid"),
						attribute_proyecto = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-attribute_proyecto"),
						productCode = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-productCode"),
						productsID = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-productsID"),
						variation_id = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-variation_id"),
						page_id = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-page_id"),
						panel = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-panel"),
						nonce = jQuery("a#iweb_continueediting'.$project["project"].'").attr("data-nonce");
						
					jQuery.ajax({
						type: "POST",
						url: "'.$link.'",
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
				';
			}
			echo '
				jQuery("a#duplicate'.$project["project"].'").click(function(){
					var 
						copyprojectid = jQuery("a#duplicate'.$project["project"].'").attr("data-copyprojectid"),
						attribute_proyecto = jQuery("a#duplicate'.$project["project"].'").attr("data-attribute_proyecto"),
						productCode = jQuery("a#duplicate'.$project["project"].'").attr("data-productCode"),
						productsID = jQuery("a#duplicate'.$project["project"].'").attr("data-productsID"),
						variation_id = jQuery("a#duplicate'.$project["project"].'").attr("data-variation_id"),
						buy = jQuery("a#duplicate'.$project["project"].'").attr("data-buy"),
						nonce = jQuery("a#duplicate'.$project["project"].'").attr("data-nonce");
						
					jQuery.ajax({
						type: "POST",
						url: "'.$linkd.'",
						data : {
							action: "my_imaxel_duplicate_edit", 
							copyprojectid: copyprojectid, 
							attribute_proyecto: attribute_proyecto, 
							productCode : productCode,
							productsID: productsID,
							variation_id: variation_id,
							buy: buy,
							nonce: nonce
						},
						success: function( datac ) {
							window.location.href = datac;
						},
						error: function( datac ) {
								//alert(datac);
						}
					});
					return false;
				});
				
				
				jQuery("a#iweb_duplicate'.$project["project"].'").click(function(){
					var 
						copyprojectid = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-copyprojectid"),
						attribute_proyecto = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-attribute_proyecto"),
						productCode = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-productCode"),
						productsID = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-productsID"),
						variation_id = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-variation_id"),
						buy = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-buy"),
						nonce = jQuery("a#iweb_duplicate'.$project["project"].'").attr("data-nonce");
						
					jQuery.ajax({
						type: "POST",
						url: "'.$linkd.'",
						data : {
							action: "my_imaxel_iweb_duplicate_edit", 
							copyprojectid: copyprojectid, 
							attribute_proyecto: attribute_proyecto, 
							productCode : productCode,
							productsID: productsID,
							variation_id: variation_id,
							buy: buy,
							nonce: nonce,
						},
						success: function( datac ) {
							window.location.href = datac;
						},
						error: function( datac ) {
							//alert(datac);
						}
					});
					return false;
				});

			});
			';
			echo '</script>';
	}		
		
		?></tbody>

	</table>

<?php endif; 

}


# ===================================== #
# ==== STATUS CHANGE EXE FUNCTIONS ==== #
# ===================================== #
//ON STATUS ON PROCESSING
add_action( 'woocommerce_order_status_processing', 'imaxel_mysite_woocommerce_order_status_processing' );
function imaxel_mysite_woocommerce_order_status_processing( $order_id ) {
	error_log( print_r( '**********imaxel_helpers imaxel_mysite_woocommerce_order_status_processing ENTRA', true ) );
	global $wpdb;
	global $woocommerce;
	global $wcdn;
	error_log( "Not ready ".$order_id."");
	$nonce = wp_create_nonce("my_imaxel_continue_edit_nonce");
	
	$automatic_send = esc_attr( get_option('automatic_send') );
	error_log( "Automatic ".$automatic_send."");
	if($automatic_send==1){

		$ord = new WC_Order($order_id);

		$items = $ord->get_items();
		foreach( $items as $item) {
			$data = array( 
				'status' => 'ready',
				'client_id' => $ord->user_id
			);
			if(empty($item['proyecto'])){
				continue;
			}
			error_log( "Processing ".$item['proyecto']."");
			$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$item['proyecto']),array('%s'),array('%s'));
		}

		######### SEND TO PRODUCCTION AUTOMATICALLY ########    
		
		// OJO: ¿No debería ser una zona horaria preestablecida por configuración?
		// date_default_timezone_set('Europe/Madrid');	

		$output = imaxel_get_itransact_args($ord,'');

		$trxJobs = json_decode($output["jobs"]);
		error_log( print_r("trxJobs are:", true));
		error_log( print_r($trxJobs, true ));
		/* 
			Build API Jobs information
		*/
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
		/*
			API Checkout information
		*/
		$apiCheckout = array(
			"billing"=>array(
					"email"=>"".$output["billing_email"],
					"firstName"=>"".$output["billing_first_name"],
					"lastName"=>"".$output["billing_last_name"],
					"phone"=>"".$output["billing_phone"]
			),
			"saleNumber"=>"".$order_id,
			"payment"=>array(
					"name"=> "".$output["payment_method"],
					"instructions"=>""
			),
			"shippingMethod" => array(
				"amount"=> ($output["shipping_method_cost"]=="" ? 0 : (double)$output["shipping_method_cost"]),
				"name" => "".$output["shipping_method"],
				"instructions" => "".$output["shipping_method_name"]
			),
			// OJO: HARDCODED !!!
			"discount"=>array(
					"amount"=> 0,
					"name"=> "",
					"code"=> ""
			),
			"total" => (double)$output["total"]
		);
		
		if($output["shipping_method"]=="local_pickup"){

			// OJO: HARCODED!!!
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
		/*
			API Call
		 */
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
		
		$jsonParams = json_encode(array(
			"jobs" => $apiJobs,
			"checkout" => $apiCheckout,
			"policy" => $policy,
			"signedPolicy" => $signedPolicy
		));
		error_log( "Parametros ".$jsonParams."");
		$remoteOrderStr = ''.imaxel_httpPostOrder($endpoint."/orders", $jsonParams);
		error_log( "Data ".$remoteOrderStr."");

		
		/*
		 Register as produced local projects
		 */
			
		$remoteOrder = json_decode($remoteOrderStr);
		$remoteJobs = $remoteOrder->jobs;
		
		error_log( "*******imaxel_mysite_woocommerce_order_status_processing: Processing Jobs! ");
			
		for($i=0; $i<count($remoteJobs); $i++){
			if( $remoteJobs[$i]=="" ){
				error_log( "*******imaxel_mysite_woocommerce_order_status_processing:  No Jobs!!!");
			} else {				
				$data = array( 
					'price' => $remoteJobs[$i]->project->design->price,
					'data_project' => json_encode($remoteJobs[$i]->project),
					'status' => 'produced'
				);
				error_log( "*******imaxel_helpers update imaxel_projects status produced! ");
				global $wpdb;
				$result=$wpdb->update($wpdb->prefix.'imaxel_projects', $data, array( 'project' => $remoteJobs[$i]->project->id ), array('%s','%s','%s'), array('%s') );	 //,'%s'
			}
		}
	}
	
}




//A PRESENT!!
//IF YOU WANT CHANGE STATUS PROJECTS ON PROCESSING OR ON HOLD
/** 

//ON STATUS COMPLETED
function mysite_woocommerce_order_status_completed( $order_id) {
	global $wpdb;
	global $woocommerce;
	global $wcdn;
	error_log( "Not ready ".$order_id."");
	$ord = new WC_Order($order_id);
	$items = $ord->get_items();
        //error_log( "Not ready ".$item['proyecto']."", 0 );
        foreach($items as $item)    {
            $data = array( 
					'status' => 'ready',
					//'client_id' => $ord->user_id
			);
			error_log( "ready ".$item['proyecto']."");
			$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$item['proyecto']),array('%s'),array('%s'));
			//error_log($wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$item['proyecto']),array('%s'),array('%s')));	
        }

	
	
}
add_action( 'woocommerce_order_status_completed', 'mysite_woocommerce_order_status_completed' );

//ON STATUS ON HOLD
function mysite_woocommerce_order_status_on_hold( $order_id ) {
	global $wpdb;
	global $woocommerce;
	global $wcdn;
	error_log( "Not ready ".$order_id."");
	$ord = new WC_Order($order_id);
	//$ord->user_id
	$items = $ord->get_items();
        //error_log( "Not ready ".$item['proyecto']."", 0 );
        foreach($items as $item)    {
            $data = array( 
					'status' => 'ready',
					//'client_id' => $ord->user_id
			);
			error_log( "ready ".$item['proyecto']."");
			$result=$wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$item['proyecto']),array('%s'),array('%s'));
			//error_log($wpdb->update($wpdb->prefix.'imaxel_projects',$data,array('project'=>$item['proyecto']),array('%s'),array('%s')));
        }


}
add_action( 'woocommerce_order_status_on-hold', 'mysite_woocommerce_order_status_on_hold' );
**/

?>