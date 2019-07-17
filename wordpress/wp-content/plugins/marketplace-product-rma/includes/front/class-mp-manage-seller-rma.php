<?php

if ( !class_exists( 'MP_Manage_Seller_Rma' ) )
{
    /**
     *
     */
    class MP_Manage_Seller_Rma
    {

        function mp_manage_seller_rmas()
        {

            global $wpdb;
            $user_id  = apply_filters( 'mp_rma_user_id', 'user_id' );
            $table_name = $wpdb->prefix.'mp_rma_requests';
            $page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='".get_option('wkmp_seller_page_title')."'");
            $wk_data = $wpdb->get_results( "SELECT * from $table_name where seller_id = '$user_id' ORDER BY id ASC", ARRAY_A);

            ?> <div class="woocommerce-account"> <?php

                apply_filters( 'mp_get_wc_account_menu', 'marketplace' );

                ?>

                <div class="woocommerce-MyAccount-content">

                    <div id="main_container">

                    <h2><?php echo _e("RMA System"); ?></h2>

                    <?php echo '<a href="'.home_url("/".$page_name."/rma-reason").'" class="button">Manage Reason(s)</a>'; ?>

                    <table class="mpRmaList">
                        <thead>
                            <tr>
                                <th><?php _e("ID"); ?></th>
                                <th><?php _e("Order ID"); ?></th>
                                <th><?php _e("Customer Name"); ?></th>
                                <th><?php _e("Products"); ?></th>
                                <th><?php _e("Reason"); ?></th>
                                <th><?php _e("RMA Status"); ?></th>
                                <th><?php _e("Date"); ?></th>
                                <th><?php _e("Action"); ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ($wk_data as $key => $value) : ?>

                            <tr>
                                <td><?php _e( $value['id'] ); ?></td>
                                <td><?php _e( $value['order_no'] ); ?></td>
                                <td><?php _e( get_userdata($value['customer_id'])->display_name); ?></td>
                                <td><?php
                                  foreach (maybe_unserialize($value['items'])['items'] as $k => $val) {
                                      echo get_the_title($val).'<br>';
                                  }
                                 ?></td>
                                 <td><?php
                                   foreach (maybe_unserialize($value['items'])['reason'] as $k => $val) {
                                      $wk_post = $wpdb->get_results("Select reason from {$wpdb->prefix}mp_rma_reasons where id = '$val'", ARRAY_A);
                                      echo $wk_post[0]['reason'].'<br>';
                                   }
                                  ?></td>
                                  <td><?php echo '<strong class="wk_rma_status_'.$value['rma_status'].'">'.ucfirst($value['rma_status']).'</strong>'; ?></td>
                                  <td><?php echo $value['datetime']; ?></td>
                                  <td><a id="viewprod" title="Edit" href="../../<?php echo $page_name; ?>/rma/edit/<?php echo $value['id']; ?>">edit</a></td>
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
