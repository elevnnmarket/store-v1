<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Save_Rma_Reason' ) )
{
    /**
     *
     */
    class MP_Save_Rma_Reason
    {

        function __construct()
        {
	      		add_action( 'mp_save_reason_rma', array( $this, 'mp_save_reason_rma' ), 1 );
        }
        function mp_save_reason_rma( $data )
        {
						global $wpdb;

            if ( ! isset( $data['rma_reason_nonce'] ) || ! wp_verify_nonce( $data['rma_reason_nonce'], 'rma_reason_nonce_action' ) )
            {
                print 'Sorry, your nonce did not verify.';
                exit;
            }
            else
            {
                if ( ! empty( $data['wk_rma_reason'] ) && ! empty( $data['wk_rma_status'] ) )
                {
                    $reason = $data['wk_rma_reason'];
                    $status = $data['wk_rma_status'];
                    $table_name = $wpdb->prefix.'mp_rma_reasons';
										$user_id = apply_filters( 'mp_rma_user_id', 'user_id' );
										$page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_title ='".get_option('wkmp_seller_page_title')."'");

                    if ( empty( $data['reason_id'] ) )
                    {
                        $sql = $wpdb->insert(
	                          $table_name,
	                          array(
																'user_id'	=> $user_id,
		                            'reason'  => $reason,
		                            'status'  => $status
	                          )
                        );
                    }
                    else
										{
	                      $sql = $wpdb->update(
		                        $table_name,
		                        array(
																'user_id'	=> $user_id,
			                          'reason'  => $reason,
			                          'status'  => $status
		                        ),
		                        array(
		                          	'id' => $data['reason_id']
		                        )
	                      );
                    }

                    if ( $sql )
										{
												if ( ! is_admin() )
												{
														wp_redirect( site_url().'/'.$page_name.'/rma-reason');
														exit;
												}
												else
												{
														wp_redirect(site_url().'/wp-admin/admin.php?page="mp-rma-reasons"');
														exit;
												}
                    }
                }

                else {
                    echo '<p class="required">Fill all fields.</p>';
                }

            }

        }

    }

		new MP_Save_Rma_Reason();

}
