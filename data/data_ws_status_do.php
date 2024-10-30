<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Values'])) {
	die();
}

if(!isset($_POST['JsonData']) ||
!isset($_POST['SignData'])) {
	die();
}

if(!is_plugin_active('woocommerce/woocommerce.php')) {
	$szMsg = esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// Protect
$JsonProtect = sanitize_text_field($_POST['JsonData']);
$SignCheck = func_iq_rpw_get_sign_json_array_protect($JsonProtect);
$szPostSign = sanitize_text_field($_POST['SignData']);
if($szPostSign != $SignCheck) { die(); }
$JsonVal = func_iq_rpw_out_protect($JsonProtect, IQ_RPW_SECRET_CODE);

########################
##### DATA PROTECT #####
########################
$CArr = json_decode($JsonVal, true);
if(empty($CArr)) {
	return false;
}

// item_id
if(!isset($CArr['item_id'])) {
	die();
}
$CArr['item_id'] = (int)$CArr['item_id'];

// pop
if(!isset($CArr['pop'])) {
	die();
}
$CArr['pop'] = (int)$CArr['pop'];

###############
##### POP #####
###############
$bPop = 0;
if(isset($_POST['Pop'])) {
	$bPop = (int)sanitize_text_field($_POST['Pop']);
}
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
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

#######################
##### DATA VALUES #####
#######################
$DataValuesArr = array();
$DataJsonStrArr = array();
$PostValues = sanitize_text_field($_POST['Values']);
$Exp = explode(',', $PostValues);
$ArrayData = array();
for($i = 0; $i < count($Exp); $i++) {
	$TempParam = trim($Exp[$i]);
	if(empty($TempParam)) { continue; }
	$ArrayData[] = $TempParam;
}

if(empty($ArrayData)) {
	die();
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}

$status_arr = $cIQ_RPW_ReferralClass->getWithdrawStatus();
if(!$status_arr) {
	$szMsg = esc_html__('Available statuses not found', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// status
$Key = $szPreTag.'status';
if(!in_array($Key, $ArrayData) || !isset($_POST[$Key])) {
	$szMsg = esc_html__('Incorrect status', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$DataValuesArr['status'] = (int)sanitize_text_field($_POST[$Key]);
if(!array_key_exists($DataValuesArr['status'], $status_arr)) {
	$szMsg = esc_html__('Incorrect status', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

global $wpdb;
$sql = "
	UPDATE
		`{$wpdb->prefix}iq_rpw_withdraw_forms`
	SET
		`status` = %d
	WHERE
		`id` = %d;
";
$result = $wpdb->query($wpdb->prepare($sql,
					$DataValuesArr['status'],
					$CArr['item_id']));
					
if(!$result) {
	$szMsg = esc_html__('Failed to change status', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// Action logs
$log = sprintf(esc_html__('Admin changed withdrawal request status #%s to %s', 'iq-referral-program-for-woocommerce'), $CArr['item_id'], $status_arr[$DataValuesArr['status']]['name']);
$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);

?>
<div id="success">
	<?php echo esc_html__('Status changed successfully', 'iq-referral-program-for-woocommerce'); ?>
</div>
