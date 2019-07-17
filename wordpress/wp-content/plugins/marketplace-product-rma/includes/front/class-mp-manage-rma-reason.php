<?php

if ( !class_exists( 'MP_Manage_Seller_Rma_Reason' ) )
{
    /**
     *
     */
    class MP_Manage_Seller_Rma_Reason
    {

        function mp_manage_seller_reason()
        {
            global $wpdb;

            $wpmp_pid = '';

            $user_id = apply_filters( 'mp_rma_user_id', 'user_id' );

            $wk_data = apply_filters( 'mp_get_rma_reasons', $user_id );

            $mainpage = get_query_var( 'main_page' );

            $p_id = get_query_var( 'pid' );

    				$action = get_query_var( 'action' );

    				if( ! empty( $p_id ) )
            {
    					  $wpmp_pid = $p_id;
            }

    				$product_auth = $wpdb->get_var("select user_id from {$wpdb->prefix}mp_rma_reasons where id='$wpmp_pid'");

            $page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='".get_option('wkmp_seller_page_title')."'");

            if( ! empty( $mainpage ) && ! empty( $action ) )
            {

              	if( $mainpage == 'rma-reason' && $action == 'delete' && $product_auth == $user_id )
      					{
                    $sql = $wpdb->delete( $wpdb->prefix.'mp_rma_reasons', array( 'id' => $wpmp_pid ) );

                    if ( $sql )
                    {
                        wp_redirect( site_url().'/'.$page_name.'/rma-reason');
                        exit;
                    }
                }

            }

            ?> <div class="woocommerce-account"> <?php

                apply_filters( 'mp_get_wc_account_menu', 'marketplace' );

                ?>

                <div class="woocommerce-MyAccount-content">

                    <div id="main_container">

                    <?php echo '<a href="../add-reason" class="button mp-button-right">Add</a>'; ?>

                    <h2><?php echo _e("RMA Reason"); ?></h2>

                    <table class="reasonlist">
                        <thead>
                            <tr>
                                <th><?php echo _e("Reason"); ?></th>
                                <th><?php echo _e("Status"); ?></th>
                                <th><?php echo _e("Action"); ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ( $wk_data as $key => $value ): ?>
                                <tr>
                                    <td><?php echo _e($value['reason']); ?></td>
                                    <td><?php echo _e(ucfirst($value['status'])); ?></td>
                                    <td><?php echo '<a id="editprod" class="mp-action" href="edit/'.$value['id'].'">edit</a>' ?><a id="delprod" class="mp-action" href="delete/<?php echo $value['id']; ?>" onclick="return confirm('Are you sure ? Delete Reason also affect RMA Order and you can lost some data related to these reasons !!')" class="ask">delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>
            <?php
        }

    }

}
