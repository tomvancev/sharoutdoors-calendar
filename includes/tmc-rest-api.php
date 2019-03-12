<?php
function register_tmc_rest_routes(){
  register_rest_route('tmc/v1', 'index', array(
        'methods' => 'GET',
        'callback' => 'get_tmc_events',
  ));
  register_rest_route('tmc/v1', 'index', array(
        'methods' => 'POST',
        'callback' => 'add_reservation_to_cart',
  ));
  register_rest_route('tmc/v1', 'index', array(
        'methods' => 'DELETE',
        'callback' => 'delete_tmc_event',
  ));
}

add_action( 'rest_api_init', 'register_tmc_rest_routes');
function get_tmc_events(){
  $no_events = 'No Events Found'; // send this to options page down the line
  $db = new CalendarDatabase();
  $date_now =  date("Y-m-d", time());
  $results = $db->read_by_date_from($date_now); // This one is ok

  if ( !empty($results) ) {
    return rest_ensure_response( json_encode($results) );
  } else {
    return rest_ensure_response(json_encode($no_events));
  }

}

function create_tmc_event($request){
  // validation
  $errors = '';

  $post_id = $request['postId'];
  if(!isset($post_id)){
    $errors .= 'Post id is not set ' . PHP_EOL;
  }

  $dateFrom = $request['dateFrom'];
  if(!isset($dateFrom)){
    $errors .= 'DateFrom is not set ' . PHP_EOL;
  }else{
    list($y, $m, $d) = explode('-', $dateFrom);
    if(!checkdate($m, $d, $y)){
      $errors .= 'DateFrom invalid format ' . PHP_EOL;
    }
  }

  $dateTo = $request['dateTo'];
  if(!isset($dateTo)){
    $errors .= 'DateTo is not set ' . PHP_EOL;
  }else{
    list($y, $m, $d) = explode('-', $dateTo);
    if(!checkdate($m, $d, $y)){
      $errors .= 'DateTo invalid format ' . PHP_EOL;
    }
  }

  if(!empty($errors)){
    return rest_ensure_response(new WP_Error($errors));
  }
  //end validation

   $newObject = new CalendarEvent(null,$dateFrom,$dateTo,$post_id);
   $response = $newObject->create();

   // catch db errors ( Date is already booked / not enough days between dates )
   $response = isset($response->ERROR) ? new WP_ERROR($response->ERROR) : json_encode( $response );

   return rest_ensure_response( $response );

}

function delete_tmc_event($request){

  if(isset($request['id'])){
    $objToDelete = new CalendarEvent($request['id']);
    $status = $objToDelete->delete();
    return rest_ensure_response($status);
  }else {
    return rest_ensure_response(new WP_ERROR("Id property was not found in the request"));
  }

}

function makeDateObject($date){
  return strtotime($date);
}

function checkIfProperDate($date){
  list($y, $m, $d) = explode('-', $date);
  if(!checkdate($m, $d, $y)){
    return false;
  }
  return true;
}

function hasCartReservationClash($date_from,$date_to){
  global $woocommerce;
  $dates = array_map( function ($obj){
            return array(
               'from' => $obj['date_from'],
               'to' => $obj['date_to']
            );
  } , $woocommerce->cart->cart_contents);
  $date_from  = makeDateObject($date_from);
  $date_to   = makeDateObject($date_to);

  $foundClash = false;
  foreach ($dates as $key => $item) {
    $dateFrom = makeDateObject($item['from']);
    $dateTo   = makeDateObject($item['to']);
    if (
      # insidecheckCartReservations
      ( $dateFrom  >= $date_from && $dateFrom <  $date_to ) // param dateFrom between dates
      ||
      ( $dateTo > $date_from && $dateTo <= $date_to ) // param dateTo between dates
      # around
      ||
      ( $date_from >= $dateFrom && $date_from < $dateTo ) // date_from between params
      ||
      ( $date_to > $dateFrom && $date_to <= $dateTo ) // date_to between params
    ){
      $foundClash = true;
    }
  }
  return $foundClash; //  $dateFrom . " >= " . $date_from; //  ( $dateFrom  >= $date_from && $dateFrom <  $date_to ); // param dateFrom between dates

}

function add_reservation_to_cart($request){
  global $woocommerce;
  $quantity = $request['quantity'];
  $product_id = $request['postId'];
  $date_from = $request['dateFrom'];
  $date_to = $request['dateTo'];
  if( !checkIfProperDate($date_from) && !checkIfProperDate($date_to)) {
    return rest_ensure_response(new WP_ERROR("dateFrom or dateTo is in incorrect format( dateFrom: "
     .$date_from . " | dateTo: " . $date_to ));
  }
  if(hasCartReservationClash($date_from,$date_to)){
      return rest_ensure_response(new WP_ERROR("This date is already booked in your cart."));
  }
  if(!CalendarDatabase::check_availability($date_from, $date_to)){
    return rest_ensure_response(new WP_ERROR("This date is already booked."));
  }



  if(isset($product_id) && isset($quantity) && isset($date_from) && isset($date_to)){
    $woocommerce->cart->add_to_cart($product_id,
                                    $quantity,
                                    $variation_id = '',
                                    $variation = '',
                                    $cart_item_data = array(
                                      'date_from' => $date_from,
                                      'date_to'   => $date_to
                                    ));

  }else{
    return rest_ensure_response(new WP_ERROR("Check that the requests has the keys: ( quantity, productId, dateFrom, dateTo )"));
  }

}
