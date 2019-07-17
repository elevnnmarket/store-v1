<?php
/*
Plugin Name: WordPress Multisite User Sync/Unsync
Description: WordPress Multisite User Sync/Unsync plugin can sync/unsync users from one site (blog) to the other sites (blogs) in your WordPress Multisite network.
Version:     1.3.0
Author:      Obtain Infotech
Author URI:  https://www.obtaininfotech.com/
License:     GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that call admin side css and js.
 */
if ( ! function_exists( 'wmus_admin_include_css_and_js' ) ) {
    add_action( 'admin_enqueue_scripts', 'wmus_admin_include_css_and_js' );
    function wmus_admin_include_css_and_js() {
        
        /* admin style */
        wp_register_style( 'wmus-style', plugin_dir_url( __FILE__ ) . 'assets/css/wmus-style.css', false, '1.0.0' );
        wp_enqueue_style( 'wmus-style' );
        
        /* admin script */
        wp_register_script( 'wmus-script', plugin_dir_url( __FILE__ ) . 'assets/js/wmus-script.js', array( 'jquery' ) );
        wp_enqueue_script( 'wmus-script' );
    }
}

/*
 * This is a function that add custom cron schedule.
 */
if ( ! function_exists( 'wmus_cron_schedules' ) ) {
    add_filter( 'cron_schedules', 'wmus_cron_schedules' );
    function wmus_cron_schedules( $schedules ) {
        $schedules['wmus_one_minute'] = array(
            'interval' => 60,
            'display'  => esc_html__( 'Every one minute' ),
        );

        return $schedules;
    }
}

/*
 * This is a function that run when plugin activate plugin and it's set one minute custom cron.
 */
if ( ! function_exists( 'wmus_register_activation_hook' ) ) {
    register_activation_hook( __FILE__, 'wmus_register_activation_hook' );
    function wmus_register_activation_hook() {
        
        if (! wp_next_scheduled ( 'wmus_one_minute_event' )) {
            wp_schedule_event(time(), 'wmus_one_minute', 'wmus_one_minute_event' );
        }
    }
}

/*
 * This is a function that run when deactivate plugin and it's clear one minute custom cron.
 */
if ( ! function_exists( 'wpmu_register_deactivation_hook' ) ) {
    register_deactivation_hook( __FILE__, 'wpmu_register_deactivation_hook' );
    function wpmu_register_deactivation_hook() {
        
	wp_clear_scheduled_hook('wmus_one_minute_event');
    }
}

/*
 * This is a function file for network settings.
 * Add network admin menu
 * Add network pages
 */
require  plugin_dir_path( __FILE__ ) . 'includes/wmus-network.php';

/*
 * This is a file for sync/unsync functions.
 */
require  plugin_dir_path( __FILE__ ) . 'includes/wmus-sync-unsync.php';