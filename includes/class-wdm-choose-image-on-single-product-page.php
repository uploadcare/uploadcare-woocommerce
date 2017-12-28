<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-wdm-choose-image-on-single-product-page
 *
 * @author teni
 */
if ( ! class_exists( 'Wdm_Choose_Image_On_Single_Product_Page' ) ) {

	class Wdm_Choose_Image_On_Single_Product_Page {

		var $upload_img_option = null;

		function __construct() {
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'wdm_add_choose_image_button' ), 1 );

			// Add item data to the cart 
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'wdm_add_cart_item_data' ), 10, 2 );

			// Load cart data per page load
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'wdm_get_cart_items_from_session' ), 1, 3 );

			// Add meta to order 
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'wdm_add_values_to_item_meta' ), 1, 2 );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'wdm_remove_image_from_cart' ), 1, 1 );
		}

		/**
		 *  Render choose image button if upload image enabled for product
		 *
		 * @access public
		 * @return 	void
		 */
		function wdm_add_choose_image_button() {

			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$post_id = get_the_ID();

			if ( get_post_meta( $post_id, "upload-image-checkbox", true ) )
				$upload_img_option = get_post_meta( $post_id, "upload-image-checkbox", true );
			if ( isset( $upload_img_option ) && $upload_img_option == "yes" ) {

				$uploadcare_public = get_option( 'uploadcare_public' );

				$uploadcare_js = stripslashes( get_option( 'uploadcare_js', true ) );

				if(!isset($_SESSION)) { 
					session_start();
				}

				$_SESSION[ 'raw_js' ] = $uploadcare_js;

				if ( isset( $uploadcare_public ) ) {
					$font_size			 = 24;
					$num_images			 = 6;
					$num_cols			 = 4;
					$image_size_value	 = 18;


					$doc_ico = plugins_url( '../icons/document-icon.png', __FILE__ );

					$data = array(
						'ajax_url'			 => admin_url( 'admin-ajax.php' ),
						'plugin_path'		 => plugins_url( '/', __FILE__ ),
						'post_id'			 => get_the_ID(),
						'image_size'		 => $image_size_value,
						'image_cols'		 => $num_cols,
						"invalid_image_file" => __( 'Invalid Image File. Please Upload Another Image.' ),
						"no_image_selected"	 => __( "No Image Selected" ),
						"rem_img"			 => __( "Remove Image" ),
						"wait_for_save"		 => __( "Please wait. The image is being saved." ),
						"remove_image"		 => __( "Please confirm that you want to remove this image." ),
						"max_selected"		 => __( "Maximum Images Selected" ),
						"dup_img"			 => __( "Duplicate Image" ),
						"uploadcare_key"	 => $uploadcare_public,
						"uploadcare_locale"	 => $uploadcare_locale,
						"image_no"			 => $num_images,
						"doc_icon"			 => $doc_ico
					);

					wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );


					wp_register_script( 'wdm-uploadcare-js', 'https://ucarecdn.com/widget/' . WOOCOMMERCE_UPLOADCARE_WIDGET_VERSION . '/uploadcare/uploadcare.full.min.js', array( 'jquery' ) );
					wp_enqueue_script( 'wdm-uploadcare-js' );

					wp_register_script( 'upload-image-js', plugins_url( '/js/uploadImage_Script.js', dirname( __FILE__ ) ), array( 'jquery', 'wdm-uploadcare-js' ) );
					wp_enqueue_script( 'upload-image-js' );
					wp_localize_script( 'upload-image-js', 'ajax_object', $data );


					wp_register_script( 'wdm-raw-js', plugins_url( '/includes/raw_js.php', dirname( __FILE__ ) ), array( 'jquery', 'wdm-uploadcare-js', 'upload-image-js' ), '1.0.0', false );
					wp_enqueue_script( 'wdm-raw-js' );



					// Style for the Plugin area.
					wp_register_style( 'wdm-design-style', plugins_url( '/css/uploadImage_Style.css', dirname( __FILE__ ) ) );
					wp_enqueue_style( 'wdm-design-style' );
					?>
					<br>
					<div class="wdm-insta-woo">

						<input type="hidden" id = "uploader" name="qs-file" role="uploadcare-uploader" data-multiple />
						<h4 id='wdm_uploaded' style="display: none"> <?php _e( "Uploaded Files " ); ?> </h4>
						<div id="list" class= 'wdm_img_drag_grid' ></div>
					</div>
					<p>

					<?php
				}
			}
		}

		/**
		 * Add Uploaded image ID from session to cart item meta of Product.(Add item data to the cart.)
		 * 
		 * @access public
		 * @param array $cart_item_data
		 * @param int $product_id 
		 * @return 	$cart_item_data
		 */
		function wdm_add_cart_item_data( $cart_item_data, $product_id ) {

			global $woocommerce;
			$option1 = null;
			session_start();

  
			if ( isset( $_SESSION[ 'wdm_cdn_urls' ] ) ) {
				$option1 = $_SESSION[ 'wdm_cdn_urls' ];
				unset( $_SESSION[ 'wdm_cdn_urls' ] );
			}
			if ( isset( $_SESSION[ 'wdm_extensions' ] ) ) {
				$option2 = $_SESSION[ 'wdm_extensions' ];
				unset( $_SESSION[ 'wdm_extensions' ] );
			}

			if ( ! empty( $option1 ) && ! empty( $option2 ) ) {
				$new_value = array( 'wdm_cdn_urls' => $option1, 'wdm_extensions' => $option2 );
				if ( empty( $cart_item_data ) )
					return $new_value;
				else
					return array_merge( $cart_item_data, $new_value );
			}        
		}

		/**
		 * Get image ID from session data and put it in $item in cart.(Load cart data per page load)  
		 * 
		 * @access public
		 * @param mixed $item
		 * @param mixed $values 
		 * @return  $cart_item_data
		 */
		function wdm_get_cart_items_from_session( $item, $values, $key ) {
			if ( array_key_exists( 'wdm_cdn_urls', $values ) && array_key_exists( 'wdm_extensions', $values ) ) {
				$item[ 'wdm_cdn_urls' ]	 = $values[ 'wdm_cdn_urls' ];
				$item[ 'wdm_extensions' ]	 = $values[ 'wdm_extensions' ];
			}
			return $item;
		}

		/**
		 * Remove image, if product is removed from cart
		 * 
		 * @access public
		 * @param mixed $cart_item_key
		 * @return void
		 */
		function wdm_remove_image_from_cart( $cart_item_key ) {
			global $woocommerce;

			// Get cart
			$cart = $woocommerce->cart->get_cart();

			// For each item in cart, if item is upsell of deleted product, delete it
			foreach ( $cart as $key => $values ) {
				if ( $values[ 'wdm_cdn_urls' ] == $cart_item_key )
					unset( $woocommerce->cart->cart_contents[ $key ] );
				if ( $values[ 'wdm_extensions' ] == $cart_item_key )
					unset( $woocommerce->cart->cart_contents[ $key ] );
			}
		}

		/**
		 * Code to add cart item metadata about order to line item.
		 * The metadata contains - <img> tag containing the URL of the image it also links to the image in the admin dashboard.    
		 * 
		 * @access public
		 * @param mixed $item_id
		 * @param mixed $values
		 * @return void
		 */
		function wdm_add_values_to_item_meta( $item_id, $values ) {

			if ( $values[ 'wdm_cdn_urls' ] && $values[ 'wdm_extensions' ] ) {

				$oth_img_id	 = $values[ 'wdm_cdn_urls' ];
				$fexts		 = $values[ 'wdm_extensions' ];

				$img_links = "";

				$images	 = $oth_img_id;
				$doc_ico = plugins_url( '../icons/document-icon.png', __FILE__ );

				foreach ( $images as $key => $img ) {
					$fext = $fexts[ $key ];

					if ( in_array( $fext, array( 'png', 'jpg', 'bmp', 'gif', 'jpeg' ) ) )
						$img_links .= "<a href='" . $img . "' target='_blank'><img src='$img' class='wdm-collage-images' height='100' width='100' style='padding:5px;'/></a>";
					else
						$img_links .= "<a href='" . $img . "' target='_blank' class='wdm-collage-images'>   <img src='$doc_ico' height='100' width='100' style='padding:5px;background-color:#fff'/>  </a>";
				}

				woocommerce_add_order_item_meta( $item_id, 'All Files', $img_links );
			}
		}

	}

}	
