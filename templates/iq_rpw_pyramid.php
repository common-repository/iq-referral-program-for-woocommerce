<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}
if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

if(func_iq_rpw_module_enable('pyramid')) {
	include IQ_RPW_PYRAMID_DIR.'/templates/pyramid_adm_index.php';
} else {
	include dirname( __FILE__ ).'/iq_rpw_window_pro.php';
}
?>