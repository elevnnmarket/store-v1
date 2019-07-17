<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_Save_Rma_Label' ) )
{
    /**
     *
     */
    class MP_Save_Rma_Label
    {

        function __construct()
        {
            add_action( 'mp_save_rma_shipping_label', array( $this, 'mp_save_rma_shipping_label' ), 1 );
        }

        function mp_save_rma_shipping_label( $data )
        {
            $dir = wp_upload_dir();
            $label_url = $data['shipping_label_path'];
						if ($label_url) {
		            $label_url = str_replace( $dir[ 'baseurl' ], "", $label_url );
		            $user_id = apply_filters( 'mp_rma_user_id', 'user_id' );
		            $paths = get_user_meta( $user_id, 'mp_rma_shipping_label_path', true );

		            if ( empty( $paths ) )
		            {
		                $path_data = array();
		            }
		            else
		            {
		                $path_data = $paths;
		            }

		            $path_data[] = $label_url;

		            $check = update_user_meta( $user_id, 'mp_rma_shipping_label_path', $path_data );

		            if ( $check )
		            {
		                ?>
		                <div class="notice notice-success">
		                    <p><?php _e( 'Shipping label uploaded successfully!', 'rma' ); ?></p>
		                </div>
		                <?php
		            }
						}
        }

    }

    new MP_Save_Rma_Label();

}
