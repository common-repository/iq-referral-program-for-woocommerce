<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

define('IQ_RPW_WIZARD_ACCESS', true);
require_once IQ_RPW_CORE_DIR.'/wizard/includes/iq_rpw_wizard_func.php';

if(!is_plugin_active('woocommerce/woocommerce.php')) {
	$szMsg = esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
##################
##### ACCESS #####
##################
if ( !is_user_logged_in() ) {
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

$cur_user_id = get_current_user_id();
if($cur_user_id <= 0) {
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$user_obj = get_userdata( $cur_user_id );

if(!$user_obj) {
	wp_logout();
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$roles_arr = (array)$user_obj->roles;

$need_role = 'administrator';
if(!in_array($need_role, $roles_arr)) {
	$szMsg = esc_html__('Access denied', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

$cWizardClass = new WizardClass();
$bResult = $cWizardClass->updateUsers();
if(!$bResult) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

$szMsg = esc_html__('Users successfully imported', 'iq-referral-program-for-woocommerce');
?>
<div id="success">
	<?php echo esc_html($szMsg); ?>
</div>