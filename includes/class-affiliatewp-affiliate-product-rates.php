<?php
/**
 * Core: Plugin Bootstrap
 *
 * @package     AffiliateWP Affiliate Product Rates
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Affiliate_Product_Rates' ) ) {

	/**
	 * Main plugin bootstrap.
	 *
	 * @since 1.0.0
	 */
	final class AffiliateWP_Affiliate_Product_Rates {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Affiliate_Product_Rates exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The version number of AffiliateWP
		 *
		 * @since 1.0
		 * @var string
		 */
		private $version = '1.0.5.1';

		/**
		 * Main plugin file.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $file = '';

		/**
		 * Main AffiliateWP_Affiliate_Product_Rates Instance
		 *
		 * Insures that only one instance of AffiliateWP_Affiliate_Product_Rates exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @param string $file Main plugin file.
		 * @return The one true AffiliateWP_Affiliate_Product_Rates
		 */
		public static function instance( $file = null ) {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Product_Rates ) ) {
				self::$instance = new AffiliateWP_Affiliate_Product_Rates;
				self::$instance->file = $file;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->hooks();
				self::$instance->includes();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-product-rates' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-product-rates' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'AFFWP_APR_VERSION' ) ) {
				define( 'AFFWP_APR_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'AFFWP_APR_PLUGIN_DIR' ) ) {
				define( 'AFFWP_APR_PLUGIN_DIR', plugin_dir_path( $this->file ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'AFFWP_APR_PLUGIN_URL' ) ) {
				define( 'AFFWP_APR_PLUGIN_URL', plugin_dir_url( $this->file ) );
			}

			// Plugin Root File.
			if ( ! defined( 'AFFWP_APR_PLUGIN_FILE' ) ) {
				define( 'AFFWP_APR_PLUGIN_FILE', $this->file );
			}
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			add_filter( 'affwp_calc_referral_amount', array( $this, 'calculate_referral_amount' ), 10, 5 );

			// update the product rates when the affiliate is updated.
			add_action( 'affwp_post_update_affiliate', array( $this, 'update_affiliate' ), 10, 2 );

			// add the product rates when adding a new affiliate.
			add_action( 'affwp_insert_affiliate', array( $this, 'add_affiliate_rates' ) );

			// register custom rest endpoint
			add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
			if ( is_admin() ) {
				require_once AFFWP_APR_PLUGIN_DIR . 'includes/class-admin.php';
				require_once AFFWP_APR_PLUGIN_DIR . 'includes/scripts.php';
			}
		}


		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory.
			$lang_dir = dirname( plugin_basename( AFFWP_APR_PLUGIN_DIR ) ) . '/languages/';
			$lang_dir = apply_filters( 'affwp_affiliate_product_rates_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-affiliate-product-rates' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'affiliatewp-affiliate-product-rates', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-affiliate-product-rates/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-affiliate-product-rates folder.
				load_textdomain( 'affiliatewp-affiliate-product-rates', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-affiliate-product-rates/languages/ folder.
				load_textdomain( 'affiliatewp-affiliate-product-rates', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'affiliatewp-affiliate-product-rates', false, $lang_dir );
			}
		}

		/**
		 * Add product rates for the  affiliate
		 * This is done when an affiliate is added to the DB
		 *
		 * @param int $affiliate_id Affiliate ID.
		 */
		public function add_affiliate_rates( $affiliate_id = 0 ) {

			// only add rates from admin.
			if ( is_admin() ) {
				$user_id = affwp_get_affiliate_user_id( $affiliate_id );
				$this->save_product_rates( $user_id, $_POST );
			}

		}

		/**
		 * Add custom rest endpoints
		 * To be used by select2 to search products
		 */
		public function register_api_routes() {

			register_rest_route( 'affwp/v1/product-rates', '/products', array(
				'methods' => 'GET',
				'callback' => array( $this, 'rest_get_products' ),
				'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
				}
			) );

		}

		/**
		 * Update the affiliate with their product rates
		 * Also handles sanitization
		 *
		 * @param array $data Affiliate data.
		 * @return void
		 */
		public function update_affiliate( $data ) {

			$user_id = isset( $data['user_id'] ) ? $data['user_id'] : '';

			if ( $user_id ) {
				// save our rates.
				$this->save_product_rates( $user_id, $_POST );
			}

		}


		/**
		 * Save the product rates when adding or updating an affiliate
		 *
		 * @since 1.0
		 *
		 * @param integer $user_id affiliate's WP user ID.
		 * @param array   $data    affiliate data.
		 */
		public function save_product_rates( $user_id = 0, $data = array() ) {

			// the array saved to the database.
			$saved = array();

			// get the product rates data.
			$product_rates = isset( $data['product_rates'] ) ? $data['product_rates'] : array();

			// sanitize data.
			if ( $product_rates ) {
				// loop through each rate.
				foreach ( $product_rates as $integration_key => $rates_array ) {

					foreach ( $rates_array as $key => $rate ) {

						if ( empty( $rate['products'] ) || empty( $rate['rate'] ) ) {
							// don't save incomplete rates.
							unset( $rates_array[$key] );

						} else {
							// add to saved array.
							$saved[ $integration_key ][ $key ]['products'] = $rate['products'];
							$saved[ $integration_key ][ $key ]['rate']     = sanitize_text_field( $rate['rate'] );
							$saved[ $integration_key ][ $key ]['type']     = sanitize_text_field( $rate['type'] );
						}

					}
				}

				// get existing array.
				$existing = get_user_meta( $user_id, 'affwp_product_rates', true );

				// if $saved if empty, delete it.
				if ( empty( $saved ) ) {
					delete_user_meta( $user_id, 'affwp_product_rates' );
				} else {
					// not empty, let's continue.
					// save to user meta if product data exists.
					update_user_meta( $user_id, 'affwp_product_rates', $saved );
				}

			}

		}

		/**
		 * Calculate new referral amounts based on affiliate's product rates
		 *
		 * @since  1.0
		 *
		 * @param string     $referral_amount Base amount to calculate the referral amount from.
		 * @param int        $affiliate_id    Affiliate ID.
		 * @param string     $amount          Base amount to calculate the referral amount from. Usually the Order Total.
		 * @param string|int $reference       Referral reference (usually the order ID).
		 * @param int        $product_id      Product ID. Default 0.
		 */
		public function calculate_referral_amount( $referral_amount, $affiliate_id, $amount, $reference, $product_id ) {

			// get context.
			if ( isset( $_POST['edd_action'] ) && 'purchase' == $_POST['edd_action'] ) {
				$context = 'edd';
			} elseif ( defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				$context = 'woocommerce';
			} else {
				$context = '';
			}

			if ( $context ) {
				// get the affiliate's product rates.
				$rates = $this->get_rates( $affiliate_id );
				$rates = isset( $rates[ $context ] ) ? $rates[ $context ] : '';

				if ( $rates ) {
					foreach ( $rates as $rate ) {
						// product matches.
						if ( in_array( $product_id, $rate['products'] ) ) {
							if ( 'percentage' == $rate['type'] ) {
								$referral_amount = $amount * $rate['rate'] / 100;
							} else {
								$referral_amount = $rate['rate'];
							}
						}
					}
				}
			}

			return $referral_amount;
		}

		/**
		 * Get products via Rest custom endpoint.
		 * @param  WP_REST_Request $data Arguments passed in from the rest request.
		 * @return Array List of products on the format needed by select2
		 */
		public function rest_get_products( $data ) {
			$context = $data['context'] ? $data['context'] : 'woocommerce';
			$term = $data['term'] ? $data['term'] : '';
			$page = $data['page'] ? $data['page'] : 1;

			switch ( $context ) {

				case 'edd':
					$post_type = 'download';
					break;

				case 'woocommerce':
					$post_type = 'product';
					break;
			}

			$args = array(
				'post_type' => $post_type,
				'orderby'   => 'title',
				'order'     => 'ASC',
				'posts_per_page' => 100,
				'paged' => $page
			);
			if( ! empty( $term ) ) {
				$args['s'] = $term;
			}
			$products = get_posts( $args );

			$results = array();
			if ( ! empty( $products ) ) {
				$results = array_map( function( $product ) {
					return array(
						'id' => $product->ID,
						'text' => $product->post_title
					);
				}, $products );
			}

			return array(
				'results' => $results,
				'pagination' => array(
					'more' => ! empty( $products )
				)
			);
		}

		/**
		 * Get products from list of IDs
		 * @param  String $context Context of the products to get: woocommerce, edd
		 * @param  Array 	$ids List of ids of products to search
		 * @return Array List of products that match the list of IDs
		 */
		public function get_products_from_ids( $context, $ids ) {

			if( empty( $ids ) ) {
				return array();
			}

			$post_type = 'product';

			switch ( $context ) {

				case 'edd':
					$post_type = 'download';
					break;

				case 'woocommerce':
					$post_type = 'product';
					break;
			}

			$products = get_posts( array(
				'post_type' => $post_type,
				'post__in' => $ids,
				'orderby'   => 'title',
				'order'     => 'ASC',
				'posts_per_page' => 300
			) );

			return $products;
		}

		/**
		 * Retrieve the product rates from user meta
		 *
		 * @access public
		 * @since 1.0
		 *
		 * @param int $affiliate_id Affiliate ID.
		 * @return array
		 */
		public function get_rates( $affiliate_id = 0 ) {
			$rates = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_product_rates', true );

			return $rates;
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param array  $links The current links array.
		 * @param string $file A specific plugin table entry.
		 * @return array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
			if ( plugin_basename( $this->file ) === $file ) {
					$plugins_link = array(
							'<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-affiliate-product-rates' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-affiliate-product-rates' ) . '</a>'
					);

					$links = array_merge( $links, $plugins_link );
			}

			return $links;
		}

		/**
		 * Currently supported integrations
		 *
		 * @since 1.0
		 * @return array supported integrations
		 */
		public function supported_integrations() {
			$supported_integrations = array(
				'edd',
				'woocommerce',
			);

			return $supported_integrations;
		}

	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Affiliate_Product_Rates
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_affiliate_product_rates = affiliatewp_affiliate_product_rates(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Affiliate_Product_Rates Instance
	 */
	function affiliatewp_affiliate_product_rates() {
		return AffiliateWP_Affiliate_Product_Rates::instance();
	}

}
