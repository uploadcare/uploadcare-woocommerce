<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-wdm-upload-image-meta
 *
 * @author teni
 */
if ( ! class_exists( 'Wdm_Upload_Image_Meta' ) ) {

	class Wdm_Upload_Image_Meta {

		function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'wdm_add_woo_product_metabox' ) );
			add_action( 'save_post', array( $this, 'wdm_add_upload_image_meta_save' ) );
		}

		/**
		 *  create metabox on create/edit product
		 *
		 * @access public
		 * @return 	void
		 */
		function wdm_add_woo_product_metabox() {
			//add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box( 'wdm_upload_images', 'Upload Image Settings', 'wdm_upload_images_display_callback', 'product', 'side', 'high' );
		}

		/**
		 *  Saves the custom meta input
		 *
		 * @access public
		 * @param  int $post_id 
		 * @return 	void
		 */
		function wdm_add_upload_image_meta_save( $post_id ) {

			// Checks save status - overcome autosave, etc.
			$is_valid_nonce = ( isset( $_POST[ 'wdm_upload_image_nonce' ] ) && wp_verify_nonce( $_POST[ 'wdm_upload_image_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

			// Exits script depending on save status
			if ( ! $is_valid_nonce ) {
				return;
			}

			// Checks for input and saves - save checked as yes and unchecked at no
			if ( isset( $_POST[ 'upload-image-checkbox' ] ) ) {
				update_post_meta( $post_id, 'upload-image-checkbox', 'yes' );
			} else {
				update_post_meta( $post_id, 'upload-image-checkbox', 'no' );
			}
		}

	}

	/**
	 *  Display checkbox to enable/disable upload image
	 *
	 * @access public
	 * @param array $post 
	 * @return 	void
	 */
	function wdm_upload_images_display_callback( $post ) {

		wp_nonce_field( basename( __FILE__ ), 'wdm_upload_image_nonce' );
		$upload_img_option = get_post_meta( $post->ID, 'upload-image-checkbox', 'true' );
		?>


		<span class="wdm-upload-row-title">
		<?php echo ("Check it for image upload functionality on Single product page:"); ?>
		</span>
		<div class="wdm-upload-row-content">
			<label for="profile-image-checkbox">

				<input type="checkbox" name="upload-image-checkbox" id="upload-image-checkbox" value="yes" <?php if ( isset( $upload_img_option ) ) checked( $upload_img_option, 'yes' ); ?> />
		<?php echo "Upload Image"; ?>

			</label>

		</div>


		<?php
	}

}
        
 
