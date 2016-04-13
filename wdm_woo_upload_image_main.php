<?php

/*
 * Plugin Name: Uploadcare plugin for WooCommerce
 * Description: Allow WooCommerce customer to upload files for the ordered product.
 * Version: 0.0.1
 * Author: Uploadcare LLC 
 * Author URI: https://uploadcare.com/
 */

session_start();

if ( ! class_exists( 'Wdm_Upload_Image_Meta' ) ) {
	include('includes/class-wdm-upload-image-meta.php' );
	$wdm_meta_img = new Wdm_Upload_Image_Meta();
}


if ( ! class_exists( 'Wdm_Widget_Menu' ) ) {
	include('includes/class-wdm-widget-menu.php' );
	$wdm_widget_menu = new Wdm_Widget_Menu();
}

if ( ! class_exists( 'Wdm_Choose_Image_On_Single_Product_Page' ) ) {
	include('includes/class-wdm-choose-image-on-single-product-page.php' );
	$wdm_choose_img = new Wdm_Choose_Image_On_Single_Product_Page();
}

const WOOCOMMERCE_UPLOADCARE_WIDGET_VERSION = '2.8.2';

/**
 * @function  to_get_image_callback
 * @Description Register and Define AJAX Callback for creating the single grid image of all the selected image.
 * @return 	void
 */
//
add_action( 'wp_ajax_to_get_image', 'to_get_image_callback' );
add_action( 'wp_ajax_nopriv_to_get_image', 'to_get_image_callback' );

function to_get_image_callback() {

	if ( ! empty( $_POST[ 'image_position' ] ) && isset( $_POST[ 'image_position' ] ) ) {

		$pos_elems	 = explode( "|", $_POST[ 'image_position' ] );
		$fnames		 = explode( "|", $_POST[ 'fnames' ] );


		//Set the final image ID into the SESSION to be used for saving it in the WooCommerce Order meta data.
		session_start();
		$_SESSION[ 'wdm_cdn_urls' ]	 = $pos_elems;
		$_SESSION[ 'wdm_extensions' ]	 = $fnames;
		echo "true";
	} else {
		echo "false";
	}
}

