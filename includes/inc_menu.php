<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

function IQ_RPW_pre_create_menu() {
	$notification_count = func_iq_rpw_get_count_requests(0);
	IQ_RPW_create_menu($notification_count);
}
function IQ_RPW_create_menu($notification_count) {
	add_menu_page(
		'IQ Referral',
		$notification_count ? sprintf('%s <span class="awaiting-mod">%d</span>', 'IQ Referral', $notification_count) : 'IQ Referral',
		'manage_options',
		IQ_RPW_MENU_TAG.'index',
		'IQ_RPW_menu_core_callback',
		'dashicons-chart-pie',
		50 );

	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Settings', 'iq-referral-program-for-woocommerce'),
		esc_html__('Settings', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'settings',
		'IQ_RPW_menu_settings_callback',
		1
	);

	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Logs', 'iq-referral-program-for-woocommerce'),
		esc_html__('Logs', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'logs',
		'IQ_RPW_menu_logs_callback',
		2
	);

	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Action logs', 'iq-referral-program-for-woocommerce'),
		esc_html__('Action logs', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'action-logs',
		'IQ_RPW_menu_action_logs_callback',
		3
	);
	
	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Requests', 'iq-referral-program-for-woocommerce'),
		$notification_count ? sprintf('%s <span class="awaiting-mod">%d</span>', esc_html__('Requests', 'iq-referral-program-for-woocommerce'), $notification_count) : esc_html__('Requests', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'requests',
		'IQ_RPW_menu_requests_callback',
		4
	);

	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Pyramid', 'iq-referral-program-for-woocommerce'),
		esc_html__('Pyramid', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'pyramid',
		'IQ_RPW_menu_pyramid_callback',
		5
	);

	// WIZARD
	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Wizard', 'iq-referral-program-for-woocommerce'),
		esc_html__('Wizard', 'iq-referral-program-for-woocommerce'),
		'manage_options',
		IQ_RPW_MENU_TAG.'wizard',
		'IQ_RPW_menu_wizard_callback',
		6
	);

	add_submenu_page(
		IQ_RPW_MENU_TAG.'index',
		esc_html__('Plugins', 'iq-referral-program-for-woocommerce'),
		'<span style="color:#81bfdd;"><span class="dashicons dashicons-image-filter" style="font-size: 17px"></span> ' . __('Plugins', 'iq-referral-program-for-woocommerce') . '</span>',
		'manage_options',
		IQ_RPW_MENU_TAG.'other-plugins',
		'IQ_RPW_go_plugins_redirect'
	);

	if(!func_iq_rpw_module_enable('pyramid')) {
		add_submenu_page(
			IQ_RPW_MENU_TAG.'index',
			'',
			'<span style="color:#f5a30d;"><span class="dashicons dashicons-star-filled" style="font-size: 17px"></span> Go Pro</span>',
			'manage_options',
			IQ_RPW_MENU_TAG.'pro',
			'IQ_RPW_go_pro_redirect'
		);
	} else {
		add_submenu_page(
			IQ_RPW_MENU_TAG.'index',
			esc_html__('Support', 'iq-referral-program-for-woocommerce'),
			'<span style="color:#81bfdd;"><i class="icofont-life-buoy" style="font-size: 18px"></i> ' . __('Support', 'iq-referral-program-for-woocommerce') . '</span>',
			'manage_options',
			IQ_RPW_MENU_TAG.'support',
			'IQ_RPW_go_support_redirect'
		);
		add_submenu_page(
			IQ_RPW_MENU_TAG.'index',
			esc_html__('License', 'iq-referral-program-for-woocommerce'),
			'<span style="color:#ddd716;"><i class="icofont-key" style="font-size: 18px"></i> ' . __('License', 'iq-referral-program-for-woocommerce') . '</span>',
			'manage_options',
			IQ_RPW_MENU_TAG.'license',
			'IQ_RPW_go_license_redirect'
		);
	}
}
function IQ_RPW_menu_core_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_users_list.php';
}
function IQ_RPW_menu_settings_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_settings.php';
}
function IQ_RPW_menu_logs_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_logs.php';
}
function IQ_RPW_menu_action_logs_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_action_logs.php';
}
function IQ_RPW_menu_pyramid_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_pyramid.php';
}
function IQ_RPW_menu_requests_callback() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_requests.php';
}
function IQ_RPW_go_plugins_redirect() {
	if ( isset( $_GET['page'] ) && IQ_RPW_MENU_TAG.'other-plugins' === sanitize_text_field($_GET['page']) ) {
		wp_redirect( 'https://lumpx.com/wp-plugins' );
		die;
	}
}
function IQ_RPW_go_support_redirect() {
	if ( isset( $_GET['page'] ) && IQ_RPW_MENU_TAG.'support' === sanitize_text_field($_GET['page']) ) {
		wp_redirect( 'https://lumpx.com/en/support/open' );
		die;
	}
}
function IQ_RPW_go_license_redirect() {
	if ( isset( $_GET['page'] ) && IQ_RPW_MENU_TAG.'license' === sanitize_text_field($_GET['page']) ) {
		wp_redirect( 'https://lumpx.com/en/wp-plugins/license' );
		die;
	}
}

function IQ_RPW_license_redirect() {
	if ( isset( $_GET['page'] ) && IQ_RPW_MENU_TAG.'license' === sanitize_text_field($_GET['page']) ) {
		wp_redirect( 'https://lumpx.com/wp-plugins/license' );
		die;
	}
}
function IQ_RPW_go_pro_redirect() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_window_pro.php';
}

/*
###################
###### WIZARD #####
###################
*/
function IQ_RPW_menu_wizard_callback() {
	include IQ_RPW_CORE_DIR . '/wizard/iq_referral_wizard.php';
}
function IQ_RPW_wp_admin_submenu_filter( $submenu_file ) {
    global $plugin_page;
    $hidden_submenus = array(
        IQ_RPW_MENU_TAG.'wizard' => true,
    );
    if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
        $submenu_file = 'submenu_to_highlight';
    }
    foreach ( $hidden_submenus as $submenu => $unused ) {
        remove_submenu_page( IQ_RPW_MENU_TAG.'index', $submenu );
    }
    return $submenu_file;
}
add_filter( 'submenu_file', 'IQ_RPW_wp_admin_submenu_filter' );