<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-wdm-widget-menu
 *
 * @author teni
 */
if ( ! class_exists( 'Wdm_Widget_Menu' ) ) {

	class Wdm_Widget_Menu {

		/**
		 *
		 * @var array stores the source tabs
		 */
		var $tabs = array();

		/**
		 *
		 * @var array stores the source tabs which are by default 
		 */
		var $tab_defaults = array();

		function __construct() {
			add_action( 'admin_menu', array( $this, 'wdm_add_widget_menu' ) );
		}

		/**
		 * Add the settings page
		 * 
		 * @access public
		 * @return 	void
		 */
		function wdm_add_widget_menu() {
			//Add admin settings page for the plugin.
			add_options_page( 'WooCommerce Uploadcare settings', 'WooCommerce Uploadcare ', 'manage_options', 'wdm_woo_uploadImage', 'wdm_add_widget_menu_callback' );
		}

	}

	/**
	 * Add the settings page with various fields
	 * 
	 * @access public
	 * @return 	void
	 */
	function wdm_add_widget_menu_callback() {


		$saved = false;
		if ( isset( $_POST[ 'uploadcare_hidden' ] ) && $_POST[ 'uploadcare_hidden' ] == 'Y' ) {
			if ( isset( $_POST[ 'uploadcare_public' ] ) ) {
				$uploadcare_public = $_POST[ 'uploadcare_public' ];
				update_option( 'uploadcare_public', $uploadcare_public );
			} else {
				$uploadcare_public = "";
			}
			$uploadcare_secret = $_POST[ 'uploadcare_secret' ];
			update_option( 'uploadcare_secret', $uploadcare_secret );

			$uploadcare_locale = $_POST[ 'uploadcare_locale' ];
			update_option( 'uploadcare_locale', $uploadcare_locale );

			$uploadcare_js = stripslashes( $_POST[ 'uploadcare_js' ] );
			//$uploadcare_js=  array('wdm_js'=>$uploadcare_js);
			update_option( 'uploadcare_js', $uploadcare_js );

			$saved = true;
		} else {
			$uploadcare_public	 = get_option( 'uploadcare_public' );
			$uploadcare_secret	 = get_option( 'uploadcare_secret' );
			$uploadcare_locale	 = get_option( 'uploadcare_locale' );
			$uploadcare_js		 = stripslashes( get_option( 'uploadcare_js', true ) );

		}
		?>

		<?php if ( $saved ): ?>
			<div class="updated"><p><strong><?php _e( 'Options saved.' ); ?></strong></p></div>
		<?php endif; ?>



		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
		<?php echo "<h2>" . __( 'Uploadcare', 'uploadcare_settings' ) . "</h2>"; ?>
			<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER[ 'REQUEST_URI' ] ); ?>">
				<input type="hidden" name="uploadcare_hidden" value="Y">
				<h3>API Keys <a href="https://uploadcare.com/documentation/keys/">[?]</a></h3>
				<p>
		<?php _e( 'Public key: ' ); ?>
					<input type="text" name="uploadcare_public" value="<?php echo $uploadcare_public; ?>" size="20">
		<?php _e( 'ex: demopublickey' ); ?>
				</p>
				<p>
		<?php _e( "Secret key: " ); ?>
					<input type="text" name="uploadcare_secret" value="<?php echo $uploadcare_secret; ?>" size="20">
		<?php _e( 'ex: demoprivatekey' ); ?>
				</p>
				<p>
		<?php _e( "Uploadcare Locale: " ); ?>
					<input type="text" name="uploadcare_locale" value="<?php echo $uploadcare_locale; ?>" size="20">
					You can get your Locale name <a href="http://www.lingoes.net/en/translator/langcode.htm">here</a>
				</p>

				<h3>Enter your JS code </h3>
				<textarea rows="5" cols="50" name="uploadcare_js"><?php echo $uploadcare_js; ?></textarea>

				<p class="submit">
					<?php submit_button(); ?>
				</p>
			</form>
			<div>
				<ul>
					<li>Files uploaded to demo account (demopublickey) are deleted after some time.</li>
					<li>You can get your own account <a href="https://uploadcare.com/pricing/">here</a>.</li>
				</ul>
			</div>
		</div>
		<?php
	}

}
