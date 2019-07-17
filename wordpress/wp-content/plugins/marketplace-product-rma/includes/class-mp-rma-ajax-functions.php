<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MP_Rma_Ajax' ) )
{
    /**
     *   RMA: Ajax functions front-end
     */
    class MP_Rma_Ajax
    {

        function __construct()
        {
            // return order items
            add_action( 'wp_ajax_nopriv_mp_rma_get_order_items', array( $this, 'mp_rma_get_order_items' ) );
            add_action( 'wp_ajax_mp_rma_get_order_items', array( $this, 'mp_rma_get_order_items' ) );

            // update rma status
            add_action( 'wp_ajax_nopriv_mp_update_rma_status', array( $this, 'mp_update_rma_status' ) );
            add_action( 'wp_ajax_mp_update_rma_status', array( $this, 'mp_update_rma_status' ) );

						// check product author
						add_action( 'wp_ajax_nopriv_mp_check_product_author', array( $this, 'mp_check_product_author' ) );
						add_action( 'wp_ajax_mp_check_product_author', array( $this, 'mp_check_product_author' ) );
        }

        // return order items
        function mp_rma_get_order_items()
        {
            if ( !isset( $_POST[ 'nonce' ] ) && empty( $_POST[ 'nonce' ] ) )
            {
                wp_die('Security check failed.');
            }
            else
            {

                $nonce = $_POST[ 'nonce' ];

                if ( ! wp_verify_nonce( $nonce, 'rma_ajax_nonce' ) )
                {
                    die( 'Security check failed.' );
                }
                else
                {
                    global $wpdb;
                    $order_id = $_POST[ 'order_id' ];
                    $order = wc_get_order( $order_id );
                    $items = $order->get_items();
                    $response = '';
										$item_requested = array();
										$sql = $wpdb->get_results( "Select items from {$wpdb->prefix}mp_rma_requests where order_no = '$order_id'", ARRAY_A );
										if ( $sql ) {
												foreach ($sql as $key => $value) {
														foreach ( maybe_unserialize($value['items'])['items'] as $k => $val ) {
																$item_requested[] = $val;
														}
												}
										}

                    foreach ( $items as $item )
										{
												if ( !in_array( $item['product_id'], $item_requested ) ):
														$product_author = get_post_field( 'post_author', $item['product_id'] );
		                        $response .= '<tr>
		                            <td>'.$item['name'].'</td>
		                            <td class="text-center">'.$item['quantity'].'</td>
		                            <td><input type="checkbox" class="check-item" name="mp_item_select['.$item['product_id'].']" value="'.$item['product_id'].'" /></td>
		                            <td>
		                                <select name="mp_rma_reason['.$item['product_id'].']" class="full-width form-control reason-select" disabled>';
		                                foreach ($this->mp_get_seller_rma_reason( $product_author ) as $key => $value) {
		                                    $response .='<option value="'.$value->id.'">'.$value->reason.'</option>';
		                                }
		                                $response .= '</select>
		                            </td>
		                            <td><input type="number" min="1" max="'.$item['quantity'].'" class="full-width form-control item-qty" name="mp_returned_quantity['.$item['product_id'].']" disabled /></td>
		                        </tr>';
												endif;
                    }

                    echo $response;

                }

            }
            wp_die();
        }

				// return rma reasons per Seller
				function mp_get_seller_rma_reason( $author_id )
				{
						global $wpdb;
						$table_name = $wpdb->prefix.'mp_rma_reasons';
						$wk_posts = $wpdb->get_results("Select * from $table_name where status = 'enabled' and user_id = '$author_id'");
						return $wk_posts;
				}

        // update rma status
        function mp_update_rma_status()
        {
            if ( !isset( $_POST[ 'nonce' ] ) && empty( $_POST[ 'nonce' ] ) )
            {
                wp_die('Security check failed.');
            }
            else
            {

                $nonce = $_POST[ 'nonce' ];

                if ( ! wp_verify_nonce( $nonce, 'rma_ajax_nonce' ) )
                {
                    die( 'Security check failed.' );
                }
                else
                {
                    global $wpdb;
                    $table = $wpdb->prefix.'mp_rma_requests';
                    $rma_id = $_POST['mp_rma_id'];
										$mp_data = apply_filters( 'mp_get_rma_data', $rma_id );
										$user_id = apply_filters( 'mp_rma_user_id', 'user_id' );
                    $sql = $wpdb->update( $table,
                        array(
                          'rma_status'  => 'cancelled'
                        ),
                        array(
                          'id'  => $rma_id
                        )
                    );
										if ( $sql )
										{
												$message = 'Hello'."\n\n";
												$headers = "From:".get_user_by( 'ID', $user_id )->user_email;
												$message .= 'RMA for order #'.$mp_data[0]->order_no.' has been cancelled by customer'."\n";
												$message .= 'RMA ID: '.$rma_id."\n\n";
												$message .= 'Thanks';
												wp_mail( get_option('admin_email'), 'RMA Status Changed', $message, $headers );
										}
                    echo $sql;
                }

            }

            wp_die();

        }

				// check product author
				function mp_check_product_author()
				{
						$ids = explode( ",", $_POST['product_id'] );
						$this_id = $_POST['this_id'];
						foreach ($ids as $key => $value)
						{
								$product_author[] = get_post_field( 'post_author', $value );
						}
						$a = count(array_unique($product_author));

						if ( $a == 1 ) :
								echo 'true';
						else :
								echo 'false';
						endif;
						wp_die();
				}

    }

    new MP_Rma_Ajax();

}
