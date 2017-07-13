// JavaScript Document
jQuery( document ).ready(function($) {
	if( !$('#wff_woocommerce_featured_first_enabled').is(":checked") ) {
    	$( '#wff_woocommerce_featured_first_enabled_on_shop' ).parents('tr').hide();
	}
	$('#wff_woocommerce_featured_first_enabled').change( function(){
		if( !$(this).is(":checked") ) {
			$( '#wff_woocommerce_featured_first_enabled_on_shop' ).parents('tr').fadeOut( 'slow' );
		}
		else {
			$( '#wff_woocommerce_featured_first_enabled_on_shop' ).parents('tr').fadeIn( 'slow' );	
		}
		
	});
});
