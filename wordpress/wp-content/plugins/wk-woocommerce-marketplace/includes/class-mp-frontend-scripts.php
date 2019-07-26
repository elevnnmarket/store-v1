<?php
/**
 * Handle frontend forms
 *
 * @class MP_Frontend_Scripts
 * @version 4.7.1
 * @package Marketplace/Classes/
 * @category Class
 * @author webkul
 */
class MP_Frontend_Scripts {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_marketplace_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_jquery_libraries' ) );
	}

	public function register_marketplace_styles() {
		wp_register_style( 'marketplace-style', WK_MARKETPLACE . 'style.css', '', MP_SCRIPT_VERSION );
		wp_register_style( 'datatable-css', WK_MARKETPLACE . 'assets/css/datatable.css' );

		wp_enqueue_style( 'marketplace-style' );
		wp_enqueue_style( 'datatable-css' );
	}

	public function add_jquery_libraries() {
		wp_register_script( 'jquery', 'http://code.jquery.com/jquery-2.2.4.min.js' );
		wp_enqueue_script( 'jquery' );

		wp_register_style( 'res-jquery1', 'https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css' );
		wp_enqueue_style( 'res-jquery1' );

		wp_register_script( 'pagination-js', WK_MARKETPLACE . 'assets/js/pagination_datatable.js' );
		wp_enqueue_script( 'pagination-js' );

		$paging_arr = array(
			'pag1' => __( 'Are you sure you want to delete selected product(s) ?', 'marketplace' ),
			'pag2' => __( 'Deleted Successfully.', 'marketplace' ),
			'pag3' => __( 'Please select product(s) first !', 'marketplace' ),
		);

		wp_localize_script( 'pagination-js', 'paginationScript',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ajaxnonce' ),
				'page_tr' => $paging_arr,
			)
		);

		wp_register_script( 'datatable-cdn', WK_MARKETPLACE . 'assets/js/datatable-cdn-js.js', '', '2.0' );

		wp_enqueue_script( 'datatable-cdn' );

		$datatab_arr = array(
			'trans1'  => __( 'activate to sort column ascending', 'marketplace' ),
			'trans2'  => __( 'activate to sort column descending', 'marketplace' ),
			'trans3'  => __( 'First', 'marketplace' ),
			'trans4'  => __( 'Last', 'marketplace' ),
			'trans5'  => __( 'Next', 'marketplace' ),
			'trans6'  => __( 'Previous', 'marketplace' ),
			'trans7'  => __( 'No data available in table', 'marketplace' ),
			'trans8'  => __( 'Show', 'marketplace' ),
			'trans9'  => __( 'entries', 'marketplace' ),
			'trans10' => __( 'Search:', 'marketplace' ),
			'trans11' => __( 'No matching records found', 'marketplace' ),
			'trans12' => __( 'Loading...', 'marketplace' ),
			'trans13' => __( 'Processing...', 'marketplace' ),
			'trans14' => __( 'filtered from', 'marketplace' ),
			'trans15' => __( 'total entries', 'marketplace' ),
			'trans16' => __( 'Showing', 'marketplace' ),
			'trans17' => __( 'to', 'marketplace' ),
			'trans18' => __( 'of', 'marketplace' ),
			'trans19' => __( 'entries', 'marketplace' ),
		);

		wp_localize_script( 'datatable-cdn', 'datatab_tr', $datatab_arr );

		wp_register_script( 'easy-js', WK_MARKETPLACE . 'assets/js/easying.js' );
		wp_enqueue_script( 'easy-js' );

		wp_register_script( 'bsxlider', WK_MARKETPLACE . 'assets/js/jquery.bxslider.min.js' );
		wp_enqueue_script( 'bsxlider' );

		if ( null !== get_query_var( 'main_page' ) && get_query_var( 'main_page' ) == 'dashboard' ) {
			wp_enqueue_script( 'google_chart', '//www.google.com/jsapi' );

			wp_enqueue_script( 'mp_chart_script', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js' );

			wp_register_script( 'mp-chart-js', WK_MARKETPLACE . '/assets/js/chart_script.js' );

			wp_enqueue_script( 'mp-chart-js' );

			wp_localize_script( 'mp-chart-js',
				'mp_chart_js',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'ajaxnonce' ),
				)
			);
		}
	}
}
new MP_Frontend_Scripts();
