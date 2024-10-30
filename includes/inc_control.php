<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
if(is_plugin_active(IQ_RPW_PLUGIN_DIR.'/'.IQ_RPW_PLUGIN_DIR.'.php')) {
	if(!isset($cIQ_RPW_ReferralClass)) {
		require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
		$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
	}
	$cur_user_id = get_current_user_id();
	$partner_info = $cIQ_RPW_ReferralClass->getInfo($cur_user_id);
	if($partner_info && (int)$partner_info['ref_enable']) {
		require_once IQ_RPW_CORE_DIR.'/includes/inc_func.php';
		add_filter ( 'woocommerce_account_menu_items', 'func_iq_rpw_account_menu_items', 40 );
		add_action( 'woocommerce_account_iq-referral_endpoint', 'func_iq_rpw_woocommerce_account_partner' );
		add_rewrite_endpoint( 'iq-referral', EP_PAGES );
		flush_rewrite_rules();
	}
}