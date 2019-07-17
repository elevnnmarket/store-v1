<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Email_Template_Table extends WP_List_Table {

	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'singular_form',
				'plural'   => 'plural_form',
				'ajax'     => false,
			)
		);
		add_action( 'admin_head', array( $this, 'admin_header' ) );

	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'ID':
			case 'first_column_name':
			case 'second_column_name':
				return '<strong>' . $item[ $column_name ] . '</strong>';
			default:
				return print_r( $item, true );

		}
	}

	public function get_sortable_columns() {

		$sortable_columns = array(
			'ID'                => array( 'ID', true ),

			'first_column_name' => array( 'first_column_name', true ),
		);

		return $sortable_columns;
	}

	public function get_hidden_columns() {
		// Setup Hidden columns and return them.
		return array();
	}


	public function column_ID( $item ) {

		$actions = array(

			'edit'  => sprintf( '<a href="?page=class-email-templates&action=add&user=%s">Edit</a>', $item['ID'] ),

			'trash' => sprintf( '<a href="?page=class-email-templates&action=trash&user=%s">Trash</a>', $item['ID'] ),

		);

		return sprintf( '%1$s %2$s', $item['ID'], $this->row_actions( $actions ) );

	}

	public function table_data() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'emailTemplate';

		$data = array();

		if ( isset( $_POST['s'] ) ) {
			$p_search = $_POST['s'];
			$query    = "SELECT * from {$wpdb->prefix}emailTemplate  where title like '%$p_search%' and status = 'publish'";
		} else {
			$query = "SELECT * from {$wpdb->prefix}emailTemplate WHERE status='publish' ";
			$ab    = $query;
		}

		$idd = array();

		$field_name_one = array();

		$field_name_two = array();

		$i = 0;

		$id = $wpdb->get_results( $query );

		foreach ( $id as $ids ) {

			$idd[] = $ids->id;

			$field_name_one[] = $ids->title;

			$data[] = array(

				'ID'                 => $idd[ $i ],

				'first_column_name'  => $field_name_one[ $i ],

				'second_column_name' => '<a href="' . esc_url( admin_url( 'admin.php?page=class-email-templates&preview_marketplace_mail=true&id=' . $idd[ $i ] ) ) . '" target="_blank">Preview</a>',

			);

			$i++;
		}

		return $data;

	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="user[]" value="%s" />', $item['ID'] );
	}


	public function admin_header() {

		$curr_page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

		echo '<style type="text/css">';
		echo '.wp-list-table .ID { width: 10%; }';
		echo '.wp-list-table .first_column_name { width: 45%; }';
		echo '</style>';
	}

	public function get_columns() {
		$columns = array(
			'cb'                 => '<input type="checkbox" />',
			'ID'                 => __( 'ID', 'marketplace' ),
			'first_column_name'  => __( 'Title', 'marketplace' ),
			'second_column_name' => __( 'Preview', 'marketplace' ),
		);

		return $columns;
	}

	public function get_bulk_actions() {
		$actions = array(
			'trash'  => 'Trash',
			'delete' => 'Delete',
		);
		return $actions;
	}

	public function process_bulk_action() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'emailTemplate';

		if ( isset( $_GET['action'] ) ) {

			if ( $_GET['action'] == 'delete' ) {

				$temp_single_data = $_GET['user'];

				if ( is_array( $temp_single_data ) ) {

					foreach ( $temp_single_data as $single_data ) {

						$wpdb->delete( $table_name, array(
							'id' => $single_data,
						) );

					}
				} else {

					$wpdb->delete( $table_name, array(
						'id' => $single_data,
					) );
				}
			}

			if ( $_GET['action'] == 'trash' ) {

				$temp_single_data = $_GET['user'];

				if ( is_array( $temp_single_data ) ) {

					foreach ( $temp_single_data as $single_data ) {

						$wpdb->update( $table_name, array( 'status' => 'trash' ), array( 'id' => $single_data ), array( '%s' ), array( '%d' ) );

					}
				} else {

					$wpdb->update( $table_name, array( 'status' => 'trash' ), array( 'id' => $temp_single_data ), array( '%s' ), array( '%d' ) );
				}
			}
		}
	}

	public function prepare_items() {

		global $wpdb;

		$columns = $this->get_columns();

		$sortable = $this->get_sortable_columns();

		$hidden = $this->get_hidden_columns();

		$this->process_bulk_action();

		$data = $this->table_data();

		$totalitems = count( $data );

		$user = get_current_user_id();

		$screen = get_current_screen();

		$perpage = $this->get_items_per_page( 'template_per_page', 20 );

		$this->_column_headers = array( $columns, $hidden, $sortable );

		if ( empty( $perpage ) || $perpage < 1 ) {

			$perpage = $screen->get_option( 'per_page', 'default' );

		}

		usort( $data, array( $this, 'usort_reorder' ) );

		$totalpages = ceil( $totalitems / $perpage );

		$currentPage = $this->get_pagenum();

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perpage ), $perpage );

		$this->set_pagination_args( array(

			'total_items' => $totalitems,

			'total_pages' => $totalpages,

			'per_page'    => $perpage,
		) );

		$this->items = $data;

	}
}
echo '<div class="wrap">';
echo '<h1 class="wp-heading-inline">Email Templates</h1>';
echo '<a href="?page=class-email-templates&action=add" class="page-title-action">Add New</a>';

$EmailTemplates = new Email_Template_Table();

$EmailTemplates->prepare_items();

?>
<form method="GET">

	<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />

	<?php

	$EmailTemplates->display();
	?>
</form>
<?php
echo '</div>';
