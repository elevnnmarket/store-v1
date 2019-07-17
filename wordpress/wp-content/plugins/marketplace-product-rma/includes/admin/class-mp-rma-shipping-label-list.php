<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'MP_List_Shipping_Label' ) )
{

    if( !class_exists( 'WP_List_Table' ) ){

      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

    }
    /**
     *
     */
    class MP_List_Shipping_Label extends WP_List_Table
    {

        function __construct()
        {
            parent::__construct( array(
                'singular'	=> 'Shipping Label',
                'plural' 	=> 'Shipping Labels',
                'ajax'   	=> false
            ) );
        }

        function prepare_items()
  			{

      			global $wpdb;

        		$columns = $this->get_columns();

        		$hidden = $this->get_hidden_columns();

        		$this->process_bulk_action();

        		$data = $this->table_data();

        		$totalitems = count($data);

        		$user = get_current_user_id();

        		$screen = get_current_screen();

        		$perpage = $this->get_items_per_page('product_per_page', 20);

        		$this->_column_headers = array( $columns, $hidden );

        		if ( empty ( $per_page) || $per_page < 1 )
  					{

          			$per_page = $screen->get_option( 'per_page', 'default' );

        		}

        		function usort_reorder($a,$b)
  					{

      					$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title

            		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc

            		$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order

            		return ($order==='asc') ? $result : -$result; //Send final sort direction to usort

      			}

        		usort($data, 'usort_reorder');

            $totalpages = ceil($totalitems/$perpage);

            $currentPage = $this->get_pagenum();

            $data = array_slice($data,(($currentPage-1)*$perpage),$perpage);

            $this->set_pagination_args( array(

              	"total_items" => $totalitems,

              	"total_pages" => $totalpages,

              	"per_page" => $perpage,

            ));

            $this->items =$data;

        }

    		/**
    		 * Define the columns that are going to be used in the table
    		 * @return array $columns, the array of columns to use with the table
    		 */

    		function get_columns()
  			{
    		   	return $columns= array (
      		   		'cb'         	    => '<input type="checkbox" />', //Render a checkbox instead of text
              	'shipping_label'	=> __('Image'),
                'label_name'		  => __('Name')
    		   	);
    		}

    		function column_default($item, $column_name)
  			{

        		switch( $column_name )
  					{
          			case 'shipping_label':
                case 'label_name':
            				return $item[ $column_name ];
          			default:
            				return print_r($item, true);
        		}

      	}

    		public function get_hidden_columns()
  			{
    			   return array();
    		}

    		function column_cb($item)
        {
    			   return sprintf('<input type="checkbox" id="sid_%s" name="sid[]" value="%s" />',$item['id'], $item['id']);
    		}

    		private function table_data()
        {

    	    	global $wpdb;

    	    	$data = array();

            $user_id = apply_filters( 'mp_rma_user_id', 'user_id' );

            $wk_data = get_user_meta( $user_id, 'mp_rma_shipping_label_path', true );

    	    	$i = 0;

            $label_id = array();
            $label_image = array();
            $label_name = array();

            $dir = wp_upload_dir();

            if ( $wk_data )
            {

        	    	foreach ( $wk_data as $key => $value )
                {
                  $label_id[]    = $key;
                  $label_image[] = $dir['baseurl'].$value;
                  $label_name[]  = $value;

        					$data[] = array(
                      'id'  => $label_id[$i],
                      'shipping_label'  => '<img src="'.$label_image[$i].'" alt="Placeholder" width="50" class="woocommerce-placeholder wp-post-image" height="50">',
                      'label_name' => $label_name[$i]
        					);

        					$i++;

        	    	}

            }

    	    	return $data;

    	    }

  				function get_bulk_actions()
  				{
  						$actions = array(
  				    		'delete'    => 'Delete'
  				  	);
  				   	return $actions;
  				}

  				function process_bulk_action()
  				{

  						if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
  								$nonce  = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
  					      $action = 'bulk-' . $this->_args['plural'];
  					      if ( ! wp_verify_nonce( $nonce, $action ) )
  					          wp_die( 'Nope! Security check failed!' );
  						}


              $user_id = apply_filters( 'mp_rma_user_id', 'user_id' );

              $wk_data = get_user_meta( $user_id, 'mp_rma_shipping_label_path', true );

  						if ( $this->current_action() == 'delete' )
  						{

                  $label_id = filter_input( INPUT_GET, 'sid', FILTER_SANITIZE_STRING );
    							foreach ( $wk_data as $key => $value ) {
    									if ( $key == $label_id ) {
      									  unset($wk_data[$key]);
    									}
    							}

                  $check = update_user_meta( $user_id, 'mp_rma_shipping_label_path', $wk_data );

                  if ( $check )
                  {
                      wp_redirect(site_url().'/wp-admin/admin.php?page=mp-rma-config&tab=shipping_label');
                      exit;
                  }

              }

  				}

    			function column_shipping_label($item)
          {

          		$actions = array(
        					'delete'   => sprintf('<a href="admin.php?page=mp-rma-config&tab=shipping_label&sid=%s&_wpnonce=%s&action=delete" class="delete-rma">Delete</a>',$item['id'], wp_create_nonce('bulk-shippinglabels') )
          		);

          		return sprintf( '%1$s %2$s', $item['shipping_label'], $this->row_actions($actions) );

      		}

    }

    $list_obj = new MP_List_Shipping_Label();
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Shipping Label</h1>';
    $list_obj->prepare_items();

    ?>
    <form method="get">

         <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

         <?php

         $list_obj->display();

         ?>

    </form>
    <?php

}
