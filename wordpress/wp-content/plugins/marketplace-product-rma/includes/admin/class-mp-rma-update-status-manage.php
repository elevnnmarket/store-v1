<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Rma_Update_Status_Manage' ) )
{
    /**
     *
     */
    class MP_Rma_Update_Status_Manage
    {

        function __construct()
        {
            add_action( 'mp_rma_view_shipping_label', array( $this, 'mp_rma_view_update_status' ) );
        }

        function mp_rma_view_update_status()
        {
            global $wpdb;
            $rma_id   = apply_filters( 'mp_rma_id', 'user_id' );
						$wk_data = apply_filters( 'mp_get_rma_data', $rma_id );
            $table = $wpdb->prefix.'mp_rma_requests';
            $meta_table = $wpdb->prefix.'mp_rma_request_meta';

            if ( isset( $_POST['submit_rma_status'] ) )
            {

                if ( isset( $_POST['wk_update_status'] ) )
                {
                    $status = $_POST['wk_update_status'];
                    $sql_status = $wpdb->update( $table,
                        array(
                          'rma_status'  => $status
                        ),
                        array(
                          'id'  => $rma_id
                        )
                    );

										if ( $sql_status )
										{
												$message = 'Hello'."\n\n";
												$message .= 'RMA status changed to "'.$status.'" by Seller for order #'.$wk_data[0]->order_no."\n";
												$message .= 'RMA ID: '.$rma_id."\n\n";
												$message .= 'Thanks';
												wp_mail( get_userdata($wk_data[0]->customer_id)->user_email, 'RMA Status Changed', $message );
										}
                }

                // update label
                if ( isset( $_POST['shipping_label'] ) )
                {
                    $label = $_POST['shipping_label'];
                    $result = $wpdb->get_results("Select meta_value from $meta_table where meta_key = 'shipping_label' and rma_id = '$rma_id'");
                    if ($result)
                    {
                        $sql = $wpdb->update( $meta_table,
                            array(
                              'meta_value'  => $label
                            ),
                            array(
                              'rma_id'  => $rma_id,
                              'meta_key'=> 'shipping_label'
                            )
                        );
                    }
                    else {
                        $sql = $wpdb->insert( $meta_table,
                            array(
                              'rma_id'      => $rma_id,
                              'meta_key'    => 'shipping_label',
                              'meta_value'  => $label
                            )
                        );
                    }

                }

            }

            $wk_data = apply_filters( 'mp_get_rma_data', $rma_id );
            $shipping_labels = apply_filters( 'mp_get_shipping_labels', $rma_id );

            $dir = wp_upload_dir();

            $result = $wpdb->get_results("Select meta_value from $meta_table where meta_key = 'shipping_label' and rma_id = '$rma_id'");

            ?>
            <form method="post" action="">

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="rma_status">RMA Status</label>
                            </th>
                            <td class="forminp">
                                <select name="wk_update_status" id="rma_status" style="min-width:350px;">
                                    <option value="">-- Select --</option>
                                    <option value="processing" <?php if ($wk_data[0]->rma_status == 'processing') echo 'selected'; ?>>Approve</option>
                                    <option value="declined" <?php if ($wk_data[0]->rma_status == 'declined') echo 'selected'; ?>>Decline</option>
                                    <option value="solved" <?php if ($wk_data[0]->rma_status == 'solved') echo 'selected'; ?>>Solved</option>
                                </select>
                            </td>
                        </tr>

                        <?php if ( $wk_data[0]->resolution == 'exchange' ) : ?>

                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="">Select Shipping Label</label>
                                </th>
                                <td class="forminp">
                                    <?php foreach ($shipping_labels as $key => $value): ?>
                                      <div class="shipping_label_each">
                                          <label for="shipping_label_<?php echo $key; ?>"><img src="<?php echo $dir['baseurl'].$value; ?>" class="shipping_label_img"></label>
                                          <input id="shipping_label_<?php echo $key; ?>" value="<?php echo $value; ?>" type="radio" <?php if ($result && $result[0]->meta_value == $value) echo 'checked'; ?> name="shipping_label" class="shipping_label">
                                      </div>
                                    <?php endforeach; ?>
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>
                <p><input type="submit" name="submit_rma_status" class="button button-primary" value="Update" /></p>
            </form>
            <?php
        }

    }

    new MP_Rma_Update_Status_Manage();

}
