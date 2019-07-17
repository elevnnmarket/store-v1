<?php

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

if( !class_exists( 'WP_List_Table' ) )
{
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'MP_Manage_Rma' ) )
{
    /**
     *
     */
    class MP_Manage_Rma extends WP_List_Table
    {

      function __construct()
      {
          parent::__construct( array(
              'singular'	=> 'RMA',
              'plural' 	=> 'RMAs',
              'ajax'   	=> false
          ) );
      }

      function prepare_items()
			{

    			global $wpdb;

      		$columns = $this->get_columns();

      		$sortable = $this->get_sortable_columns();

      		$hidden = $this->get_hidden_columns();

      		$this->process_bulk_action();

      		$data = $this->table_data();

      		$totalitems = count($data);

      		$user = get_current_user_id();

      		$screen = get_current_screen();

      		$perpage = $this->get_items_per_page('product_per_page', 20);

      		$this->_column_headers = array( $columns, $hidden, $sortable );

      		if ( empty ( $per_page) || $per_page < 1 )
					{

        			$per_page = $screen->get_option( 'per_page', 'default' );

      		}

      		function usort_reorder($a,$b)
					{

    					$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order_id'; //If no sort, default to title

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
    		   		'cb'         	  => '<input type="checkbox" />', //Render a checkbox instead of text
            	'order_id'			=> __('Order Id'),
              'cust_name'			=> __('Customer Name'),
              'products'			=> __('Products'),
              'reason'			  => __('Reason'),
							'rma_status'	=> __('RMA Status'),
              'delivery_status'	=> __('Delivery Status'),
    	      	'date'			    => __('Date'),
  		   	);
  		}

  		function column_default($item, $column_name)
			{

      		switch( $column_name )
					{
        			case 'order_id':
              case 'cust_name':
              case 'products':
              case 'reason':
              case 'rma_status':
							case 'delivery_status';
        			case 'date':
          				return $item[ $column_name ];
        			default:
          				return print_r($item, true);
      		}

    	}

  		/**
  		 * Decide which columns to activate the sorting functionality on
  		 * @return array $sortable, the array of columns that can be sorted by the user
  		 */
  		public function get_sortable_columns()
			{

  		   	return $sortable = array(
							'order_id' => array( 'order_id', true )
  		   	);

  		}

  		public function get_hidden_columns()
			{
  			   return array();
  		}

  		function column_cb($item)
      {
  			   return sprintf('<input type="checkbox" id="rma_%s" name="rma[]" value="%s" />',$item['id'], $item['id']);
  		}

  		private function table_data()
      {

  	    	global $wpdb;

  	    	$data = array();

          $table_name = $wpdb->prefix.'mp_rma_requests';

          if ( isset( $_POST['s'] ) )
          {
              $string = $_POST['s'];
              $wk_posts = $wpdb->get_results("Select * from $table_name where reason like '%$string%'");
          }
          else
          {
  	    	    $wk_posts = $wpdb->get_results("Select * from $table_name");
          }

  	    	$i = 0;

          $order_id = array();
          $cust_name = array();
          $products = array();
          $reason = array();
					$rma_status = array();
          $delivery_status = array();
          $date = array();

  	    	foreach ( $wk_posts as $key => $value )
          {
						$product = '';
						$reasons = '';
            $id[] = $value->id;
            $order_id[] = $value->order_no;
            $cust_name[] = get_userdata($value->customer_id)->display_name;
            foreach (maybe_unserialize($value->items)['items'] as $val) {
                $product .= get_the_title($val).'<br>';
            }
            $products[] = $product;
            foreach (maybe_unserialize($value->items)['reason'] as $reason_id) {
              $wk_post = $wpdb->get_results("Select reason from {$wpdb->prefix}mp_rma_reasons where id = '$reason_id'", ARRAY_A);
              $reasons .= $wk_post[0]['reason'].'<br>';
            }
            $reason[] = $reasons;
						$rma_status[] = $value->rma_status;
            $delivery_status[] = $value->order_status;
            $date[] = $value->datetime;
  					$data[] = array(
                'id'  						=> $id[$i],
                'order_id'  			=> $order_id[$i],
                'cust_name' 			=> $cust_name[$i],
                'products'  			=> $products[$i],
                'reason'    			=> $reason[$i],
                'rma_status' 			=> '<strong class="wk_rma_status_'.$rma_status[$i].'">'.ucfirst($rma_status[$i]).'</strong>',
								'delivery_status'	=> ucfirst($delivery_status[$i]),
                'date'         		=> $date[$i]
  					);

  					$i++;

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
						global $wpdb;

						$table = $wpdb->prefix.'wk_rma_requests';

						if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
								$nonce  = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
					      $action = 'bulk-' . $this->_args['plural'];
					      if ( ! wp_verify_nonce( $nonce, $action ) )
					          wp_die( 'Nope! Security check failed!' );
						}

						if ( $this->current_action() == 'delete' )
						{

								if ( is_array($_GET['rid']))
								{
										foreach ($_GET['rid'] as $key => $value) {
												$wpdb->delete( $table, array( 'id' => $value ) );
										}
								}
								else
								{
										$wpdb->delete( $table, array( 'id' => $_GET['reason'] ) );
								}

						}

				}

  			function column_order_id($item)
        {

        		$actions = array(

      					'view'     => sprintf('<a href="admin.php?page=marketplace-rma&rid=%s&action=view">View</a>', $item['id']),

      					'delete'   => sprintf('<a href="admin.php?page=marketplace-rma&action=delete&rid=%s&_wpnonce=%s" class="delete-rma">Delete</a>',$item['id'], wp_create_nonce('bulk-rmas') )

        		);

        		return sprintf( '%1$s %2$s', $item['order_id'], $this->row_actions($actions) );

    		}

    }

}
