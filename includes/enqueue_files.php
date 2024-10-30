<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly	


# ============= #
# ==== CSS ==== #
# ============= #
 

function imaxel_load_css_styles() {

	wp_enqueue_style( 'style_imaxel', imaxel_myplugin_plugin_url_imaxel().'/assets/css/style.css' );
}

add_action( 'admin_head', 'imaxel_load_css_styles' );

function imaxel_iweb_load_css_styles() {

	wp_enqueue_style( 'style_imaxel', imaxel_iweb_myplugin_plugin_url_imaxel().'/assets/css/imaxel_iweb_style.css' );
}

add_action( 'admin_head', 'imaxel_iweb_load_css_styles' );


# ==================== #
# ==== JAVASCRIPT ==== #
# ==================== #

function imaxel_load_js_file_v2() {
   wp_register_script( "my_editor", imaxel_myplugin_plugin_url_imaxel().'/assets/js/imaxel.js', array('jquery') );
   wp_localize_script( 'my_editor', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'my_editor' );

}

add_action( 'init', 'imaxel_load_js_file_v2' );

function imaxel_iweb_load_js_file() {
   wp_register_script( "my_editor2", imaxel_iweb_myplugin_plugin_url_imaxel().'/assets/js/imaxel_iweb.js', array('jquery') );
   wp_localize_script( 'my_editor2', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'my_editor2' );

}

add_action( 'init', 'imaxel_iweb_load_js_file' );


# ============================== #
# ==== OLD JS LOAD NOT USED ==== #
# ============================== #


function imaxel_load_js_file()
{
	wp_enqueue_script('imaxel_js', imaxel_myplugin_plugin_url_imaxel().'/assets/js/imaxel.js');
}

//add_action('wp_head', 'imaxel_load_js_file');
?>