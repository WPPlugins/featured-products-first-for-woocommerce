<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetured Products Widget.
 *
 * @author   pmbaldha
 * @category Widgets
 * @package  featured-products-first-for-woocommerce/Widgets
 * @version  0.1
 * @extends  WC_Widget
 */
class WFF_Widget_Featured_Product extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce woocommerce_fetured_products';
		$this->widget_description = __( 'Display a list of fetaured products.', 'featured-products-first-for-woocommerce' );
		$this->widget_id          = 'woocommerce_fetured_products';
		$this->widget_name        = __( 'WooCommerce Fetured Product', 'featured-products-first-for-woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Featured Products', 'featured-products-first-for-woocommerce' ),
				'label' => __( 'Title', 'featured-products-first-for-woocommerce' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'featured-products-first-for-woocommerce' )
			)
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		if( version_compare(WC()->version, 3.0) >= 0 )
		{
			$query_args = array( 
				'posts_per_page' => $number, 
				'no_found_rows' => 1, 
				'post_status' => 'publish', 
				'post_type' => 'product',  
				'post__in' => wc_get_featured_product_ids(),
			);
		}
		else
		{
			$query_args = array( 
				'posts_per_page' => $number, 
				'no_found_rows' => 1, 
				'post_status' => 'publish', 
				'post_type' => 'product',  
				'meta_key' => '_featured',  
				'meta_value' => 'yes', 
				'meta_compare' => '=' 
			);
		}
		$query_args['meta_query'] = WC()->query->get_meta_query();

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			$this->widget_start( $args, $instance );

			echo '<ul class="product_list_widget">';

			while ( $r->have_posts() ) {
				$r->the_post();
				wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) );
			}

			echo '</ul>';

			$this->widget_end( $args );
		}
		
		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}

/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function wff_register_widgets() {
	register_widget( 'WFF_Widget_Featured_Product' );	
}
add_action( 'widgets_init', 'wff_register_widgets' );
