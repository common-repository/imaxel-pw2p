<?php
/**
* Plugin Name: Imaxel editors 
* Plugin URI: http://www.imaxel.com
* Description: A wordpress plugin to integrate imaxel with woocommerce and wordpress.
* Version: 4.2.1
* Text Domain: Imaxel
* Author: Imaxel
* Author URI: http://www.imaxel.com
* License: All right reserved 2016
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$imaxeldb_db_version = '1.1';
$imaxelplugin_version = '4.2.1';
$compatible_wp_version ='4.5.2 - 4.7';
$compatible_woo_version ='2.5.5 - 2.6.9';
global $imaxeldb_db_version;
global $imaxelplugin_version;
global $compatible_wp_version;
global $compatible_woo_version;
add_thickbox();
add_action('admin_menu', 'imaxel_plugin_setup_menu');

# ============== #
# ==== MENU ==== #
# ============== #

function imaxel_plugin_setup_menu(){
		//Admin panel menu and submenu
		$url_icon=plugin_dir_url( __FILE__ )."/img/imaxel.png";
        add_menu_page( 'Imaxel editors', 'Imaxel editors', 'manage_options', 'imaxel-plugin', 'imaxel_init', $url_icon,25);
        //add_submenu_page( 'imaxel-plugin', ''.__('Info','Imaxel').'', ''.__('Info','Imaxel').'', 'manage_options', 'imaxel-plugin', '' );
		//ahora es comun el setting de Iweb y el de HTML5
		add_submenu_page( 'imaxel-plugin', ''.__('Settings','Imaxel').'', ''.__('Settings','Imaxel').'', 'manage_options', 'imaxel-iweb-options', 'imaxel_iweb_options' );
        //add_submenu_page( 'imaxel-plugin', ''.__('Settings','Imaxel').'', ''.__('Settings','Imaxel').'', 'manage_options', 'imaxel-options', 'imaxel_options' );
        add_submenu_page( 'imaxel-plugin', ''.__('Products','Imaxel').'', ''.__('Products','Imaxel').'', 'manage_options', 'imaxel-import', 'imaxel_import' );
        add_submenu_page( 'imaxel-plugin', ''.__('Projects','Imaxel').'', ''.__('Projects','Imaxel').'', 'manage_options', 'imaxel-projects', 'imaxel_project' );
		//iweb
		
        add_submenu_page( 'imaxel-plugin', ''.__('Products','Imaxel').'', ''.__('Iweb Products','Imaxel').'', 'manage_options', 'imaxel-iweb-import', 'imaxel_iweb_import' );
        add_submenu_page( 'imaxel-plugin', ''.__('Projects','Imaxel').'', ''.__('Iweb Projects','Imaxel').'', 'manage_options', 'imaxel-iweb-projects', 'imaxel_iweb_project' );
		
		//Changing the first tab name        
        $submenu['imaxel-plugin'][0][0] = ''.__('Info','Imaxel').'';
        
        //action!
		add_action( 'admin_init', 'register_imaxel_settings' );
		add_action( 'admin_init', 'register_imaxel_iweb_init_settings' );

}
register_activation_hook( __FILE__, 'imaxeldb_install_total' );


# =================================== #
# ==== INSTALL BD TABLE FUNCTION ==== #
# =================================== #



function imaxeldb_install_total(){
	global $wpdb;
	global $imaxeldb_db_version;
	global $imaxelplugin_version;
	global $compatible_wp_version;
	global $compatible_woo_version;
	
	//table config
	$table_name = $wpdb->prefix . 'imaxel_projects';
	$charset_collate = $wpdb->get_charset_collate();
	
	//data table imaxel projects
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		project varchar(55) NOT NULL,
		price varchar(55) NOT NULL,
		product varchar(255) NOT NULL,
		product_id varchar(55) NOT NULL,
		variation_id varchar(55) NOT NULL,
		data_project text NOT NULL,
		status varchar(55) DEFAULT 'created' NOT NULL,
		client_id varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	//create table
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	
	$table_name = $wpdb->prefix . 'imaxel_iweb_projects';
	$charset_collate = $wpdb->get_charset_collate();
	
	//data table imaxel projects
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		project varchar(55) NOT NULL,
		price varchar(55) NOT NULL,
		product varchar(255) NOT NULL,
		product_id varchar(55) NOT NULL,
		variation_id varchar(55) NOT NULL,
		data_project text NOT NULL,
		status varchar(55) DEFAULT 'created' NOT NULL,
		client_id varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	//create table
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	$table_name_postmeta= $wpdb->prefix . 'postmeta';
	$table_name_post= $wpdb->prefix . 'posts';
	//actualizar 
	//No se permite este tipo de ejcuciones
	$sql ="select Id,'_iweb','-1' from $table_name_post
			inner join $table_name_postmeta on  $table_name_post.ID=$table_name_postmeta.post_id
			where $table_name_postmeta.meta_value like '%proyecto%CUSTOM_TEXT%' and $table_name_postmeta.meta_key='_product_attributes' and $table_name_postmeta.post_id not in
			(Select $table_name_postmeta.post_id from $table_name_postmeta where meta_key='_iweb')";
			
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//dbDelta( $sql );
	$postsOldPlugin = $wpdb->get_results($sql);
	foreach ( $postsOldPlugin as $post)
	{ 
    	
		$wpdb->insert($table_name_postmeta,array(
		'post_id'=>$post->Id,
		'meta_key'=>'_iweb',
		'meta_value'=>-1));
    }
	
	
		
	
	
	
	
	
	//se tendria que realizar primero una busqueda y despues sustituir el ID
	
	$welcome_name = __('Imaxel','Imaxel');
	$welcome_text = __('Congratulations, you just completed the installation!','Imaxel');
	
	//option version db for future upgrades
	
}
//no use
function imaxeldb_install() {
	global $wpdb;
	global $imaxeldb_db_version;
	global $imaxelplugin_version;
	global $compatible_wp_version;
	global $compatible_woo_version;
	
	//table config
	$table_name = $wpdb->prefix . 'imaxel_projects';
	$charset_collate = $wpdb->get_charset_collate();
	
	//data table imaxel projects
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		project varchar(55) NOT NULL,
		price varchar(55) NOT NULL,
		product varchar(255) NOT NULL,
		product_id varchar(55) NOT NULL,
		variation_id varchar(55) NOT NULL,
		data_project text NOT NULL,
		status varchar(55) DEFAULT 'created' NOT NULL,
		client_id varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	//create table
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	$welcome_name = __('Imaxel','Imaxel');
	$welcome_text = __('Congratulations, you just completed the installation!','Imaxel');
	
	//option version db for future upgrades
	add_option( 'imaxeldb_db_version', $imaxeldb_db_version );
}
//no use
function imaxel_iweb_db_install() {
	global $wpdb;
	global $imaxeldb_db_version;
	global $imaxelplugin_version;
	global $compatible_wp_version;
	global $compatible_woo_version;
	
	//table config
	$table_name = $wpdb->prefix . 'imaxel_iweb_projects';
	$charset_collate = $wpdb->get_charset_collate();
	
	//data table imaxel projects
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		project varchar(55) NOT NULL,
		price varchar(55) NOT NULL,
		product varchar(255) NOT NULL,
		product_id varchar(55) NOT NULL,
		variation_id varchar(55) NOT NULL,
		data_project text NOT NULL,
		status varchar(55) DEFAULT 'created' NOT NULL,
		client_id varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	//create table
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	$welcome_name = __('Imaxel','Imaxel');
	$welcome_text = __('Congratulations, you just completed the installation!','Imaxel');
	
	//option version db for future upgrades
	add_option( 'imaxeldb_db_version', $imaxeldb_db_version );
}

# ======================== #
# ==== OPTIONS PLUGIN ==== #
# ======================== #

function register_imaxel_settings() {
	//Register our options
	register_setting( 'imaxel-settings-group', 'public_key' );
	register_setting( 'imaxel-settings-group', 'private_key' );
	register_setting( 'imaxel-settings-group', 'url_base' );
	register_setting( 'imaxel-settings-group', 'productos_data' );
	register_setting( 'imaxel-settings-group', 'fecha' );
	register_setting( 'imaxel-settings-group', 'cart_page' );
	register_setting( 'imaxel-settings-group', 'automatic_send' );
	register_setting( 'imaxel-settings-group', 'wc_status_automatic_send' );
	register_setting( 'imaxel-settings-group', 'url_base_api' );
	
}

function register_imaxel_iweb_init_settings() {
	//Register our options
	register_setting( 'imaxel_iweb-settings-group', 'public_key' );
	register_setting( 'imaxel_iweb-settings-group', 'private_key' );
	register_setting( 'imaxel_iweb-settings-group', 'url_base' );
	register_setting( 'imaxel_iweb-settings-group', 'productos_data' );
	register_setting( 'imaxel_iweb-settings-group', 'fecha' );
	register_setting( 'imaxel_iweb-settings-group', 'cart_page' );
	register_setting( 'imaxel_iweb-settings-group', 'automatic_send' );
	register_setting( 'imaxel_iweb-settings-group', 'wc_status_automatic_send' );
	register_setting( 'imaxel_iweb-settings-group', 'url_base_iweb_api' );
	
	
}
 
 
# ================================= #
# ==== INFO PAGE CONTROL PANEL ==== #
# ================================= #

function imaxel_init(){
        //Dashboard Tab
        echo "<h2>".__('Imaxel editors','Imaxel')."</h2>";
        
        //Get info
        global $imaxeldb_db_version;
        global $imaxelplugin_version;
        global $compatible_wp_version;
        global $compatible_woo_version; 
        
        $woo_version = imaxel_wpbo_get_woo_version_number();
        $wp_version = get_bloginfo('version');
	//	$imaxel_pw2p_version=imaxel_wpbo_get_imaxel_pw2p_number();
      //  $imaxel_pw2p_active=imaxel_wpbo_get_imaxel_pw2p_isactive();
        //print info
        echo '<div id="post-body" class="metabox-holder column-2">
					<div class="card" style="float: left; margin-left: 10px; width: 48%;">
						<h3 class="title">'.__('About <span style="color: orange;">Imaxel editors</span>','Imaxel').'</h3>
						<p>'.__('This plugin connects Wordpress to Imaxel pW2P cloud platform to enable the creation and sales of personalized products such as prints, photobooks, canvas, collages, gifts, t-shirts, mugs and others directly in woocommerce and wordpress embedding the imaxel pW2P online editors.','Imaxel').'</p>
						
						<h3 class="title">'.__('System Requirements to enable personalizable products','Imaxel').'</h3>
						<p>'.__('1. Wordpress compatible version<br/> 2. WooCoomerce plugin compatible version.<br/>3. Active account in Imaxel pW2P external platform. <br/>Contact imaxel.com for further details.','Imaxel').'</p>
					</div>
					
					<div class="card" style="float: left; margin-left: 10px; width: 48%;">	
						<p><strong class="title">'.__('This plugin version is:','Imaxel').'</strong> '.$imaxelplugin_version.'<br/></p>
						<hr/>
						<p><strong class="title">'.__('This plugin version is tested up to','Imaxel').'</strong><br/>
						<strong>- '.__('Wordpress version:','Imaxel').'</strong> '.$compatible_wp_version.'<br/>
						<strong>- '.__('Woocommerce plugin version:','Imaxel').'</strong> '.$compatible_woo_version.'<br/></p>
						<hr/>
						<p><strong class="title">'.__('Use only tested versions to avoid functioning issues.<br/>
						This site is currently using versions:','Imaxel').'</strong><br/>
						<strong>- '.__('Wordpress version:','Imaxel').'</strong> '.$wp_version.'<br/>
						<strong>- '.__('Woocommerce plugin version:','Imaxel').'</strong> '.$woo_version.'<br/>
						
					</div>
			</div>';
			
		//<strong>- '.__('Imaxel for WordPress plugin version:','Imaxel').'</strong> '.$imaxel_pw2p_version.''.$imaxel_pw2p_active.'<br/></p>		
//<br/>3. Desactive old plugin Imaxel for WordPress.
		/*if ( in_array( 'imaxel-pw2p/imaxel.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		{
			echo 'caca';
		}*/
			
			
}

# ===================================== #
# ==== SETTINGS PAGE CONTROL PANEL ==== #
# ===================================== #

function imaxel_options()
{
		//Options TAB
        echo "<h2>".__('imaxel pW2P plugin settings','Imaxel')."</h2>";
        
        //Form to update options
        ?>
        <div class="wrap">
		<form method="post" action="options.php">
		    <?php settings_fields( 'imaxel-settings-group' ); ?>
		    <?php do_settings_sections( 'imaxel-settings-group' ); ?>
		    <table class="form-table">
		        <tr valign="top">
		        <td scope="row" style="width: 340px;"><?php _e('Introduce <strong>“Public key”</strong> supplied by Imaxel','Imaxel'); ?></td>
		        <td><input type="text" name="public_key" size="55" value="<?php echo esc_attr( get_option('public_key') ); ?>" /></td>
		        </tr>
		         
		        <tr valign="top">
		        <td scope="row"><?php _e('Introduce <strong>“Private key”</strong> supplied by Imaxel','Imaxel'); ?></td>
		        <td><input type="text" name="private_key" size="55" value="<?php echo esc_attr( get_option('private_key') ); ?>" /></td>
		        </tr>
		        
		        <?php 
			        $url_base = get_option('url_base'); 
			        if($url_base==""){ $url_base=plugin_dir_url(__FILE__); }
					
			    ?>
			    <input type="hidden" name="url_base" size="55" value="<?php echo esc_attr( $url_base ); ?>" />
		        
		        
		        <?php
			      $data = imaxel_get_all_products();
				  
				  if($data!=""){
				  }else{
					  $data = get_option('productos_data');
				  }
				  
				  if($data!=""){
				  	$fecha = date("Y-m-d H:i:s");  
				  }else{
					$fecha =  get_option('fecha');
				  }
			    ?>
		        <input type="hidden" name="productos_data" value="<?php echo esc_attr( $data ); ?>" />		  
				<input type="hidden" name="url_base_api" size="55" value="<?php 
					if(esc_attr( get_option('url_base_api') )==""){ echo ('http://services.imaxel.com/api/v3');}
					else{echo esc_attr( get_option('url_base_api') ); }
				?>" />
		        
		        <tr valign="top">
		        <td scope="row"><?php _e('select the actual <strong>“shopping cart”</strong> page used in Woo','Imaxel'); ?></td>
		        <td><?php imaxel_combo_select_page_callback() ?></td>
		        </tr>
		        
		        <tr valign="top">
		        <td scope="row"><?php _e('Activate <strong>automatic production</strong>','Imaxel'); ?></td>
		        <td><input type="checkbox" name="automatic_send" 
		        <?php if(esc_attr( get_option('automatic_send') )==1){ echo 'checked="checked" '; } ?> value="1"/> <?php _e(' after Woo order status changes to <strong>“Processing”</strong> ','Imaxel'); ?>
		        <?php
				  // For future changing status of automatic send to production
			      /*$status = wc_get_order_statuses(); 
			      echo '<pre>';print_r($status);echo '</pre>';
			      echo '<select name="wc_status_automatic_send">';
			      echo '<option value="#">'; 
			      _e('Select one...','Imaxel');
			      echo '</option>';
			      foreach ($status as $k => $v) {
				     echo '<option '; 
				     if(esc_attr( get_option('wc_status_automatic_send') )==$status[$k]){ 
					     	echo 'selected="selected" '; 
					    }
				     echo 'value="'.$status[$k].'">'.$v.'</option>';
			      }
			      echo '</select>';*/
			      echo '<input type="hidden" name="wc_status_automatic_send" value="wc-processing"/>';
			    ?>
		        </td>
		        </tr>
		        
		    </table>
		    <?php submit_button(__('Save and update','Imaxel')); ?>
		    <p><em><?php _e('Last connection to imaxel pW2P platform on:','Imaxel'); ?> <?php echo '<strong style="color: green;">'.esc_attr( $fecha ),'</strong>'; ?><input type="hidden" name="fecha" value="<?php echo esc_attr( $fecha ); ?>" /></em></p>
		    
		    
			
		</form>
        <?php
}
function imaxel_iweb_options(){
		//Options TAB
        echo "<h2>".__('Settings','Imaxel')."</h2>";
        
        //Form to update options
        ?>
        <div class="wrap">
		<form method="post" action="options.php">
		    <?php settings_fields( 'imaxel_iweb-settings-group' ); ?>
		    <?php do_settings_sections( 'imaxel_iweb-settings-group' ); ?>
		    <table class="form-table">
		        <tr valign="top">
		        <td scope="row" style="width: 340px;"><?php _e('Introduce <strong>“Public key”</strong> supplied by Imaxel','Imaxel'); ?></td>
		        <td><input type="text" name="public_key" size="55" value="<?php echo esc_attr( get_option('public_key') ); ?>" /></td>
		        </tr>
		         
		        <tr valign="top">
		        <td scope="row"><?php _e('Introduce <strong>“Private key”</strong> supplied by Imaxel','Imaxel'); ?></td>
		        <td><input type="text" name="private_key" size="55" value="<?php echo esc_attr( get_option('private_key') ); ?>" /></td>
		        </tr>
				
			   
		        
		        <?php 
			        $url_base = get_option('url_base'); 
			        if($url_base==""){ $url_base=plugin_dir_url(__FILE__); }
					
			    ?>
			    <input type="hidden" name="url_base" size="55" value="<?php echo esc_attr( $url_base ); ?>" />
		        
		      
				 <tr valign="top">
		        <td scope="row"><?php _e('Introduce <strong>“URL IwebApi”</strong> supplied by Imaxel','Imaxel'); ?></td>
		        <td><input type="text" name="url_base_iweb_api" size="55" value="<?php echo esc_attr( get_option('url_base_iweb_api') ); ?>" /></td>
		        </tr>
		        <?php
			      $data = imaxel_iweb_get_all_products();
				  
				  if($data!=""){
				  }else{
					  $data = get_option('productos_data');
				  }
				  
				  if($data!=""){
				  	$fecha = date("Y-m-d H:i:s");  
				  }else{
					$fecha =  get_option('fecha');
				  }
			    ?>
		        <input type="hidden" name="productos_data" value="<?php echo esc_attr( $data ); ?>" />		       
		        
		         <tr valign="top">
		        <td scope="row"><?php _e('select the actual <strong>“shopping cart”</strong> page used in Woo','Imaxel'); ?></td>
		        <td><?php imaxel_iweb_combo_select_page_callback() ?></td>
		        </tr>
		        
		        <tr valign="top">
		        <td scope="row"><?php _e('Activate <strong>automatic production</strong>','Imaxel'); ?></td>
		        <td><input type="checkbox" name="automatic_send" 
		        <?php if(esc_attr( get_option('automatic_send') )==1){ echo 'checked="checked" '; } ?> value="1"/> <?php _e(' after Woo order status changes to <strong>“Processing”</strong> ','Imaxel'); ?>
		        <?php
				 
			      echo '<input type="hidden" name="wc_status_automatic_send" value="wc-processing"/>';
			    ?>
		        </td>
		        </tr>
		        
		    </table>
		    <?php submit_button(__('Save and update','Imaxel')); ?>
		    <p><em><?php _e('Last connection to Imaxel platform on:','Imaxel'); ?> <?php echo '<strong style="color: green;">'.esc_attr( $fecha ),'</strong>'; ?><input type="hidden" name="fecha" value="<?php echo esc_attr( $fecha ); ?>" /></em></p>
		    
		    
			
		</form>
        <?php
}

# ===================================== #
# ==== PRODUCTS PAGE CONTROL PANEL ==== #
# ===================================== #

function imaxel_import(){
        //Import products tab
        echo "<h2>".__('imaxel pW2P personalizable products','Imaxel')."</h2>";
        
        //Update products file from imaxel - hidden form with button
        $fecha = get_option('fecha'); 
        echo '<form method="post" action="options.php">';
		settings_fields( 'imaxel-settings-group' ); 
		do_settings_sections( 'imaxel-settings-group' ); 
			      
		$data = imaxel_get_all_products();
		if($data!=""){ }else{ $data = get_option('productos_data'); }				  
		if($data!=""){ $fecha = date("Y-m-d H:i:s"); }else{ $fecha =  get_option('fecha'); }
	    echo '<input type="hidden" name="public_key" value="'.esc_attr( get_option('public_key') ).'" />';
	    echo '<input type="hidden" name="private_key" value="'.esc_attr( get_option('private_key') ).'" />';
	    echo '<input type="hidden" name="url_base" value="'.esc_attr( get_option('url_base') ).'" />';
		echo '<input type="hidden" name="productos_data" value="'.esc_attr( $data ).'" />';
		echo '<input type="hidden" name="cart_page" value="'.esc_attr(get_option('cart_page')).'" />';
		echo '<input type="hidden" name="automatic_send" ';
		if(esc_attr( get_option('automatic_send') )==1){ echo 'value="1" '; }else{ echo 'value="" '; }
		echo '/>';
		echo '<input type="hidden" name="wc_status_automatic_send" value="wc-processing"/>';
		echo '<input type="hidden" name="fecha" value="'.esc_attr( $fecha ).'" />';
		
		//Print datetime and button
        echo '<p style="color: orange;">'; 
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Import now','Imaxel').'"> ';
        echo '<strong>'.__('Last products import:','Imaxel').'</strong> '.$fecha.'';
		echo '</p>';
		echo '</form>';
		
		        
        /**
		 * Check if WooCommerce is active
		 **/
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			
			//Create product in woocommerce
			if(($_POST["sku"]!="") && ($_POST["accion"]=="create")){
				
				$post = array(
				    'post_author' => $user_id,
				    'post_content' => '',
				    'post_status' => "publish",
				    'post_title' => sanitize_text_field($_POST["title"]),
				    'post_parent' => '',
				    'post_type' => "product",
			    );
				$post_id = wp_insert_post( $post, $wp_error );
				     
				//update terms and meta of the product inserted
				wp_set_object_terms($post_id, 'variable', 'product_type');
				
				update_post_meta( $post_id, '_visibility', 'visible' );
				update_post_meta( $post_id, '_stock_status', 'instock');
				update_post_meta( $post_id, 'total_sales', '0');
				update_post_meta( $post_id, '_downloadable', 'no');
				update_post_meta( $post_id, '_virtual', 'no');
				update_post_meta( $post_id, '_regular_price', sanitize_text_field($_POST["price"]) );
				update_post_meta( $post_id, '_featured', "no" );
				update_post_meta( $post_id, '_sku', ''); //$_POST["sku"]
				update_post_meta( $post_id, '_price', sanitize_text_field($_POST["price"]) );
				
				
				if(empty($_POST["_html5WorkType"])==false&&$_POST["_html5WorkType"]=="printspack")
				{
					update_post_meta( $post_id, '_sold_individually', "yes" );
				}
				else
				{
					update_post_meta( $post_id, '_sold_individually', "" );
				}
				
				update_post_meta( $post_id, '_manage_stock', "no" );
				update_post_meta( $post_id, '_backorders', "no" );
				update_post_meta( $post_id, '_stock', "" );
				update_post_meta( $post_id, '_iweb', '-1' );
				update_post_meta( $post_id, '_html5WorkType', $_POST["_html5WorkType"] );
				
				
				wp_set_object_terms($post_id, $avail_attributes, 'proyecto');
				
				//IMPORTANT IMAXEL PRODUCT NEED THIS!!
				$thedata = Array('proyecto'=>Array(
					'name'=>'proyecto',
					'value'=>'CUSTOM_TEXT',
					'is_visible' => '1', 
					'is_variation' => '1',
					'is_taxonomy' => '0'
					));
				update_post_meta( $post_id,'_product_attributes',$thedata);
					
				$my_post = array(
				    'post_title'=> 'Variación '.$post_id.' 1',
				    'post_name' => 'product-' . $post_id . '-variation-1',
				    'post_status' => 'publish',
				    'post_parent' => $post_id, //post is a child post of product post
				    'post_type' => 'product_variation', //set post type to product_variation
				    'guid'=>home_url() . '/?product_variation=product-' . $post_id . '-variation-1'
				);
				
				//Insert ea. post/variation into database:
				$attID = wp_insert_post( $my_post );
				
				//set IDs for product_variation posts:
				$variation_id = $attID;
				
				//Create product_variation:
				update_post_meta($variation_id, 'attribute_proyecto', '');
				update_post_meta($variation_id, '_price', sanitize_text_field($_POST["price"]));
				update_post_meta($variation_id, '_regular_price', sanitize_text_field($_POST["price"]));
				update_post_meta($variation_id, '_sku', sanitize_text_field($_POST["sku"]));
				update_post_meta($variation_id, '_iweb', '-1');
				

				wp_set_object_terms($variation_id, $avail_attributes, 'proyecto');
				$thedata = Array('proyecto'=>Array(
				    'name'=>'Proyecto',
				    'value'=>'CUSTOM_TEXT',
				    'is_visible' => '1', 
				    'is_variation' => '1',
				    'is_taxonomy' => '0'
				));
				update_post_meta( $variation_id,'_product_attributes',$thedata);
    
				//Message product created
				echo '<div class="updated"><p>';
				     _e( 'Product created width ID <strong>'.$post_id.'</strong> successfully!', 'Imaxel' );
				echo '</p></div>';
			}
			//END Create product in woocommerce

		    //Get product list from imaxel
		    $data_now = imaxel_get_all_products();
		    $data_now_processed=json_decode($data_now);
		    $data_now_processed=imaxel_objectToArray($data_now_processed);

		    //Insert then in an array
		    $skus_imaxel = array();
		    foreach($data_now_processed as $result_now) {
			    array_push($skus_imaxel,$result_now["code"]);
			}
			
			//Get product data option
			$data=get_option('productos_data');
			$data_processed=json_decode($data);
			$data_processed=imaxel_objectToArray($data_processed);
			
			//Prepare de woo loop
			$args = array( 'post_type' => 'product', 'posts_per_page' => 50, );
			$loop = new WP_Query( $args );
			$skus = array();
			$wooid = array();
			
			//a loop!
			while ( $loop->have_posts() ) : $loop->the_post(); 
				global $product;
				$imx = new WC_Product_Variable($product->id);
				$available_variations = $imx->get_available_variations();
				//$available_variations = $product->get_available_variations();
				array_push($skus,$product->get_sku());
				foreach($available_variations as $vart){
					array_push($skus,$vart["sku"]);
					$wooid["".$vart["sku"].""] = $product->id;
				}
			endwhile; 
			wp_reset_query(); 
			
			
			//Print the table!			
			$j=0;
			echo '<table class="wp-list-table widefat fixed striped pages" style="width: 98%;">
					<thead>
					<tr>
						<th style="width: 9%;">'.__('<span style="color: orange;">pW2P</span> product','Imaxel').'</th>
						<th style="width: 8%;">'.__('Woo Product','Imaxel').'</th>
						<th style="width: 15%;">'.__('Product','Imaxel').'</th>
						<th style="width: 7%;">'.__('In <span style="color: orange;">pW2P</span>','Imaxel').'</th>
						<th style="width: 7%;">'.__('In Woo','Imaxel').'</th>
						<th style="width: 30%;">'.__('Variants','Imaxel').'</th>
						<th style="width: 14%;">'.__('Action','Imaxel').'</th>
					</tr>
					</thead>';

			//Proccess our database product list
			foreach($data_now_processed as $result) {
				//Line by line!
				echo '<tr>
						<td><form method="post" action="admin.php?page=imaxel-import">
						<input type="hidden" name="page" value="imaxel-import"/>'.$result['code'].'
						<input type="hidden" name="sku" value="'.$result['code'].'"/></td>
						<td><a href="post.php?post='.$wooid["".$result['code'].""].'&action=edit">'.$wooid["".$result['code'].""].'</a></td>
						<td>'.$result['name']["default"].'
						<input type="hidden" name="title" value="'.$result['name']["default"].'"/></td>
						<td>'; 
						
						if(empty($result['module'])==false&&empty($result['module']['code'])==false)
						{
							echo  '<input type="hidden" name="_html5WorkType" value="'.$result['module']['code'].'"/></td>';
							
						}
						else
						{
							echo '<input type="hidden" name="_html5WorkType" value=""/></td>';
						}
						if(in_array($result['code'], $skus_imaxel)){ 
							echo '<span class="dashicons dashicons-yes" style="color: green;" ></span>';
						}else{
							echo '<span class="dashicons dashicons-no" style="color: red;"></span>';
						}
			 			echo '</td>
						<td>'; 
						if(in_array($result['code'], $skus)){ 
							echo '<span class="dashicons dashicons-yes" style="color: green;"  ></span >';
						}else{
							echo '<span class="dashicons dashicons-no" style="color: red;"></span >';
						}
			 			echo '</td>
						<td>'; 
						
						//The lowest price from the variants
						$price=10000000;
						foreach($result['variants'] as $variaciones) {
							if(count($variaciones["price_values"])>=1){
								
								for($i=0; $i<count($variaciones["price_values"]); $i++){
									echo ''.$variaciones["code"].' - '.$variaciones["name"]["default"].' - '.$variaciones["price_values"][$i]["units"].' - '.$variaciones["price_values"][$i]["price"].' '.get_woocommerce_currency_symbol().'<br/>';
									if($variaciones["price_values"][$i]["price"]<$price){
										$price=$variaciones["price_values"][$i]["price"];
									}	
								}
							}else{
								echo ''.$variaciones["code"].' - '.$variaciones["name"]["default"].' - '.$variaciones["price"].' '.get_woocommerce_currency_symbol().'<br/>';	
								if($variaciones["price"]<$price){
									$price=$variaciones["price"];
								}
							}
						}
						
						if($wooid["".$result['code'].""]!=""){
							//Sincronize prices
							update_post_meta( $wooid["".$result['code'].""], '_regular_price', sanitize_text_field($price) );
							update_post_meta( $wooid["".$result['code'].""], '_price', sanitize_text_field($price) );
							
							update_post_meta( ($wooid["".$result['code'].""]+1), '_regular_price', sanitize_text_field($price) );
							update_post_meta( ($wooid["".$result['code'].""]+1), '_price', sanitize_text_field($price));
							wc_delete_product_transients( $wooid["".$result['code'].""] );
						}
						
						echo '<input type="hidden" name="price" value="'.$price.'"/>'; 
						echo '<hr/><strong>Options: ';
						
						//Popup!! to show on info click!
						echo '<div id="'.$result['code'].'" style="display:none;">';
						echo '<h3>'.$result['name']["default"].' variations</h3><p>';
						foreach($variaciones['options'] as $options) {
							foreach($options["values"] as $valores) {
								echo '<strong>'.$options["name"]["default"].'</strong> - '.$valores["code"].' ('.$valores["price"].' '.get_woocommerce_currency_symbol().')<br/>';
							}
						echo '<br/>';	
						}
						echo '</p></div>';
						//info! icon
						echo '<a href="#TB_inline?width=600&height=550&inlineId='.$result['code'].'" class="thickbox"><span class="dashicons dashicons-info" style="color: #427fde;"></span></a>';
						echo '</strong>';
						echo '</td>'; 
						
						echo '<td>'; 
						if(in_array($result['code'], $skus)){ 
							if(in_array($result['code'], $skus_imaxel)){}else{
							//For future delete this product in Woo	
							/*echo '<input type="hidden" name="accion" value="delete"/>
								<input type="submit" class="button button-primary button-large" value="'.__('Delete in Woo','Imaxel').'"/>	';
							*/
							}
						}else{
							echo '<input type="hidden" name="accion" value="create"/>
							<input type="submit" class="button button-primary button-large" value="'.__('Create product in Woo','Imaxel').'"/>';
						}
						echo '</form>';
			 			echo '</td>
					</tr>';
			}
			echo '</table>';
			
			
		    
		}else{
			
			echo __('Sorry, it\'s seem that you may active first the woocommerce plugin.','Imaxel');
			
		}
}

function imaxel_iweb_import(){
        //Import products tab
        echo "<h2>".__('imaxel Iweb personalizable products','Imaxel')."</h2>";
        
        //Update products file from imaxel - hidden form with button
        $fecha = get_option('fecha'); 
        echo '<form method="post" action="options.php">';
		settings_fields( 'imaxel_iweb-settings-group' ); 
		do_settings_sections( 'imaxel_iweb-settings-group' ); 
			      
		$data = imaxel_iweb_get_all_products();
		if($data!=""){ }else{ $data = get_option('productos_data'); }				  
		if($data!=""){ $fecha = date("Y-m-d H:i:s"); }else{ $fecha =  get_option('fecha'); }
	    echo '<input type="hidden" name="public_key" value="'.esc_attr( get_option('public_key') ).'" />';
	    echo '<input type="hidden" name="private_key" value="'.esc_attr( get_option('private_key') ).'" />';
	    echo '<input type="hidden" name="url_base" value="'.esc_attr( get_option('url_base') ).'" />';
		echo '<input type="hidden" name="productos_data" value="'.esc_attr( $data ).'" />';
		echo '<input type="hidden" name="cart_page" value="'.esc_attr(get_option('cart_page')).'" />';
		echo '<input type="hidden" name="automatic_send" ';
		if(esc_attr( get_option('automatic_send') )==1){ echo 'value="1" '; }else{ echo 'value="" '; }
		echo '/>';
		echo '<input type="hidden" name="wc_status_automatic_send" value="wc-processing"/>';
		echo '<input type="hidden" name="fecha" value="'.esc_attr( $fecha ).'" />';
		echo '<input type="hidden" name="url_base_iweb_api" value="'.esc_attr( get_option('url_base_iweb_api') ).'" />';
		//Print datetime and button
        echo '<p style="color: orange;">'; 
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Import now','Imaxel').'"> ';
        echo '<strong>'.__('Last products import:','Imaxel').'</strong> '.$fecha.'';
		echo '</p>';
		echo '</form>';
		
		        
        /**
		 * Check if WooCommerce is active
		 **/
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			
			//Create product in woocommerce
			if((empty($_POST)==false)&&($_POST["sku"]!="") && ($_POST["accion"]=="create")){
				
				$post = array(
				    'post_author' => $user_id,
				    'post_content' => '',
				    'post_status' => "publish",
				    'post_title' => sanitize_text_field($_POST["title"]),
				    'post_parent' => '',
				    'post_type' => "product",
			    );
				$post_id = wp_insert_post( $post, $wp_error );
				     
				//update terms and meta of the product inserted
				wp_set_object_terms($post_id, 'variable', 'product_type');
				
				update_post_meta( $post_id, '_visibility', 'visible' );
				update_post_meta( $post_id, '_stock_status', 'instock');
				update_post_meta( $post_id, 'total_sales', '0');
				update_post_meta( $post_id, '_downloadable', 'no');
				update_post_meta( $post_id, '_virtual', 'no');
				update_post_meta( $post_id, '_regular_price', sanitize_text_field($_POST["price"]) );
				update_post_meta( $post_id, '_featured', "no" );
				update_post_meta( $post_id, '_sku', ''); //$_POST["sku"]
				update_post_meta( $post_id, '_price', sanitize_text_field($_POST["price"]) );
				
				if(empty($_POST["iwebWorkType"])==false&&$_POST["iwebWorkType"]=="1")
				{
						update_post_meta( $post_id, '_sold_individually', "yes" );
				}
				else
				{
					update_post_meta( $post_id, '_sold_individually', "" );
				}
				
				
				update_post_meta( $post_id, '_manage_stock', "no" );
				update_post_meta( $post_id, '_backorders', "no" );
				update_post_meta( $post_id, '_stock', "" );
				update_post_meta( $post_id, '_iweb', '1' );
				update_post_meta( $post_id, '_iwebWorkType', $_POST["iwebWorkType"] );
				
				
				wp_set_object_terms($post_id, $avail_attributes, 'proyecto');
				
				//IMPORTANT IMAXEL PRODUCT NEED THIS!!
				$thedata = Array('proyecto'=>Array(
					'name'=>'proyecto',
					'value'=>'CUSTOM_TEXT',
					'is_visible' => '1', 
					'is_variation' => '1',
					'is_taxonomy' => '0'
					));
				update_post_meta( $post_id,'_product_attributes',$thedata);
					
				$my_post = array(
				    'post_title'=> 'Variación '.$post_id.' 1',
				    'post_name' => 'product-' . $post_id . '-variation-1',
				    'post_status' => 'publish',
				    'post_parent' => $post_id, //post is a child post of product post
				    'post_type' => 'product_variation', //set post type to product_variation
				    'guid'=>home_url() . '/?product_variation=product-' . $post_id . '-variation-1'
				);
				
				//Insert ea. post/variation into database:
				$attID = wp_insert_post( $my_post );
				
				//set IDs for product_variation posts:
				$variation_id = $attID;
				
				//Create product_variation:
				update_post_meta($variation_id, 'attribute_proyecto', '');
				update_post_meta($variation_id, '_price', sanitize_text_field($_POST["price"]));
				update_post_meta($variation_id, '_regular_price', sanitize_text_field($_POST["price"]));
				update_post_meta($variation_id, '_sku', sanitize_text_field($_POST["sku"]));
				update_post_meta($variation_id, '_iweb', '1');

				wp_set_object_terms($variation_id, $avail_attributes, 'proyecto');
				$thedata = Array('proyecto'=>Array(
				    'name'=>'Proyecto',
				    'value'=>'CUSTOM_TEXT',
				    'is_visible' => '1', 
				    'is_variation' => '1',
				    'is_taxonomy' => '0'
				));
				update_post_meta( $variation_id,'_product_attributes',$thedata);
    
				//Message product created
				echo '<div class="updated"><p>';
				     _e( 'Product created width ID <strong>'.$post_id.'</strong> successfully!', 'Imaxel' );
				echo '</p></div>';
			}
			//END Create product in woocommerce

		    //Get product list from imaxel
		    $data_now = imaxel_iweb_get_all_products();
		    $data_now_processed=json_decode($data_now);
		    $data_now_processed=imaxel_iweb_objectToArray($data_now_processed);

		    //Insert then in an array
		    $skus_imaxel = array();
		    foreach($data_now_processed as $result_now) {
			    array_push($skus_imaxel,$result_now["code"]);
			}
			
			//Get product data option
			$data=get_option('productos_data');
			$data_processed=json_decode($data);
			$data_processed=imaxel_iweb_objectToArray($data_processed);
			
			//Prepare de woo loop
			$args = array( 'post_type' => 'product', 'posts_per_page' => 50, );
			$loop = new WP_Query( $args );
			$skus = array();
			$wooid = array();
			
			//a loop!
			while ( $loop->have_posts() ) : $loop->the_post(); 
				global $product;
				$imx = new WC_Product_Variable($product->id);
				$available_variations = $imx->get_available_variations();
				//$available_variations = $product->get_available_variations();
				array_push($skus,$product->get_sku());
				foreach($available_variations as $vart){
					array_push($skus,$vart["sku"]);
					$wooid["".$vart["sku"].""] = $product->id;
				}
			endwhile; 
			wp_reset_query(); 
			
			
			//Print the table!			
			$j=0;
			echo '<table class="wp-list-table widefat fixed striped pages" style="width: 98%;">
					<thead>
					<tr>
						<th style="width: 9%;">'.__('<span style="color: orange;">Iweb</span> product','Imaxel').'</th>
						<th style="width: 8%;">'.__('Woo Product','Imaxel').'</th>
						<th style="width: 15%;">'.__('Product','Imaxel').'</th>
						<th style="width: 7%;">'.__('In <span style="color: orange;">Iweb</span>','Imaxel').'</th>
						<th style="width: 7%;">'.__('In Woo','Imaxel').'</th>
						<th style="width: 30%;">'.__('Variants','Imaxel').'</th>
						<th style="width: 14%;">'.__('Action','Imaxel').'</th>
					</tr>
					</thead>';

			//Proccess our database product list
			foreach($data_now_processed as $result) {
				//Line by line!
				echo '<tr>
						<td><form method="post" action="admin.php?page=imaxel-iweb-import">
						<input type="hidden" name="page" value="imaxel-iweb-import"/>'.$result['code'].'
						<input type="hidden" name="sku" value="'.$result['code'].'"/></td>';
						if(empty($result['workType'])==false)
						{
							echo  '<input type="hidden" name="iwebWorkType" value="'.$result['workType'].'"/></td>';
							
						}
						else
						{
							echo '<input type="hidden" name="iwebWorkType" value=""/></td>';
						}
						
						
						if(empty($wooid)==false&&count($wooid)>0&&empty($wooid["".$result['code'].""])==false)
						{
							echo '<td><a href="post.php?post='.$wooid["".$result['code'].""].'&action=edit">'.$wooid["".$result['code'].""].'</a></td>';
						}
						else
							echo '<td></td>';
					
						echo '<td>'.$result['name']["default"].'
						<input type="hidden" name="title" value="'.$result['name']["default"].'"/></td>
						<td>'; 
						if(in_array($result['code'], $skus_imaxel)){ 
							echo '<span class="dashicons dashicons-yes" style="color: green;"></span>';
						}else{
							echo '<span class="dashicons dashicons-no" style="color: red;"></span>';
						}
			 			echo '</td>
						<td>'; 
						if(in_array($result['code'], $skus)){ 
							echo '<span class="dashicons dashicons-yes" style="color: green;"></span>';
						}else{
							echo '<span class="dashicons dashicons-no" style="color: red;"></span>';
						}
			 			echo '</td>
						<td>'; 
						
						//The lowest price from the variants
						$price=10000000;
						foreach($result['variants'] as $variaciones) {
							echo ''.$variaciones["code"].' - '.$variaciones["name"]["default"].' - '.$variaciones["price"].' '.get_woocommerce_currency_symbol().'<br/>';	
							if($variaciones["price"]<$price){
								$price=$variaciones["price"];
							}
						}
						
						try
						{
							if(empty($wooid)==false&&count($wooid)>0&&empty($wooid["".$result['code'].""])==false&&$wooid["".$result['code'].""]!="")
							{
								//Sincronize prices
								update_post_meta( $wooid["".$result['code'].""], '_regular_price', sanitize_text_field($price) );
								update_post_meta( $wooid["".$result['code'].""], '_price', sanitize_text_field($price) );
								
								update_post_meta( ($wooid["".$result['code'].""]+1), '_regular_price', sanitize_text_field($price) );
								update_post_meta( ($wooid["".$result['code'].""]+1), '_price', sanitize_text_field($price));
								wc_delete_product_transients( $wooid["".$result['code'].""] );
							}
						}
						catch(Exception $e)
						{
						}						
						
						
						echo '<input type="hidden" name="price" value="'.$price.'"/>'; 
						//echo '<hr/><strong>Options: ';
						
						//Popup!! to show on info click!
						echo '<div id="'.$result['code'].'" style="display:none;">';
						echo '<h3>'.$result['name']["default"].' variations</h3><p>';
						if(empty($options)==false)
						{
							foreach($variaciones['options'] as $options)
							{
								foreach($options["values"] as $valores) 
								{
									echo '<strong>'.$options["name"]["default"].'</strong> - '.$valores["code"].' ('.$valores["price"].' '.get_woocommerce_currency_symbol().')<br/>';
								}
								echo '<br/>';	
							}
						}
						
						echo '</p></div>';
						//info! icon
						//echo '<a href="#TB_inline?width=600&height=550&inlineId='.$result['code'].'" class="thickbox"><span class="dashicons dashicons-info" style="color: #427fde;"></span></a>';
						echo '</strong>';
						echo '</td>'; 
						
						echo '<td>'; 
						if(in_array($result['code'], $skus)){ 
							if(in_array($result['code'], $skus_imaxel)){}else{
							//For future delete this product in Woo	
							/*echo '<input type="hidden" name="accion" value="delete"/>
								<input type="submit" class="button button-primary button-large" value="'.__('Delete in Woo','Imaxel').'"/>	';
							*/
							}
						}else{
							echo '<input type="hidden" name="accion" value="create"/>
							<input type="submit" class="button button-primary button-large" value="'.__('Create product in Woo','Imaxel-Iweb').'"/>';
						}
						echo '</form>';
			 			echo '</td>
					</tr>';
			}
			echo '</table>';
			
			
		    
		}else{
			
			echo __('Sorry, it\'s seem that you may active first the woocommerce plugin.','Imaxel');
			
		}
}
# ===================================== #
# ==== PROJECTS PAGE CONTROL PANEL ==== #
# ===================================== #


function imaxel_project(){
		//The project tab
        echo "<h2>".__('pW2P projects','Imaxel')."</h2>";
        
        //Check if woocommerce is active
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			
			
			global $wpdb;	
			
			//Prepare de loop
			$filters = array(
			    'post_status' => 'any',
			    'post_type' => 'shop_order',
			    'posts_per_page' => 200,
			    'paged' => 1,
			    'orderby' => 'modified',
			    'order' => 'DESC'
			);
			$loop = new WP_Query($filters);
			
			//The loop			
			while ($loop->have_posts()) {
				$loop->the_post();
				$order = new WC_Order($loop->post->ID);
				$user_id=$order->user_id;
					$data_extra = $order->get_items();
					foreach($data_extra as $producto){
						if(isset($producto["proyecto"]) ){
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
				    
			} 
			wp_reset_query();
			 
			//Prepare the table projects
			$j=0;
			global $wpdb;
			
			//Search by ID
			$ID_search = isset($_GET['numberid_f']) ? abs((int)$_GET['numberid_f']) : '';
			if($ID_search!=""){ $filter_query=" WHERE project='".$ID_search."'"; }else{ $filter_query='';}
			
			//Search by user
			$ID_user = isset($_GET['imaxel_customer_id']) ? abs((int)$_GET['imaxel_customer_id']) : '';
			if($ID_user!=""){ $filter_user_query=" WHERE client_id='".$ID_user."'"; }else{ $filter_user_query='';}
			
			//Prepare pagination
			$query = "SELECT * FROM ".$wpdb->prefix."imaxel_projects ".$filter_query." ".$filter_user_query."";
			$total_query = "SELECT count(*) FROM ".$wpdb->prefix."imaxel_projects ".$filter_query." ".$filter_user_query."";
		    $total = $wpdb->get_var( $total_query );
		    $items_per_page = 100;
		    $page = isset($_GET['cpage']) ? abs((int)$_GET['cpage']) : 1;
		    $offset = ( $page * $items_per_page ) - $items_per_page;
		    $project_array = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
			
			//Print the filter form
			//By project ID
			echo '<form id="posts-filter" class="search-box-imaxel" method="get">
			<p><input type="hidden" name="page" value="imaxel-projects"/>
			<input type="search"  id="numberid_f" name="numberid_f" value="'.$ID_search.'" placeholder="'.__('Project ID','Imaxel').'">
			<input type="submit" id="search-submit" class="button" value="'.__('Filter','Imaxel').'">
			</p>
			</form>';
			
			//By customer
			echo '<form id="posts-filter" class="search-box-imaxel" method="get">
			<p><input type="hidden" name="page" value="imaxel-projects"/>';
			imaxel_customers_dropdown_list($ID_user);
			echo '<input type="submit" id="search-submit" class="button" value="'.__('Filter','Imaxel').'">
			</p>
			</form>';
			
			//Print the pagination - you can change styles on assets/css/style.css			
			echo '<div class="imaxel_pagination">';
			echo paginate_links( array(
		        'base' => add_query_arg( 'cpage', '%#%' ),
		        'format' => '',
		        'prev_text' => __('&laquo;'),
		        'next_text' => __('&raquo;'),
		        'total' => ceil($total / $items_per_page),
		        'current' => $page
		    ));
		    echo '</div>';
		    
		    //here we go with the table head
			echo '<table class="wp-list-table widefat fixed striped pages">
					<thead>
					<tr>
						<th style="width: 110px;">'.__('<span style="color: orange;">pW2P</span> project','Imaxel').'</th>
						<th>'.__('Updated on','Imaxel').'</th>
						<th style="width: 80px;">'.__('Woo Order','Imaxel').'</th>
						<th>'.__('User name','Imaxel').'</th>
						<th>'.__('Products','Imaxel').'</th>
						<th style="width: 80px;">'.__('Price','Imaxel').'</th>
						<th>'.__('Woo Status','Imaxel').'</th>
						<th style="width: 110px;">'.__('<span style="color: orange;">pW2P</span> status','Imaxel').'</th>
						<th>'.__('Action','Imaxel').'</th>
					</tr>
					</thead>';


			//Prepare to print the list
			$project_array=imaxel_objectToArray($project_array);
			
			//Loop!
			foreach($project_array as $project){
				$hasOrder = array_key_exists("".$project["project"], $order_data);
				$projectOrder = ($hasOrder) ? $order_data["".$project["project"].""] : null;

				echo '<tr id="project'.$project["project"].'">
							<td>'.$project["project"].'</td>
							<td>'.$project["time"].'</td>
							<td>'; 
							
							//The Order ID - Link to edit order
							if(	$hasOrder ){
								echo '<a href="post.php?post='.$order_data["".$project["project"].""]["order_id"].'&action=edit">'.$order_data["".$project["project"].""]["order_id"].'</a>
								<input type="hidden" name="post_id" value="'.$order_data["".$project["project"].""]["order_id"].'"/>';
							} else {
								echo __('','Imaxel');
							} 
							
							echo '</td>
							<td>';
							
							//The customer - link to profile
							if($project["client_id"]!=0){ echo '<a href="user-edit.php?user_id='.$project["client_id"].'">';}
							if( ($project["client_id"]!=0 && !$hasOrder) ||  ($project["client_id"]!=0 && $hasOrder && $projectOrder["client_id"]=="")){ 
								$data_user_woo=get_userdata($project["client_id"]); 

								if((''.$data_user_woo->display_name.'')==""){
									$nombre = ''.$data_user_woo->display_name.'';
								}else{
									$nombre = ''.$data_user_woo->user_nicename.'';
								}
									
								if (strlen(''.$nombre.'') > 25){
									echo substr(''.$nombre.'', 0, 25) . '...';
								}else{
									echo ''.$nombre.''; 
								}
								
							}elseif($hasOrder && $projectOrder["user_id"]!=""){
								$data_user_woo=get_userdata($projectOrder["user_id"]); 

								if((''.$data_user_woo->display_name.'')==""){
									$nombre = ''.$data_user_woo->display_name.'';
								}else{
									$nombre = ''.$data_user_woo->user_nicename.'';
								}
									
								if (strlen(''.$nombre.'') > 25){
									echo substr(''.$nombre.'', 0, 25) . '...';
								}else{
									echo ''.$nombre.''; 
								}
							}elseif($project["client_id"]==0){
								echo __('Unkwon user','Imaxel');
							}elseif($hasOrder){
							
								if (strlen( $projectOrder["client_id"]) > 25){
									echo substr( $projectOrder["client_id"], 0, 25) . '...';
								}else{
									echo  $projectOrder["client_id"]; 
								}
							}
							if($project["client_id"]!=0){ echo '</a>';}
							echo '</td>';
							
							//Product name
							echo '<td>'; 
							if (strlen($project["product"]) > 25){
								echo substr($project["product"], 0, 25) . '...';
							}else{
								echo $project["product"]; 
							}
							echo '</td>';
							echo '<td>'; 
							
							//Price
							if($hasOrder && $projectOrder["line_total"]!=0){ 
								echo round($order_data["".$project["project"].""]["line_total"],2); 
								echo get_woocommerce_currency_symbol();
							}else{
								echo $project["price"];
								if($project["price"]!=""){ echo get_woocommerce_currency_symbol(); }
							}
							
							//Status in Woo
							echo '</td>
							<td>'.($hasOrder ? $projectOrder["status_WC"] : '').'</td>';
							echo '<td>'; 
							
							//Status in Imaxel
							if($project["status"]=="created"){
								echo '<span style="color: orange;" class="smsimaxel'.$project["project"].'">'.__('Started','Imaxel').'</span>';
							}elseif($project["status"]=="ready"){
								echo '<span style="color: green;" class="smsimaxel'.$project["project"].'">'.__('Ready','Imaxel').'</span>';
							}elseif($project["status"]=="produced"){
								echo '<span style="color: blue;" class="smsimaxel'.$project["project"].'">'.__('Ordered','Imaxel').'</span>';
							}elseif($project["status"]=="cancelled"){
								echo '<span style="color: red;" class="smsimaxel'.$project["project"].'">'.__('Cancelled','Imaxel').'</span>';
							}else
							echo '</td>';
							echo '<td>';
							
							//Print the action button - AJAX!
							if($hasOrder && $projectOrder["order_id"]!=""){
								imaxel_my_custom_checkout_field_display_admin_order_meta($order_data["".$project["project"]."_WC"],$project["project"],$project["status"],$project["product_id"],$project["variation_id"],$projectOrder["status_WC"]);
							}else{
								imaxel_my_custom_checkout_field_display_admin_order_meta('',$project["project"],$project["status"],$project["product_id"],$project["variation_id"],'');
							}
							echo '</td>
					</tr>';
			}
			echo '</table>';
			
			//Close table
			
			//Print the pagination on botton
			echo '<div class="imaxel_pagination">';
			echo paginate_links( array(
		        'base' => add_query_arg( 'cpage', '%#%' ),
		        'format' => '',
		        'prev_text' => __('&laquo;'),
		        'next_text' => __('&raquo;'),
		        'total' => ceil($total / $items_per_page),
		        'current' => $page
		    ));
		    echo '</div>';
		   
		}else{
			//No Woo, no cry
			echo __('Sorry, it\'s seem that you may active first the woocommerce plugin.','Imaxel');
			
		}
}

function imaxel_iweb_project(){
	//The project tab
	echo "<h2>".__('Iweb projects','Imaxel')."</h2>";
	
	//Check if woocommerce is active
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		global $wpdb;	
			
		//Prepare de loop
		$filters = array(
				'post_status' => 'any',
				'post_type' => 'shop_order',
				'posts_per_page' => 200,
				'paged' => 1,
				'orderby' => 'modified',
				'order' => 'DESC'
		);
		$loop = new WP_Query($filters);
		$order_data=array();
			
			//The loop			
		while ($loop->have_posts()) {
			$loop->the_post();
			$order = new WC_Order($loop->post->ID);
			$user_id=$order->user_id;
			$data_extra = $order->get_items();
			foreach($data_extra as $producto)
			{
				if(isset($producto["proyecto"]) ){
					$order_data_id=$order->id;
					$order_data_statusWC=$order->get_status();
					$order_data_line_total=$producto["line_total"];
					$order_data_client_id=''.$order->billing_first_name . ' ' . $order->billing_last_name.'';
					$order_data_user_id=''.$user_id.'';
					$order_data["".$producto["proyecto"].""]=array(
						'order_id'=>$order_data_id,
						'status_WC'=>$order_data_statusWC,
						'line_total'=>$order_data_line_total,
						'client_id'=>$order_data_client_id,
						'user_id'=>$order_data_user_id
					);
					$order_data["".$producto["proyecto"]."_WC"]=new WC_Order($loop->post->ID);
				}
			}  
		} 
		wp_reset_query();
			 
		//Prepare the table projects
		$j=0;
		global $wpdb;
			
		//Search by ID
		$ID_search = isset($_GET['numberid_f']) ? abs((int)$_GET['numberid_f']) : '';
		if($ID_search!=""){ $filter_query=" WHERE project='".$ID_search."'"; }else{ $filter_query='';}
		
		//Search by user
		$ID_user = isset($_GET['imaxel_customer_id']) ? abs((int)$_GET['imaxel_customer_id']) : '';
		if($ID_user!=""){ $filter_user_query=" WHERE client_id='".$ID_user."'"; }else{ $filter_user_query='';}
		
		//Prepare pagination
		$query = "SELECT * FROM ".$wpdb->prefix."imaxel_iweb_projects ".$filter_query." ".$filter_user_query."";
		$total_query = "SELECT count(*) FROM ".$wpdb->prefix."imaxel_iweb_projects ".$filter_query." ".$filter_user_query."";
		$total = $wpdb->get_var( $total_query );
		$items_per_page = 100;
		$page = isset($_GET['cpage']) ? abs((int)$_GET['cpage']) : 1;
		$offset = ( $page * $items_per_page ) - $items_per_page;
		$project_array = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
		
		//Print the filter form
		//By project ID
		echo '<form id="posts-filter" class="search-box-imaxel" method="get">
		<p><input type="hidden" name="page" value="imaxel-iweb-projects"/>
		<input type="search"  id="numberid_f" name="numberid_f" value="'.$ID_search.'" placeholder="'.__('Project ID','Imaxel').'">
		<input type="submit" id="search-submit" class="button" value="'.__('Filter','Imaxel').'">
		</p>
		</form>';
			
		//By customer
		echo '<form id="posts-filter" class="search-box-imaxel" method="get">
		<p><input type="hidden" name="page" value="imaxel-iweb-projects"/>';
		imaxel_iweb_customers_dropdown_list($ID_user);
		echo '<input type="submit" id="search-submit" class="button" value="'.__('Filter','Imaxel').'">
		</p>
		</form>';
			
		//Print the pagination - you can change styles on assets/css/style.css			
		echo '<div class="imaxel_iweb_pagination">';
		echo paginate_links( array(
					'base' => add_query_arg( 'cpage', '%#%' ),
					'format' => '',
					'prev_text' => __('&laquo;'),
					'next_text' => __('&raquo;'),
					'total' => ceil($total / $items_per_page),
					'current' => $page
			));
			echo '</div>';
		    
		  //here we go with the table head
			echo '<table class="wp-list-table widefat fixed striped pages">
					<thead>
					<tr>
						<th style="width: 110px;">'.__('<span style="color: orange;">Iweb</span> project','Imaxel').'</th>
						<th>'.__('Updated on','Imaxel').'</th>
						<th style="width: 80px;">'.__('Woo Order','Imaxel').'</th>
						<th>'.__('User name','Imaxel').'</th>
						<th>'.__('Products','Imaxel').'</th>
						<th style="width: 80px;">'.__('Price','Imaxel').'</th>
						<th>'.__('Woo Status','Imaxel').'</th>
						<th style="width: 110px;">'.__('<span style="color: orange;">Iweb</span> status','Imaxel').'</th>
						<th>'.__('Action','Imaxel').'</th>
					</tr>
					</thead>';


			//Prepare to print the list
			$project_array=imaxel_iweb_objectToArray($project_array);
			
			//Loop!
			foreach($project_array as $project){
				$hasOrder = array_key_exists("".$project["project"], $order_data);
				$projectOrder = ($hasOrder) ? $order_data["".$project["project"].""] : null;

				echo '<tr id="project'.$project["project"].'">';
				echo '<td>'.$project["project"].'</td>';
				echo '<td>'.$project["time"].'</td>';
				echo '<td>'; 

				//The Order ID - Link to edit order
				if( $hasOrder ) {
					echo '<a href="post.php?post='.$projectOrder["order_id"].'&action=edit">'.$projectOrder["order_id"].'</a>
					<input type="hidden" name="post_id" value="'.$projectOrder["order_id"].'"/>';
				} else {
					echo __('','Imaxel');
				} 
							
				echo '</td>';
				echo '<td>';
		
							//The customer - link to profile
							if($project["client_id"]!=0){ 
								echo '<a href="user-edit.php?user_id='.$project["client_id"].'">';
							}
							$nombre = '';
							if( ($project["client_id"]!=0 && !$hasOrder) || ($project["client_id"]!=0 && $hasOrder && $projectOrder["client_id"]=="") ){ 
								$data_user_woo=get_userdata($project["client_id"]); 
								$nombre = (''.$data_user_woo->user_nicename.'' == '') ? 
									''.$data_user_woo->display_name :
									''.$data_user_woo->user_nicename;
									
							} elseif ($hasOrder && $projectOrder["user_id"]!=""){
								$data_user_woo=get_userdata($projectOrder["user_id"]); 
								$nombre = (''.$data_user_woo->user_nicename.'' == '') ? 
									''.$data_user_woo->display_name :
									''.$data_user_woo->user_nicename;
							} elseif($project["client_id"]==0) {
								$nombre= __('Unkwon user','Imaxel');
							} elseif($hasOrder) {
								$nombre = $projectOrder["client_id"];
							}
							if (strlen(''.$nombre.'') > 25){
								echo substr(''.$nombre.'', 0, 25) . '...';
							}else{
								echo ''.$nombre.''; 
							}
							if($project["client_id"]!=0){
								 echo '</a>';
							}

							echo '</td>';
							
							//Product name
							echo '<td>'; 
							if (strlen($project["product"]) > 25){
								echo substr($project["product"], 0, 25) . '...';
							}else{
								echo $project["product"]; 
							}
							echo '</td>';
							echo '<td>'; 
							
							//Price
							if($hasOrder && $projectOrder["line_total"]!=0){ 
								echo round($projectOrder["line_total"],2); 
								echo get_woocommerce_currency_symbol();
							}else{
								echo $project["price"];
								if($project["price"]!=""){ echo get_woocommerce_currency_symbol(); }
							}
							
							//Status in Woo
							echo '</td>';
							
							echo '<td>'.($hasOrder ? $projectOrder["status_WC"] : '').'</td>';
							
							echo '<td>'; 
							//Status in Imaxel
							if($project["status"]=="created"){
								echo '<span style="color: orange;" class="smsimaxel'.$project["project"].'">'.__('Started','Imaxel').'</span>';
							}elseif($project["status"]=="ready"){
								echo '<span style="color: green;" class="smsimaxel'.$project["project"].'">'.__('Ready','Imaxel').'</span>';
							}elseif($project["status"]=="produced"){
								echo '<span style="color: blue;" class="smsimaxel'.$project["project"].'">'.__('Ordered','Imaxel').'</span>';
							}elseif($project["status"]=="cancelled"){
								echo '<span style="color: red;" class="smsimaxel'.$project["project"].'">'.__('Cancelled','Imaxel').'</span>';
							}else {
							}
							echo '</td>';

							echo '<td>';
							
							//Print the action button - AJAX!
							if($hasOrder && $projectOrder["order_id"]!=""){
								imaxel_iweb_my_custom_checkout_field_display_admin_order_meta($order_data["".$project["project"]."_WC"],$project["project"],$project["status"],$project["product_id"],$project["variation_id"],$projectOrder["status_WC"]);
							}else{
								imaxel_iweb_my_custom_checkout_field_display_admin_order_meta('',$project["project"],$project["status"],$project["product_id"],$project["variation_id"],'');
							}

							echo '</td>
					</tr>';
			}
			echo '</table>';
			
			//Close table
			
			//Print the pagination on botton
			echo '<div class="imaxel_iweb_pagination">';
			echo paginate_links( array(
				'base' => add_query_arg( 'cpage', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => ceil($total / $items_per_page),
				'current' => $page
			));
			echo '</div>';
		   
		}else{
			//No Woo, no cry
			echo __('Sorry, it\'s seem that you may active first the woocommerce plugin.','Imaxel');
			
		}
}

# ========================= #
# ==== GET PLUGIN PATH ==== #
# ========================= #


function imaxel_myplugin_plugin_path_imaxel() {
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

function imaxel_myplugin_plugin_url_imaxel() {
  return untrailingslashit( plugin_dir_url( __FILE__ ) );
}

function imaxel_iweb_myplugin_plugin_path_imaxel() {
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

function imaxel_iweb_myplugin_plugin_url_imaxel() {
  return untrailingslashit( plugin_dir_url( __FILE__ ) );
}

# =========================== #
# ==== LANGUAGE FUNCTION ==== #
# =========================== #


function language_imaxel() {
 $plugin_dir = basename(dirname(__FILE__)).'/language/';
 load_plugin_textdomain( 'Imaxel', false, $plugin_dir );
}
add_action('plugins_loaded', 'language_imaxel');


# ========================= #
# ==== HTTP POST CURL  ==== #
# ========================= #


function imaxel_httpPost($url,$params)
{
	error_log( print_r( '**********imaxel imaxel_httpPost ENTRA', true ) );
  $postData = '';

   foreach($params as $k => $v) 
   { 
      $postData .= $k . '='.$v.'&'; 
   }
   rtrim($postData, '&');
    $timeout=5;
    $ch = curl_init();  
 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch,CURLOPT_AUTOREFERER, true);
    curl_setopt($ch,CURLOPT_POST, count($postData));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);  
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);  
 
    $output=curl_exec($ch);
 
    curl_close($ch);
    return $output;
 
}

function imaxel_iweb_httpPost($url,$params)
{
	error_log( print_r( '**********imaxel imaxel_iweb_httpPost ENTRA', true ) );
  $postData = '{';

   foreach($params as $k => $v) 
   { 
      $postData .='"'.$k.'":"'.$v.'",'; 
   }
    $postData=substr($postData,0,-1);
    $postData .='}';
    $timeout=10;
    $ch = curl_init();  
	//echo '</br>imaxel_iweb_httpPost URL '.$url.'';
	//echo '</br>imaxel_iweb_httpPost DATA '.$postData.'';
	
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch,CURLOPT_AUTOREFERER, true);
	curl_setopt($ch,CURLOPT_POST, true);
   // curl_setopt($ch,CURLOPT_POSTFIELDSIZE, count($postData));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);  
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout); 
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	
	curl_setopt($ch,CURLOPT_TIMEOUT, $timeout); 
	//curl_setopt($ch,CURLOPT_ENCODING, ""); 
	//curl_setopt($ch,CURLOPT_USERAGENT, "wordpress api"); 
	//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));  	
 
    $output=curl_exec($ch);
	$info = curl_getinfo($ch);
	$errors = curl_error($ch);
    //echo '<br>Se tardó ' .$info['total_time'] . ' segundos en enviar la petición a ' .$info['url'].'<br> StatusCode '.$info['http_code'].' ContentType '.$info['content_type'].'';
	//echo '<br> Error '.$errors .'';
	//echo '<br>'.$output.'';
    //curl_close($ch);
	
	
	//curl -H "Content-Type: application/json" -X POST -d "{'productCode':'71021','policy':'ewoJICAicHJvZHVjdENvZGUiOiAiNzEwMjEiLAoJICAicHVibGljS2V5IjogIjA0NGYxYThlNjhmMmI1ZGU2NTViMTM1OTQ2MmQ1MTEzIiwKCSAgImV4cGlyYXRpb25EYXRlIjogIjIwMTYtMDMtMjNUMTM6MzU6MjMrMDA6MDAiCgl9','signedPolicy':'4%2FbYO7S%2FbuCA3VryetND3ysM4h49YcRVVHTxLkTv3PI%3D'}" "http://ips.0002.imaxel.com/WebCounterApiRest/api/projects"
	//echo '<br>'.$output.'';
    return $output;
 
}
# ================================== #
# ==== HTTP POST CURL FOR ORDER ==== #
# ================================== #


function imaxel_httpPostOrder($url, $params)
{
	error_log( print_r( '**********imaxel imaxel_httpPostOrder ENTRA', true ) );
	error_log( print_r( '**********url=', true ) );
	error_log( print_r( $url, true ) );
	error_log( print_r( '**********params=', true ) );
	error_log( print_r( $params, true ) );
  $postData = '';
    $postData = $params;
    
    $ch = curl_init();  
 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));   
 
    $output=curl_exec($ch);
 
    curl_close($ch);
    return $output;
 
}

function imaxel_iweb_httpPostOrder($url,$params)
{
  $postData = '';
    $postData = $params;
    
    $ch = curl_init();  
		error_log( print_r( '**********imaxel_iweb imaxel_iweb_httpPostOrder URL'.$url.'', true ) );
		error_log( print_r( '**********imaxel_iweb imaxel_iweb_httpPostOrder Params'.$params.'', true ) );
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));   
	//error_log( print_r( '**********imaxel_iweb imaxel_iweb_httpPostOrder', true ) );
    $output=curl_exec($ch);
	error_log( print_r( '**********imaxel_iweb imaxel_iweb_httpPostOrder OUTPUT'.$output.'', true ) );
 
    curl_close($ch);
    return $output;
 
}

# =================================== #
# ==== GENERIC HELPER FUNCTIONS ===== #
# =================================== #

/*
	If first and last character of the string are '"' or "'", then a new string is returned without them.$_COOKIE
	Otherwise, the original one is returned
*/
function cleanup_string_quotes($str){
	return (substr($str,0,1)=='"' && substr($str, strlen($str)-1,1)=='"' ) ? 
		substr($str,1,strlen($str)-2) : 
		(substr($str,0,1)=="'" && substr($str, strlen($str)-1,1)=="'" ) ?
			substr($str,1,strlen($str)-2) : 
			$str;
}

function filter_woocommerce_email_order_item_quantity( $item_qty, $item ) 
{ 

	//error_log(print_R($item,TRUE));

	
	$myId=$item['product_id'];
    $isIweb=get_post_meta($myId , '_iweb');
	if(empty($isIweb)==false)
	{
		global $wpdb;
		$idProject=$item['proyecto'];
		//error_log(' idproject '.$idProject.'');
		//es tipo Iweb
		if( $isIweb[0]=="1")
		{
			
			$worktypeIweb= get_post_meta( $myId, '_iwebWorkType');
			if(empty($worktypeIweb)==false&&$worktypeIweb[0]==1)
			{
				
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_iweb_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				//error_log(print_R($project_array,TRUE));
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str= cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectIweb=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', $projectIweb->design->printsRequested,$cart_item_key );
						//return ' <strong class="product-quantity">' . sprintf( '&times; %d', $projectIweb->design->printsRequested ) . '</strong>';
						return $projectIweb->design->printsRequested ;
				}
			
			}
			
		}
		//Es tipo HTML5
		else if($isIweb[0]=="-1")
		{
		    
			$moduleHTML5=get_post_meta( $myId, '_html5WorkType');
			//error_log(print_R($moduleHTML5,TRUE));
			if( empty($moduleHTML5)==false&&empty($moduleHTML5[0])==false&&$moduleHTML5[0]=='printspack')
			{
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str= cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectHTML5=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						
						return count($projectHTML5->design->pages);
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', count($projectHTML5->design->pages),$cart_item_key );
				}
				
			}
			
		}
	}
	
    return $item_qty; 
}

add_filter( 'woocommerce_email_order_item_quantity', 'filter_woocommerce_email_order_item_quantity', 10, 2 ); 


//used
function extra_info_order_admin( $order ){ 
 ?>
	<?php
		$dealerOrderNumber=get_post_meta( $order->id, '_iwebDealerOrderNumber', true );
		if(empty($dealerOrderNumber)==true)
			return;
	?>
    <div class="order_data_column">
        <h4><?php _e( 'Iweb' ); ?></h4>
        <?php 
            echo '<p><strong>' . __( 'DealerOrderNumber' ) . ' : </strong>' . $dealerOrderNumber . '</p>';
            ?>
    </div>
<?php }
add_action( 'woocommerce_admin_order_data_after_order_details', 'extra_info_order_admin' );


function extra_info_item_order( $item_id, $item, $order, $plain_text)
{ 
	
   /*         echo 'hola';
			$plain_text=$plain_text."hola";
			console.log(extra_info_item_order);
     error_log('extra_info_item_order');*/
 }
//add_action('woocommerce_order_item_meta_start','extra_info_item_order');


function filter_woocommerce_order_item_quantity_html( $strong_class_product_quantity_sprintf_times_s_esc_html_item_qty_strong, $item )
 { 
    // make filter magic happen here... 
	//error_log('filter_woocommerce_order_item_quantity_html');
    //return "20";//$strong_class_product_quantity_sprintf_times_s_esc_html_item_qty_strong; 
   // error_log(print_R($item,TRUE));
	$myId=$item['product_id'];
   $isIweb=get_post_meta($myId , '_iweb');
	if(empty($isIweb)==false)
	{
		global $wpdb;
		$idProject=$item['proyecto'];
		//error_log(' idproject '.$idProject.'');
		//es tipo Iweb
		if( $isIweb[0]=="1")
		{
			
			$worktypeIweb= get_post_meta( $myId, '_iwebWorkType');
			if(empty($worktypeIweb)==false&&$worktypeIweb[0]==1)
			{
				
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_iweb_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				//error_log(print_R($project_array,TRUE));
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str=cleanup_string_quotes($project_array[0]['data_project']);
						//error_log(''.$str.'');
						$projectIweb=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', $projectIweb->design->printsRequested,$cart_item_key );
						return ' <strong class="product-quantity">' . sprintf( '&times; %d', $projectIweb->design->printsRequested ) . '</strong>';
				}
				//se tiene que obtner los datos del proyect
				
				//error_log('cart_quantityfilter IWeb print');
				//return sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
		//Es tipo HTML5
		else if($isIweb[0]=="-1")
		{
		    
			$moduleHTML5=get_post_meta( $myId, '_html5WorkType');
			//error_log(print_R($moduleHTML5,TRUE));
			if( empty($moduleHTML5)==false&&empty($moduleHTML5[0])==false&&$moduleHTML5[0]=='printspack')
			{
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str=cleanup_string_quotes($project_array[0]['data_project']);
						//error_log(''.$str.'');
						$projectHTML5=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						
						return ' <strong class="product-quantity">' . sprintf( '&times; %d', count($projectHTML5->design->pages) ) . '</strong>';
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', count($projectHTML5->design->pages),$cart_item_key );
				}
				//error_log('cart_quantityfilter HTML5 print');
				//return sprintf( 'HTML5 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
	}
	return $strong_class_product_quantity_sprintf_times_s_esc_html_item_qty_strong;
}
function extra_item_display( $output, $this )
{

	/*error_log('extra_item_display');
	$output="dos por";*/
	/*echo 'hola que es lo que esta pasando';*/
	
}
         
// add the filter 
add_filter( 'woocommerce_order_item_quantity_html', 'filter_woocommerce_order_item_quantity_html', 10, 2 ); 
//add_filter( 'woocommerce_admin_order_item_quantity_html', 'filter_woocommerce_order_item_quantity_html', 10, 2 ); 

//informacion que aparece despues de Purchased en la pantalla de listado de pedido del admi
//add_filter( 'woocommerce_order_items_meta_display', 'extra_item_display', 10, 2 ); 
function checkout_cart_item_quantity( $strong_class_product_quantity_sprintf_times_s_cart_item_quantity_strong, $cart_item, $cart_item_key)
{
	$myId=$cart_item['product_id'];
   $isIweb=get_post_meta($myId , '_iweb');
   //error_log('cart_quantityfilter es productoid '.$myId.' IWeb '.$isIweb.'');
  // error_log(print_R($isIweb,TRUE));
   if(empty($isIweb)==false)
   {
   
		global $wpdb;
		$idProject=$cart_item['variation']['attribute_proyecto'];
		//error_log(' idproject '.$idProject.'');
		//es tipo Iweb
		if( $isIweb[0]=="1")
		{
			
			$worktypeIweb= get_post_meta( $myId, '_iwebWorkType');
			if(empty($worktypeIweb)==false&&$worktypeIweb[0]==1)
			{
				
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_iweb_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				//error_log(print_R($project_array,TRUE));
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str=cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectIweb=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', $projectIweb->design->printsRequested,$cart_item_key );
						return ' <strong class="product-quantity">' . sprintf( '&times; %d', $projectIweb->design->printsRequested ) . '</strong>';
				}
				//se tiene que obtner los datos del proyect
				
				//error_log('cart_quantityfilter IWeb print');
				//return sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
		//Es tipo HTML5
		else if($isIweb[0]=="-1")
		{
		    
			$moduleHTML5=get_post_meta( $myId, '_html5WorkType');
			//error_log(print_R($moduleHTML5,TRUE));
			if( empty($moduleHTML5)==false&&empty($moduleHTML5[0])==false&&$moduleHTML5[0]=='printspack')
			{
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str= cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectHTML5=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						
						return ' <strong class="product-quantity">' . sprintf( '&times; %d', count($projectHTML5->design->pages) ) . '</strong>';
						//return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', count($projectHTML5->design->pages),$cart_item_key );
				}
				//error_log('cart_quantityfilter HTML5 print');
				//return sprintf( 'HTML5 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
   }
   return $strong_class_product_quantity_sprintf_times_s_cart_item_quantity_strong;
}
function cart_quantityfilter($product_quantity, $cart_item_key, $cart_item)
{
	//error_log('cart_quantityfilter ');
	//error_log(print_R($cart_item,TRUE));
	// es el texto por defecto
	//falta saber que vamos a realizar
	//$product_quantity
	//identificardor del producto y del post
   $myId=$cart_item['product_id'];
   $isIweb=get_post_meta($myId , '_iweb');
   //error_log('cart_quantityfilter es productoid '.$myId.' IWeb '.$isIweb.'');
  // error_log(print_R($isIweb,TRUE));
   if(empty($isIweb)==false)
   {
   
		global $wpdb;
		$idProject=$cart_item['variation']['attribute_proyecto'];
		//error_log(' idproject '.$idProject.'');
		//es tipo Iweb
		if( $isIweb[0]=="1")
		{
			
			$worktypeIweb= get_post_meta( $myId, '_iwebWorkType');
			if(empty($worktypeIweb)==false&&$worktypeIweb[0]==1)
			{
				
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_iweb_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				//error_log(print_R($project_array,TRUE));
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str=cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectIweb=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', $projectIweb->design->printsRequested,$cart_item_key );
				}
				//se tiene que obtner los datos del proyect
				
				//error_log('cart_quantityfilter IWeb print');
				return sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
		//Es tipo HTML5
		else if($isIweb[0]=="-1")
		{
		    
			$moduleHTML5=get_post_meta( $myId, '_html5WorkType');
			//error_log(print_R($moduleHTML5,TRUE));
			if( empty($moduleHTML5)==false&&empty($moduleHTML5[0])==false&&$moduleHTML5[0]=='printspack')
			{
				$query = "SELECT * FROM ".$wpdb->prefix."imaxel_projects WHERE project=".$idProject."";
				$project_array = $wpdb->get_results($query);
				$project_array=imaxel_objectToArray($project_array); 
				if(empty($project_array)==false&&empty($project_array[0])==false&&empty($project_array[0]['data_project'])==false)
				{
						
						$str=cleanup_string_quotes( $project_array[0]['data_project'] );
						//error_log(''.$str.'');
						$projectHTML5=json_decode($str);
						//error_log(print_R($projectIweb,TRUE));
						return sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="1" />', count($projectHTML5->design->pages),$cart_item_key );
				}
				//error_log('cart_quantityfilter HTML5 print');
				//return sprintf( 'HTML5 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			}
			
		}
   }
   return $product_quantity;
   
	//$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
	///echo 'hola';
}

add_filter( 'woocommerce_checkout_cart_item_quantity', 'checkout_cart_item_quantity', 10, 3 ); 

add_filter( 'woocommerce_order_items_meta_display', 'extra_item_display', 10, 2 ); 

add_filter( 'woocommerce_cart_item_quantity', 'cart_quantityfilter', 10, 3 ); 
//echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );

# =========================== #
# ==== INCLUDE FUNCTIONS ==== #
# =========================== #

include (plugin_dir_path( __FILE__ ) . '/includes/enqueue_files.php'); 
include (plugin_dir_path( __FILE__ ) . '/includes/functions_helper.php'); 
include (plugin_dir_path( __FILE__ ) . '/includes/imaxel_helpers.php'); 
include (plugin_dir_path( __FILE__ ) . '/includes/imaxel_api.php'); 
include (plugin_dir_path( __FILE__ ) . '/includes/imaxel_iweb_helpers.php'); 
include (plugin_dir_path( __FILE__ ) . '/includes/imaxel_iweb_api.php');

# ================================== #
# ==== DELETE OPTIONS AND TABLE ==== #
# ================================== #

//if uninstall not called from WordPress exit
/*if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

//Delete options
delete_option( 'public_key' );
delete_option( 'private_key' );
delete_option( 'url_base' );
delete_option( 'productos_data' );
delete_option( 'fecha' );
delete_option( 'cart_page' );
delete_option( 'automatic_send' );
delete_option( 'wc_status_automatic_send' );
delete_option( 'imaxeldb_db_version' );

//Delete table
global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS '.$wpdb->prefix.'imaxel_projects' );*/

?>