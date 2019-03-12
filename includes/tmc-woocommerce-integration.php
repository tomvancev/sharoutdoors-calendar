<?php
/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', 'woo_calendar_product_tab' );
function woo_calendar_product_tab( $tabs ) {
  if(get_post_meta( get_the_id(), $key = 'has_calendar', $single = true )){
  	// Adds the new tab

  	$tabs['test_tab'] = array(
  		'title' 	=> 'Book now',
  		'priority' 	=> 50,
  		'callback' 	=> 'woo_calendar_product_tab_content'
  	);

  	return $tabs;
  }
}
function woo_calendar_product_tab_content() {

	// The new tab content
  calendarPageHtml();

}

/**
 * Display engraving text in the cart.
 *
 * @param array $item_data
 * @param array $cart_item
 *
 * @return array
 */
function tmc_display_dates_cart( $item_data, $cart_item ) {


	if ( empty( $cart_item['date_from'] ) ) {
		return $item_data;
	}

  $item_data[] = array(
		'key'     => 'Date From',
		'value'   => wc_clean( $cart_item['date_from'] ),
		'display' => '',
	);
  $item_data[] = array(
    'key'     => 'Date To',
    'value'   => wc_clean( $cart_item['date_to'] ),
    'display' => '',
  );

	return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'tmc_display_dates_cart', 10, 2 );
