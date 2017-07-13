<?php
/**
 * Plugin Name: Featured Products First for WooCommerce 
 * Plugin URI: https://1stphp.wordpress.com/
 * Description: Enables featured products listed first On Shop Page and Search Page
 * Version: 0.5
 * Author: pmbaldha
 * Author URI: https://1stphp.wordpress.com/
 * Requires at least: 4.5
 * Tested up to: 4.8
 *
 * Text Domain: featured-products-first-for-woocommerce
 * Domain Path: /languages/
 */
 
/**
 * @package WooCommerce Featured First
 * @category Core
 * @author pmbaldha
 */

// Make sure we don't expose any info if called directly 
if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}

define( 'WFF_VERSION' , '1.0' );

if( !function_exists( 'wff_after_woocommerce_loded' ) ) {
	add_action( 'activated_plugin', 'wff_activate', 10, 2 );
	/**
	 * Function to set the default settings
	 * @since  0.1
	 */
	function wff_activate() {
		add_option('wff_woocommerce_featured_first_enabled', 'yes');
		add_option('wff_woocommerce_featured_first_enabled_on_shop', 'yes');
		add_option('wff_woocommerce_featured_first_enabled_on_search', 'yes');
		add_option('wff_woocommerce_featured_first_enabled_on_archive', 'yes');
	}
}

/**
 * Check if WooCommerce is active
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}



if( !function_exists( 'wff_after_woocommerce_loded' ) ) {
	add_action( 'woocommerce_loaded', 'wff_after_woocommerce_loded' );
	/**
	 * Function to set fetured first widget
	 * @since  0.1
	 */
	function wff_after_woocommerce_loded() {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/widgets/class-wff-widget-fetured-product.php');
	}
}



if( !function_exists( 'wff_pre_get_posts' ) ) {
	if( !is_admin() && get_option( 'wff_woocommerce_featured_first_enabled' ) == 'yes') {
		
		add_action( 'pre_get_posts', 'wff_pre_get_posts', 999 );
		/**
		 * Function to set fetured products first in product list 
		 * @since  0.1
		 */
		function wff_pre_get_posts( $query ) {	
			/*
			echo "<pre>";
			print_r($query);
			die;
			*/
			
			
			
			
			//This function is for old woocommerce
			//If woocommerce version is latest 
			//then return as it is
			if( version_compare(WC()->version, 3.0) > 0 )
				return $query;
		
			if( 
				!empty($query->query_vars[ 'wc_query' ])
				&&
				$query->query_vars[ 'wc_query' ] == 'product_query' 
			&&
			(
				( get_option( 'wff_woocommerce_featured_first_enabled_on_shop' ) == 'yes' &&  empty( $query->query_vars[ 's' ] ) )		
				||
				( get_option( 'wff_woocommerce_featured_first_enabled_on_search' ) == 'yes' &&  !empty( $query->query_vars[ 's' ] ) )
				||
				( get_option( 'wff_woocommerce_featured_first_enabled_on_archive' ) == 'yes' &&  empty( $query->query_vars[ 's' ] ) && is_tax())
			 ) 
			 &&
			 ( 
			 	!empty( $query->query_vars[ 'orderby' ] )
			 	&&
				$query->query_vars[ 'orderby' ] == 'menu_order title'
				&&
				!empty( $query->query_vars[ 'order' ] )
				&&
				$query->query_vars[ 'order' ]== 'ASC' 		
				
			  )
			  
			) {
				
				
					$query->set( 'meta_key', '_featured');
					$query->set( 'orderby' , "meta_value " . $query->get( 'orderby' ) );
					$query->set( 'order' , "DESC " . $query->get( 'order' ) );
					
								
			}
			
		
			return $query;
		}
	}
}

/**
 * For admin screen
 */
if( is_admin() ) {
	if( !function_exists( 'wff_all_settings' ) ) {
		add_filter( 'woocommerce_get_settings_products', 'wff_all_settings', 10, 2 );
		/**
		 * Function to set fetured first settings
		 * @since  0.1
		 */
		function wff_all_settings( $settings, $current_section ) {
			/**
			 * Check the current section is what we want
			 **/
			if ( $current_section == 'display' ) {
						
						
				$custom_fields = array(
					array(
						'title' => esc_html__( 'Featured Products First ' , 'featured-products-first-for-woocommerce' ),
						'desc' => esc_html__( 'Enable Featured Products Listed First On Product Listing' , 'featured-products-first-for-woocommerce' ),
						'id' => 'wff_woocommerce_featured_first_enabled',
						'default' => 'yes',
						'type' => 'checkbox',
					),
					array
					(
						'title' => esc_html__( 'Enable Featured Products First' , 'featured-products-first-for-woocommerce' ),
						'desc' => esc_html__( 'Enable Featured Products First On Shop Page' , 'featured-products-first-for-woocommerce' ),
						'id' => 'wff_woocommerce_featured_first_enabled_on_shop',
						'default' => 'yes',
						'type' => 'checkbox',
						'checkboxgroup' => 'start'
					),
					array
					(
						'desc' => esc_html__( 'Enable Featured Products First On Product Search' , 'featured-products-first-for-woocommerce' ),
						'id' => 'wff_woocommerce_featured_first_enabled_on_search',
						'default' => 'yes',
						'type' => 'checkbox',
						'checkboxgroup' => 'end'
					),
					array
					(
						'desc' => esc_html__( 'Enable Featured Products First On Archive Product Category' , 'featured-products-first-for-woocommerce' ),
						'id' => 'wff_woocommerce_featured_first_enabled_on_archive',
						'default' => 'yes',
						'type' => 'checkbox',
						'checkboxgroup' => 'end'
					),					
				);
				array_splice( $settings, 5, 0, $custom_fields); //pos 5	
				return $settings;
				
				
			
			/**
			 * If not, return the standard settings
			 **/
			} else {
				return $settings;
			}
		}
	}
	
	if( !function_exists( 'wff_admin_enqueue' ) ) {
		add_action( 'admin_enqueue_scripts', 'wff_admin_enqueue' );
		/**
		 * Function for enqueue javascript 
		 * provides easyness to admin section
		 * @since  0.1
		 */
		function wff_admin_enqueue($hook) {
				
			if ( 'woocommerce_page_wc-settings' != $hook  ) {
				return;
			}
		
			if(  !empty( $_GET['tab'] ) && $_GET['tab'] == 'products' && !empty( $_GET['section'] ) && $_GET['section'] == 'display' ) {
				wp_enqueue_script( 'ff-admin-custom', plugins_url( 'assets/js/admin-custom.js', __FILE__ ), array( 'jquery' ), WFF_VERSION  );
			}
		}
	}
}




add_filter('posts_orderby', 'wff_order_by_change',99,2 );
function wff_order_by_change( $order_by, $query )
{
	//This function is for new woocommerce
	//If woocommerce version is latest 
	//then return as it is
	if( version_compare(WC()->version, 3.0) <= 0 )
		return $order_by;
	
 
	if( 
				$query->is_main_query() && $query->is_archive &&   
				(
					(
						!empty($query->query_vars[ 'post_type' ])
						&&
						$query->query_vars[ 'post_type' ] == 'product' 
					)
				||
				is_tax(  get_object_taxonomies( 'product', 'names' ) )
				)
			&&
			(
				( get_option( 'wff_woocommerce_featured_first_enabled_on_shop' ) == 'yes' &&  empty( $query->query_vars[ 's' ] ) )		
				||
				( get_option( 'wff_woocommerce_featured_first_enabled_on_search' ) == 'yes' &&  !empty( $query->query_vars[ 's' ] ) )
				||
				( get_option( 'wff_woocommerce_featured_first_enabled_on_archive' ) == 'yes' &&  empty( $query->query_vars[ 's' ] ) && is_tax())
			 ) 
			 &&
			 ( 
			 	!empty( $query->query_vars[ 'orderby' ] )
			 	&&
				$query->query_vars[ 'orderby' ] == 'menu_order title'
				&&
				!empty( $query->query_vars[ 'order' ] )
				&&
				$query->query_vars[ 'order' ]== 'ASC' 		
				
			  )
			  
			) {	
				$feture_product_id = wc_get_featured_product_ids();
				
					
				if( is_array( $feture_product_id ) && !empty($feture_product_id)  )
					{
						
						if( empty( $order_by ) ) {
								$order_by =  "FIELD(".$GLOBALS['wpdb']->posts.".ID,'".implode("','",$feture_product_id)."') DESC ";
						}
						else
						{
							$order_by =  "FIELD(".$GLOBALS['wpdb']->posts.".ID,'".implode("','",$feture_product_id)."') DESC, " . $order_by;
						}
						
						
					
						
						
					}
			}
	return $order_by;
	
}
 
