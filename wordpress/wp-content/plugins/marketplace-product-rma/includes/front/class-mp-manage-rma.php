<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Manage_Front_Rma' ) )
{
    /**
     *
     */
    class MP_Manage_Front_Rma
    {

        public static $endpoint = 'rma';

        function __construct()
        {
            add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'mp_rma_endpoint_content' ) );
        }


        function mp_rma_endpoint_content()
        {
            global $wp_query, $wpdb;
            $table_name = $wpdb->prefix.'mp_rma_requests';
            $user_id = apply_filters( 'mp_rma_user_id', 'user_id' );
						$page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='".get_option('wkmp_seller_page_title')."'");

            if ( isset($wp_query->query_vars['rma']) && ( $wp_query->query_vars['rma'] == 'rma' || ( !empty($wp_query->query_vars['rma']) && is_numeric($wp_query->query_vars['rma']) ) ) )
						{
								$mp_rma_data = apply_filters( 'mp_get_rma_data_by_customer', $user_id, $wp_query->query_vars['rma'] );
								$wk_data = $mp_rma_data['data'];
                ?>
								<?php if ( $wk_data ) : ?>
                <a href="../../<?php echo $page_name; ?>/rma/add" class="woocommerce-button button" title="Request New RMA">Add</a>
                <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                		<thead>
                			<tr>
        									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-id"><span class="nobr">ID</span></th>
        									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-order-id"><span class="nobr">Order ID</span></th>
        									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr">RMA Status</span></th>
                          <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Date</span></th>
                          <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="nobr">Action</span></th>
        							</tr>
                		</thead>

                		<tbody>
											<?php foreach ($wk_data as $key => $value): ?>

                	     	<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-cancelled order">
                               <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-id" data-title="ID">
                    					    <?php echo $value->id; ?>
                    					 </td>
                    					 <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-id" data-title="Order ID">
                    						 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>view-order/<?php echo $value->order_no; ?>">#<?php echo $value->order_no; ?></a>
                    					 </td>
                    					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="Status">
                                  <strong class="wk_rma_status_<?php echo $value->rma_status; ?>"><?php echo ucfirst($value->rma_status); ?></strong>
                              </td>
                              <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Date">
                    					  	<time datetime=""><?php echo date( "F j, Y H:i:s", strtotime($value->datetime) ); ?></time>
                              </td>
                              <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="Actions">
                                  <a href="<?php echo site_url('/') . $page_name; ?>/rma/edit/<?php echo $value->id; ?>" class="rma-action view">View</a>
                                  <?php if ( $value->rma_status != 'cancelled' && $value->rma_status != 'solved' ): ?>
                                    <span class="rma-action"> | </span>
                    					  	  <a href="" data-rma-id="<?php echo $value->id; ?>" class="mp-rma-action cancel">Cancel</a>
                                  <?php endif; ?>
                              </td>
                					</tr>
										<?php endforeach; ?>
                		</tbody>
                </table>
                <?php
								echo $mp_rma_data['count'];
								else :
								?>
								<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info"><a class="woocommerce-Button button" href="../../<?php echo $page_name; ?>/rma/add">Add</a>No RMA has been made yet.</div>
								<?php
								endif;
            } else {
							?>
							<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info"><a class="woocommerce-Button button" href="<?php echo wc_get_endpoint_url('rma'); ?>">Back</a><?php echo __('Not found.', 'marketplace-rma'); ?></div>
							<?php
						}

        }

    }

    new MP_Manage_Front_Rma();

}
