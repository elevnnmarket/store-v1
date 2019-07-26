<?php

/**
 * Get additional system & plugin specific information for feedback
 *
 */
if ( ! function_exists( 'ig_get_additional_info' ) ) {

	/**
	 * Get TLWP specific information
	 *
	 * @param $additional_info
	 * @param bool $system_info
	 *
	 * @return mixed
	 *
	 * @since 1.5.17
	 */
	function ig_get_additional_info( $additional_info, $system_info = false ) {
		global $icegram, $ig_tracker;
		$additional_info['version'] = $icegram->version;
		if ( $system_info ) {

			$additional_info['active_plugins']   = implode( ', ', $ig_tracker::get_active_plugins() );
			$additional_info['inactive_plugins'] = implode( ', ', $ig_tracker::get_inactive_plugins() );
			$additional_info['current_theme']    = $ig_tracker::get_current_theme_info();
			$additional_info['wp_info']          = $ig_tracker::get_wp_info();
			$additional_info['server_info']      = $ig_tracker::get_server_info();

			// IG Specific information
			$additional_info['plugin_meta_info'] = Icegram::get_ig_meta_info();
		}
		return $additional_info;

	}

}

add_filter( 'ig_additional_feedback_meta_info', 'ig_get_additional_info', 10, 2 );
