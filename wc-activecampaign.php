<?php
/**
 * Plugin Name: WooCommerce Customer to Active Campaign
 * Plugin URI: https://rayflores.com/plugins/wc-ac/
 * Description: Sends WooCommerce Customer/Order details to Active Campaign via AC API
 * Author: rayflores
 * Version: 0.1
 * Author URI: https://rayflores.com
 * Text Domain: wc-ac
 * Domain Path: /languages/
 */
add_action( 'plugins_loaded', 'fire_it_up' );
function fire_it_up(){
  return WC_Active_Campaign::get_instance();
}

class WC_Active_Campaign {
  
  private static $instance = null;
  
  
  
  public function __construct() {
    add_action( 'add_meta_boxes', array( $this, 'custom_add_meta_boxes_to_order_admin' ) );
    add_action( 'woocommerce_thankyou', array( $this, 'send_wp_request' ) ); 
  }
  // Adding Meta container admin shop_order pages
  function custom_add_meta_boxes_to_order_admin()
  {
    add_meta_box( 'student_detail_fields', __('Student Details','woocommerce'), array( $this, 'custom_add_meta_fields_to_order_admin' ), 'shop_order', 'normal', 'low' );
  }
  /**
   * Display field value on the order edit page
   */
  public function custom_add_meta_fields_to_order_admin(){
    global $post;

    $student_first_name = get_post_meta( $post->ID, 'student_first_name', true ) ? get_post_meta( $post->ID, 'student_first_name', true ) : '';
    $student_last_name = get_post_meta( $post->ID, 'student_last_name', true ) ? get_post_meta( $post->ID, 'student_last_name', true ) : '';
    $student_email = get_post_meta( $post->ID, 'student_email', true ) ? get_post_meta( $post->ID, 'student_email', true ) : '';
    $student_address_1 = get_post_meta( $post->ID, 'student_address_1', true ) ? get_post_meta( $post->ID, 'student_address_1', true ) : '';
    $student_address_2 = get_post_meta( $post->ID, 'student_address_2', true ) ? get_post_meta( $post->ID, 'student_address_2', true ) : '';
    $student_city = get_post_meta( $post->ID, 'student_city', true ) ? get_post_meta( $post->ID, 'student_city', true ) : '';
    $student_state = get_post_meta( $post->ID, 'student_state', true ) ? get_post_meta( $post->ID, 'student_state', true ) : '';
    $student_zip = get_post_meta( $post->ID, 'student_zip', true ) ? get_post_meta( $post->ID, 'student_zip', true ) : '';
    $student_country = get_post_meta( $post->ID, 'student_country', true ) ? get_post_meta( $post->ID, 'student_country', true ) : '';
    $student_phone = get_post_meta( $post->ID, 'student_phone', true ) ? get_post_meta( $post->ID, 'student_phone', true ) : '';
  ?>
    <table class="widefat fixed" cellspacing="0">
      <thead>
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">Address</th>
        <th scope="col">City</th>
        <th scope="col">State</th>
        <th scope="col">Zip</th>
        <th scope="col">Country</th>
        <th scope="col">Phone</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td><?php echo $student_first_name . ' ' . $student_last_name; ?></td>
        <td><?php echo $student_email; ?></td>
        <td><?php echo $student_address_1 . '<br/>' . $student_address_2; ?></td>
        <td><?php echo $student_city; ?></td>
        <td><?php echo $student_state; ?></td>
        <td><?php echo $student_zip; ?></td>
        <td><?php echo $student_country; ?></td>
        <td><?php echo $student_phone; ?></td>
      </tr>
      </tbody>
    </table><?php

  }
 
  public function send_wp_request( $order_id ){
    $order = wc_get_order( $order_id ); // WC_Order object
    $user = $order->get_user(); // WC_User object
    // This section takes the input fields and converts them to the proper format
    $params = array(
      // the API Key can be found on the "Your Settings" page under the "API" tab.
      // replace this with your API Key
        'api_key'      => '5b56be9509d2bf2fade963c475b0c7beb835ef0b10b57727c4966d08f580ba337239f5e1',
      // this is the action that adds a contact
        'api_action'   => 'contact_sync',
      // define the type of output you wish to get back
      // possible values:
      // - 'xml'  :      you have to write your own XML parser
      // - 'json' :      data is returned in JSON format and can be decoded with
      //                 json_decode() function (included in PHP since 5.2.0)
      // - 'serialize' : data is returned in a serialized format and can be decoded with
      //                 a native unserialize() function
        'api_output'   => 'json',
    );
    $query = "";
    foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
    $query = rtrim($query, '& ');

    $url = 'http://nutritiouslife.api-us1.com/admin/api.php?' . $query;

    $items_meta_data = $order->get_meta_data();
    $student_first_name = $student_last_name = $student_email = $student_address_1 = $student_address_2 = $student_city = $student_state = $student_zip = $student_country = $student_phone = '';

    foreach( $items_meta_data as $item_meta_data ){
      if ( $item_meta_data->key === 'student_first_name' ) {
        $student_first_name = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_last_name' ) {
        $student_last_name = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_email' ) {
        $student_email = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_address_1' ) {
        $student_address_1 = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_address_2' ) {
        $student_address_2 = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_city' ) {
        $student_city = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_state' ) {
        $student_state = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_zip' ) {
        $student_zip = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_country' ) {
        $student_country = $item_meta_data->value;
      }
      if ( $item_meta_data->key === 'student_phone' ) {
        $student_phone = $item_meta_data->value;
      }

    }
// here we define the data we are posting in order to perform an update
    $post = array(
        'email'                    => $order->get_billing_email(), // wc billing_email
        'first_name'               => $order->get_billing_first_name(), // wc billing_first_name
        'last_name'                => $order->get_billing_last_name(), // wc billing_last_name
        'phone'                    => $order->get_billing_phone(), // wc billing_phone 
      // any custom fields
        'field[%EMAIL_ADDRESS_FOR_COMMISSIONS%,0]'      => $order->get_billing_email(), // wc billing_email
        'field[%USERNAME%,0]'      => $user->user_login, // WP_User login
        'field[%PASSWORD%,0]'      => $user->user_pass, // WP_User pass
        'field[%CONFIRM_PASSWORD%,0]'      => $user->user_pass, // WP_User pass
        'field[%BILLING_ADDRESS_LINE_1%,0]'      => $order->get_billing_address_1(), // wc billing_address _1
        'field[%BILLING_ADDRESS_LINE_2%,0]'      => $order->get_billing_address_2(), // wc billing_address_2
        'field[%BILLING_CITY%,0]'      => $order->get_billing_city(), // wc billing_city
        'field[%BILLING_STATE%,0]'      => $order->get_billing_state(), // wc billing_state
        'field[%BILLING_ZIP_CODE%,0]'      => $order->get_billing_postcode(), // wc billing_postcode
      // STUDENT DETAILS
        'field[%STUDENT_NAME%,0]'      => $student_first_name . ' ' . $student_last_name, // using the personalization tag instead
        'field[%STUDENT_EMAIL%,0]'      => $student_email, // using the personalization tag instead
        'field[%STUDENT_ADDRESS_LINE_1%,0]'      => $student_address_1, // using the personalization tag instead
        'field[%STUDENT_ADDRESS_LINE_2%,0]'      => $student_address_2, // using the personalization tag instead
        'field[%STUDENT_CITY%,0]'      => $student_city, // using the personalization tag instead
        'field[%STUDENT_STATE%,0]'      => $student_state, // using the personalization tag instead
        'field[%STUDENT_ZIP%,0]'      => $student_zip, // using the personalization tag instead
        'field[%STUDENT_COUNTRY%,0]'      => $student_country, // using the personalization tag instead
        'field[%STUDENT_PHONE%,0]'      => $student_phone, // using the personalization tag instead

    );
    // This section takes the input data and converts it to the proper format
    $data = "";
    foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
    $data = rtrim($data, '& ');
    
    $wp_args =       array( 'body' => $data ,
      'method' => 'POST');
    sleep(5); // wait for wc to send first, then update contact in Active Campaign
    $request = wp_remote_request( $url, $wp_args );

    //Check for success
    if(!is_wp_error($request) && ($request['response']['code'] == 200 || $request['response']['code'] == 201)) {
      $note = json_decode( $request['body'], true );
      $message = 'You can view the user in Active Campaign <a href="https://nutritiouslife.activehosted.com/app/contacts/'. $note['subscriber_id'] . '" target="_blank">HERE</a>';
      $order->add_order_note( $note['result_message'] . "\n" . $message );
    }
    else {
      $note = json_decode( $request['body'], true );
      $message = 'User Not Updated in Active Campaign!';
      $order->add_order_note( $note['result_message'] . "\n" . $message );
    }
  }
  /**
   * Returns the running object
   *
   * @return WC_Active_Campaign
   **/
  public static function get_instance() {
    if( is_null( self::$instance ) ) {
      self::$instance = new self();
//				self::$instance->hooks();
    }
    return self::$instance;
  }
}