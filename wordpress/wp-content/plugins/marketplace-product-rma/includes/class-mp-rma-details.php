<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Rma_Details' ) )
{
    /**
     *
     */
    class MP_Rma_Details
    {

        function __construct()
        {
            add_action( 'mp_rma_view_details', array( $this, 'mp_customer_rma_details' ) );
        }

        function mp_customer_rma_details()
        {
            $rma_id   = apply_filters( 'mp_rma_id', 'rma_id' );
            $wk_data = apply_filters( 'mp_get_rma_data', $rma_id );

            if ( isset( $_GET['rid'] ) )
            {
                $order_url = get_edit_post_link($wk_data[0]->order_no);
            }
            else
            {
                $order_url = get_permalink( get_option('woocommerce_myaccount_page_id') ).'view-order/'.$wk_data[0]->order_no;
            }

            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <td class="toptable"><strong>Option</strong></td>
                        <td class="toptable"><strong>Value</strong></td>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>Order ID</td>
                        <td>#<?php echo $wk_data[0]->order_no; ?></td>
                    </tr>

                    <tr>
                        <td>RMA Status</td>
                        <td>
                          <strong class="wk_rma_status_<?php echo $wk_data[0]->rma_status; ?>"><?php echo ucfirst($wk_data[0]->rma_status); ?></strong>
                        </td>
                    </tr>

                    <tr>
                        <td>Delivery Status</td>
                        <td>
                          <strong class="wk_delivery_status"><?php echo ucfirst($wk_data[0]->order_status); ?></strong>
                        </td>
                    </tr>

                    <tr>
                        <td>Customer Name</td>
                        <td><?php echo get_userdata($wk_data[0]->customer_id)->display_name; ?></td>
                    </tr>

                    <tr>
                        <td>Resolution Type</td>
                        <td><?php echo ucfirst($wk_data[0]->resolution); ?></td>
                    </tr>

                    <tr>
                        <td>Additional Information</td>
                        <td><?php echo $wk_data[0]->information; ?></td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td class="toptable"><strong>Option</strong></td>
                        <td class="toptable"><strong>Value</strong></td>
                    </tr>
                </tfoot>
            </table>
            <?php
        }

    }

    new MP_Rma_Details();

}
