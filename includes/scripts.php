<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Scripts
 *
 * @since 1.0
*/
function affwp_affiliate_product_rates_admin_enqueue_scripts() {
	if ( affwp_apr_is_affiliate_page() ) {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'affwp-apr-js', AFFWP_APR_PLUGIN_URL . 'assets/js/select2' . $suffix . '.js', array( 'jquery' ), AFFWP_APR_VERSION );
		wp_enqueue_style( 'affwp-apr-css', AFFWP_APR_PLUGIN_URL . 'assets/css/select2' . $suffix . '.css', '', AFFWP_APR_VERSION, 'screen' );
	}
}
add_action( 'admin_enqueue_scripts', 'affwp_affiliate_product_rates_admin_enqueue_scripts' );

/**
 * JS for admin page to allow options to be visible
 *
 * @since 1.0
*/
function affwp_affiliate_product_rates_admin_footer_js() { 
	
	if ( ! affwp_apr_is_affiliate_page() ) {
		return;
	}

	?>
	<script>
		jQuery(document).ready(function ($) {

			$('select.apr-select-multiple').select2({
			    placeholder: "Select a Product",
			    allowClear: true,
			});
			
		});
	</script>
<?php 
}
add_action( 'in_admin_footer', 'affwp_affiliate_product_rates_admin_footer_js' );


/**
 *  Determines whether the current admin page is either the edit or add affiliate admin page
 *  
 *  @since 1.0
 *  @return bool True if either edit or new affiliate admin pages
 */
function affwp_apr_is_affiliate_page() {

	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		$ret = false;
	}
	
	if ( ! ( isset( $_GET['page'] ) && 'affiliate-wp-affiliates' != $_GET['page'] ) ) {
		$ret = false;
	}

	$action  = isset( $_GET['action'] ) ? $_GET['action'] : '';

	$actions = array(
		'edit_affiliate',
		'add_affiliate'
	);
		
	$ret = in_array( $action, $actions );
	
	return $ret;
}