<?php
/**
 * This will create the required table in the Database on hook activation
 *
 * @package marketplace-badge.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if direct access then exit.
}

if ( ! class_exists( 'MPBadgesInstall' ) ) :
	/**
	 * Class MPBadgesInstall
	 */
	class MPBadgesInstall {

		/**
		 * Classs constructor.
		 */
		public function __construct() {
			add_action( 'my_table', array( $this, 'mp_badges_install' ) );
			do_action( 'my_table' );
		}

		/**
		 * On install.
		 */
		public function mp_badges_install() {
			$this->create_plugin_database_table();
		}

		/**
		 * Creates table.
		 */
		public function create_plugin_database_table() {
			global $wpdb, $mpbadgetables;
			$wpdb->hide_errors();
			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) ) {
					$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
				}
				if ( ! empty( $wpdb->collate ) ) {
					$collate .= " COLLATE $wpdb->collate";
				}
			}
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$mpbadgetables = "
			CREATE TABLE `{$wpdb->prefix}mpbadge` (
				id  int(11)  NOT NULL auto_increment,
				b_name  VARCHAR(100)   NOT NULL,
				b_des  VARCHAR(200)   NOT NULL,
				rank  int(128)   NOT NULL,
				tumbnail  VARCHAR(500)   NOT NULL,
				status	int(2) NOT NUll,
				PRIMARY KEY (id)
				) $collate ;
				CREATE TABLE `{$wpdb->prefix}mpbadge_assign` (
					id  int(11)  NOT NULL auto_increment,
					b_id bigint(20) NOT NULL,
					u_id bigint(20) NOT NULL,
					PRIMARY KEY  (id)
					) $collate;
					";
					dbDelta( $mpbadgetables );
		}
	}
	endif;
	return new MPBadgesInstall();
