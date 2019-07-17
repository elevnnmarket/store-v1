<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Add_Front_Rma' ) )
{
    /**
     *
     */
    class MP_Add_Front_Rma
    {

        function mp_add_new_rma()
        {

            if ( isset( $_POST['submit_rma_request'] ) )
            {
                do_action( 'mp_save_rma_request_details', $_POST );
            }
            foreach ( get_option('mp_rma_order_statuses') as $key => $value) {
                $status_arr[$value] = $value;
            }

            $days = get_option( 'mp_rma_time' );

            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => wc_get_order_types(),
                'post_status' => array_keys( $status_arr ),
                'date_query'  => array(
                    'after' => $days.' days ago'
                )
            ) );

            $user_id  = apply_filters( 'mp_rma_user_id', 'user_id' );

            $requested_rma = apply_filters( 'mp_get_customer_rma_order_id', $user_id );

						if ( null == $requested_rma ) {
								$requested_rma[0] = array();
						}

						?> <div class="woocommerce-account"> <?php

                apply_filters( 'mp_get_wc_account_menu', 'marketplace' );

                ?>

                <div class="woocommerce-MyAccount-content">

		            <h1><?php echo __('New RMA Information', 'marketplace-rma'); ?></h1>

		            <form method="post" action="" class="mp_request_rma" enctype="multipart/form-data">

		                <p><span class="required">* </span><label for="rma-order">Order</label></p>

		                <p><select name="mp_rma_order" id="mp-rma-order" class="full-width">
		                    <option value="">--Select--</option>
		                    <?php foreach ($customer_orders as $key => $value): ?>
		                        <?php if ( !in_array( $value->ID, $requested_rma ) ): ?>
		                            <option value="<?php echo $value->ID; ?>"><?php echo '#'.$value->ID.' '.$value->post_title; ?></option>
		                        <?php endif; ?>
		                    <?php endforeach; ?>
		                </select></p>

		                <p><span class="required">* </span><label for="item">Items Ordered</label></p>

		                <div class="responsive"><table class="mp_rma_items_ordered" border="1">
		                    <thead>
		                        <tr>
		                            <th>Product Name</th>
		                            <th>Quantity</th>
		                            <th></th>
		                            <th>Reason</th>
		                            <th>Returned Quantity</th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
		                </table></div>

		                <p><label for="images">Image(s) of Product</label></p>

		                <div class="form-elm form-images">
		      							<div id="mk-rss-img-wrapper">
		          							<label class="image-preview" id="mk-rss-attach-img-label-1" for="mk-rss-attach-img-1" ><span onclick=remove_preview(1) class="mk-rss-image-remove">x</span>
		          							<input type="file" onchange=image_selected(1) name="product-img-1" class="hide-input" id="mk-rss-attach-img-1"></label>
		      							</div>
		      							<span class="attach-more"><a id="mk-rss-attach-more">+ Attach image</a></span>
		    						</div>

		                <p><span class="required">* </span><label for="info">Additional Information</label></p>

		                <p><textarea rows="4" class="mp_rma_add_info" name="mp_add_info"></textarea></p>

		                <p><span class="required">* </span><label for="status">Order Delivery Status</label></p>

		                <p>
		                  <select id="status" name="mp_order_status" class="mp_order_status full-width">
		                      <option value="">--Select--</option>
		                      <option value="complete">Complete</option>
		                      <option value="pending">Pending</option>
		                  </select>
		                </p>

		                <p><span class="required">* </span><label for="resolution">Resolution Type</label></p>

		                <p>
		                  <select id="resolution" name="mp_resolution_type" class="mp_resolution full-width">
		                      <option value="">--Select--</option>
		                      <option value="refund">Refund</option>
		                      <option value="exchange">Exchange</option>
		                  </select>
		                </p>

		                <p><label for="consignment">Please add consignment no. if you are returning product(s)</label></p>

		                <p><input type="text" name="mp_autono" class="full-width" id="consignment" value=""></p>

		                <p><label for="policy">Return Policy</label></p>

		                <p class="policy-content"><?php echo get_option('mp_rma_policy'); ?></p>

		                <p><span class="required">* </span><input name="mp_policy_agree" id="wk_i_agree" type="checkbox" class="wk_rma_checkall" data-validate="{required:true}">
		                <label for="wk_i_agree"><span>I have read and agree to the policy.</span></label></p>

		                <?php wp_nonce_field( 'request_rma_nonce_action', 'request_rma_nonce' ); ?>

		                <p style="float:right"><input type="submit" id="mp_rma_add_button" value="Request" name="submit_rma_request" class="woocommerce-button button"></p>

		                <p><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id')); ?>rma" class="woocommerce-button button">Back</a></p>

		            </form>

						</div>

					</div>

            <?php

        }

    }

}
